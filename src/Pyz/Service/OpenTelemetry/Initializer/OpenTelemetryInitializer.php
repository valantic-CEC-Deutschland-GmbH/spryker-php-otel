<?php

namespace Pyz\Service\OpenTelemetry\Initializer;

use OpenTelemetry\API\Instrumentation\Configurator;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\Contrib\Otlp\SpanExporterFactory;
use OpenTelemetry\SDK\Common\Time\ClockFactory;
use OpenTelemetry\SDK\Common\Util\ShutdownHandler;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProviderBuilder;

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
    }
}
