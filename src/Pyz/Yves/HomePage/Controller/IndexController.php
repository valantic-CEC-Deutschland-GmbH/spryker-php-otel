<?php
declare(strict_types=1);

namespace Pyz\Yves\HomePage\Controller;

use OpenTelemetry\API\Metrics\ObserverInterface;
use OpenTelemetry\Contrib\Otlp\MetricExporterFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporterFactory;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use Spryker\Shared\Log\LoggerTrait;
use SprykerShop\Yves\HomePage\Controller\IndexController as SprykerIndexController;

class IndexController extends SprykerIndexController
{
    use LoggerTrait;

    public function indexAction()
    {
       // $this->startOTLP();

        //$this->demoOTLP();

        return parent::indexAction();
    }

    public function startOTLP() {
        $tracerProvider =  new TracerProvider(
            new SimpleSpanProcessor(
                (new SpanExporterFactory())->create()
            )
        );

        $tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

        //start a root span
        $rootSpan = $tracer->spanBuilder('YVES::Index')->startSpan();

        $rootScope = $rootSpan->activate();
    }

    private function demoOTLP(): void
    {
        $tracerProvider =  new TracerProvider(
            new SimpleSpanProcessor(
                (new SpanExporterFactory())->create()
            )
        );

        $tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

        //start a root span
        $rootSpan = $tracer->spanBuilder('Spryker')->startSpan();

        //future spans will be parented to the currently active span
        $rootScope = $rootSpan->activate();

        try {
            $span1 = $tracer->spanBuilder('is')->startSpan();
            $span1Scope = $span1->activate();

            try {
                $this->getLogger()->info("Start generating metrics");

                $span2 = $tracer->spanBuilder('great')->startSpan();
                echo 'OpenTelemetry welcomes Spryker' . PHP_EOL;
                $span2->end();

                $this->exportMetric();
                $this->getLogger()->info("Stop generating metrics");

            } finally {
                $span1Scope->detach();
                $span1->end();
            }
        } catch (\Throwable $t) {
            //The library's code shouldn't be throwing unhandled exceptions (it should emit any errors via diagnostic events)
            //This is intended to illustrate a way you can capture unhandled exceptions coming from your app code
            $rootSpan->recordException($t);
        } finally {
            //ensure span ends and scope is detached
            $rootScope->detach();
            $rootSpan->end();
        }
        $tracerProvider->shutdown();
    }

    private function exportMetric(): void
    {

        $reader = new ExportingReader(
            (new MetricExporterFactory())->create()
        );

        $meterProvider = MeterProvider::builder()
            ->setResource(ResourceInfoFactory::emptyResource())
            ->addReader($reader)
            ->build();

        $meterProvider
            ->getMeter('spryker_unknown')
            ->createObservableGauge('demo_meter', 'items', 'Random number')
            ->observe(static function (ObserverInterface $observer): void {
                $observer->observe(random_int(0, 256));
            });

        for ($x=0; $x<60; $x++) {
            $reader->collect();
//            sleep(1);
        }
    }

}
