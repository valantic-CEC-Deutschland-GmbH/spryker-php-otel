<?php

namespace Pyz\Yves\Application;

use Pyz\Service\OpenTelemetry\OpenTelemetryServiceInterface;
use Pyz\Yves\Application\ApplicationDependencyProvider;
use Spryker\Yves\Application\ApplicationFactory as SprykerApplicationFactory;

class ApplicationFactory extends SprykerApplicationFactory
{
    public function getOpenTelemetryService(): OpenTelemetryServiceInterface
    {
        return $this->getProvidedDependency(ApplicationDependencyProvider::SERVICE_OPENTELEMETRY);
    }
}
