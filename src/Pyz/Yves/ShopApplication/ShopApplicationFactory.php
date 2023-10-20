<?php

namespace Pyz\Yves\ShopApplication;

use Pyz\Service\OpenTelemetry\OpenTelemetryServiceInterface;
use Pyz\Yves\Application\ApplicationDependencyProvider;
use SprykerShop\Yves\ShopApplication\ShopApplicationFactory as SprykerShopApplicationFactory;

class ShopApplicationFactory extends SprykerShopApplicationFactory
{
    public function getOpenTelemetryService(): OpenTelemetryServiceInterface
    {
        return $this->getProvidedDependency(ApplicationDependencyProvider::SERVICE_OPENTELEMETRY);
    }
}
