<?php

namespace Pyz\Yves\Application\Plugin;

use Pyz\Yves\Application\ApplicationFactory;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\BootableApplicationPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method ApplicationFactory getFactory()
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
