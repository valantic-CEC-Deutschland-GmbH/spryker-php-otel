<?php

namespace Pyz\Yves\ShopApplication\Plugin;

use Pyz\Yves\ShopApplication\ShopApplicationFactory;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\BootableApplicationPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method ShopApplicationFactory getFactory()
 */
class OpenTelemetrySetupPlugin extends AbstractPlugin implements ApplicationPluginInterface, BootableApplicationPluginInterface
{

    public function provide(ContainerInterface $container): ContainerInterface
    {
        $this->getFactory()->getOpenTelemetryService()->registerInitializer();

        return $container;
    }

    public function boot(ContainerInterface $container): ContainerInterface
    {
        $this->getFactory()->getOpenTelemetryService()->registerInitializer();

        return $container;
    }
}
