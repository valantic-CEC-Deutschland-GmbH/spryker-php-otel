<?php

namespace Pyz\Zed\Application\Communication\Plugin;

use Pyz\Zed\Application\Communication\ApplicationCommunicationFactory;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\BootableApplicationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method ApplicationCommunicationFactory getFactory()
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
