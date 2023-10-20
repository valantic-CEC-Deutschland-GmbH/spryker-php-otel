<?php

namespace Pyz\Zed\Application\Communication;

use Pyz\Service\OpenTelemetry\OpenTelemetryServiceInterface;
use Pyz\Zed\Application\ApplicationDependencyProvider;
use Spryker\Zed\Application\Communication\ApplicationCommunicationFactory as SprykerApplicationCommunicationFactory;

class ApplicationCommunicationFactory extends SprykerApplicationCommunicationFactory
{

    public function getOpenTelemetryService(): OpenTelemetryServiceInterface
    {
        return $this->getProvidedDependency(ApplicationDependencyProvider::SERVICE_OPENTELEMETRY);
    }
}
