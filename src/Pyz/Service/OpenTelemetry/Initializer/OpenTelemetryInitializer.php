<?php

namespace Pyz\Service\OpenTelemetry\Initializer;

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Instrumentation\CachedInstrumentation;
use OpenTelemetry\API\Instrumentation\Configurator;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\Context\Context;
use OpenTelemetry\Context\Propagation\ArrayAccessGetterSetter;
use OpenTelemetry\Contrib\Otlp\SpanExporterFactory;
use OpenTelemetry\SDK\Common\Time\ClockFactory;
use OpenTelemetry\SDK\Common\Util\ShutdownHandler;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProviderBuilder;
use OpenTelemetry\SemConv\TraceAttributes;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function OpenTelemetry\Instrumentation\hook;

class OpenTelemetryInitializer implements OpenTelemetryInitializerInterface
{

    public function initialize(): void {

        \OpenTelemetry\API\Globals::registerInitializer(function (Configurator $configurator) {
            $propagator = TraceContextPropagator::getInstance();
            $spanProcessor = new BatchSpanProcessor(
                (new SpanExporterFactory())->create(),
                (new ClockFactory())->build()
            );
            $tracerProvider = (new TracerProviderBuilder())
                ->addSpanProcessor($spanProcessor)
                ->setSampler(new ParentBased(new AlwaysOnSampler()))
                ->build();

            ShutdownHandler::register([$tracerProvider, 'shutdown']);

            return $configurator
                ->withTracerProvider($tracerProvider)
                ->withPropagator($propagator);
        });

        $this->register();
    }


    public static function register(): void
    {
        $instrumentation = new CachedInstrumentation('io.opentelemetry.contrib.php.symfony_http');

        hook(
            \Spryker\Shared\ZedRequest\Client\AbstractZedClient::class,
            'call',
            pre: static function (
                \Spryker\Shared\ZedRequest\Client\AbstractZedClient $client,
                array $params,
                string $class,
                string $function,
                ?string $filename,
                ?int $lineno,
            ) use ($instrumentation): array {
                /** @psalm-suppress ArgumentTypeCoercion */
                $builder = $instrumentation
                    ->tracer()
                    ->spanBuilder(\sprintf('HTTP %s', $params[0]))
                    ->setSpanKind(SpanKind::KIND_CLIENT)
                    ->setAttribute(TraceAttributes::URL_FULL, (string) $params[0])
                    ->setAttribute(TraceAttributes::CODE_FUNCTION, $function)
                    ->setAttribute(TraceAttributes::CODE_NAMESPACE, $class)
                    ->setAttribute(TraceAttributes::CODE_FILEPATH, $filename)
                    ->setAttribute(TraceAttributes::CODE_LINENO, $lineno);

                $propagator = Globals::propagator();
                $parent = Context::getCurrent();

                $span = $builder
                    ->setParent($parent)
                    ->startSpan();

                $requestOptions = $params[2] ?? [];

                if (!isset($requestOptions['headers'])) {
                    $requestOptions['headers'] = [];
                }

                $previousOnProgress = $requestOptions['on_progress'] ?? null;

                //As Response are lazy we end span when status code was received
                $requestOptions['on_progress'] = static function (int $dlNow, int $dlSize, array $info) use (
                    $previousOnProgress,
                    $span
                ): void {
                    if (null !== $previousOnProgress) {
                        $previousOnProgress($dlNow, $dlSize, $info);
                    }

                    $statusCode = $info['http_code'];

                    if (0 !== $statusCode && null !== $statusCode && $span->isRecording()) {
                        $span->setAttribute(TraceAttributes::HTTP_RESPONSE_STATUS_CODE, $statusCode);

                        if ($statusCode >= 400 && $statusCode < 600) {
                            $span->setAttribute(TraceAttributes::HTTP_RESPONSE_STATUS_CODE, $statusCode);
                            $span->setStatus(StatusCode::STATUS_ERROR);
                        }

                        $span->end();
                    }
                };

                $context = $span->storeInContext($parent);
                $propagator->inject($requestOptions['headers'], ArrayAccessGetterSetter::getInstance(), $context);

                Context::storage()->attach($context);
                $params[2] = $requestOptions;

                return $params;
            },
            post: static function (
                \Spryker\Shared\ZedRequest\Client\AbstractZedClient $client,
                array $params,
                TransferInterface $response,
                ?\Throwable $exception
            ): void {
                $scope = Context::storage()->scope();
                if (null === $scope) {
                    return;
                }
                $scope->detach();
                $span = Span::fromContext($scope->context());

                if (null !== $exception) {
                    $span->recordException($exception, [
                        TraceAttributes::EXCEPTION_ESCAPED => true,
                    ]);
                    $span->setStatus(StatusCode::STATUS_ERROR, $exception->getMessage());
                    $span->end();
                }

                //As Response are lazy we end span after response is received,
                //it's added in on_progress callback, see line 63
            },
        );
    }
}
