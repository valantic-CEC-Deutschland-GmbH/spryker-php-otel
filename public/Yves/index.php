<?php

use OpenTelemetry\API\Instrumentation\Configurator;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\Contrib\Otlp\SpanExporterFactory;
use OpenTelemetry\SDK\Common\Util\ShutdownHandler;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProviderBuilder;
use Pyz\Yves\ShopApplication\YvesBootstrap;
use Spryker\Shared\Config\Application\Environment;
use Spryker\Shared\ErrorHandler\ErrorHandlerEnvironment;

require __DIR__ . '/maintenance/maintenance.php';

define('APPLICATION', 'YVES');
defined('APPLICATION_ROOT_DIR') || define('APPLICATION_ROOT_DIR', dirname(__DIR__, 2));

require_once APPLICATION_ROOT_DIR . '/vendor/autoload.php';

Environment::initialize();

OpenTelemetry\API\Globals::registerInitializer(function (Configurator $configurator) {
    $propagator = TraceContextPropagator::getInstance();
/*    $spanProcessor = new SimpleSpanProcessor(
        (new SpanExporterFactory())->create()
    );*/
    $spanProcessor = new BatchSpanProcessor(
        (new SpanExporterFactory())->create(),
        (new \OpenTelemetry\SDK\Common\Time\ClockFactory())->build()
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

$errorHandlerEnvironment = new ErrorHandlerEnvironment();
$errorHandlerEnvironment->initialize();

$bootstrap = new YvesBootstrap();
$bootstrap
    ->boot()
    ->run();
