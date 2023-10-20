<?php

namespace Pyz\Service\OpenTelemetry;

use Pyz\Service\OpenTelemetry\Initializer\OpenTelemetryInitializer;
use Pyz\Service\OpenTelemetry\Initializer\OpenTelemetryInitializerInterface;
use Spryker\Service\Kernel\AbstractServiceFactory;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\AbstractFactory;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Kernel\Dependency\Injector\DependencyInjector;

class OpenTelemetryServiceFactory extends AbstractServiceFactory
{
    public function createOpenTelementryInitializer(): OpenTelemetryInitializerInterface {
        return new OpenTelemetryInitializer();
    }
}
