<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Application;

use Pyz\Yves\Application\Plugin\OpenTelemetrySetupPlugin;
use Spryker\Service\Container\Exception\FrozenServiceException;
use Spryker\Yves\Application\ApplicationDependencyProvider as SprykerApplicationDependencyProvider;
use Spryker\Yves\Kernel\Container;
use SprykerShop\Yves\DateTimeConfiguratorPageExample\Plugin\Application\ConfiguratorSecurityHeaderExpanderPlugin;

class ApplicationDependencyProvider extends SprykerApplicationDependencyProvider
{
    public const SERVICE_OPENTELEMETRY = 'SERVICE_OPENTELEMETRY';

    /**
     * @return array<\Spryker\Yves\ApplicationExtension\Dependency\Plugin\SecurityHeaderExpanderPluginInterface>
     */
    protected function getSecurityHeaderExpanderPlugins(): array
    {
        return [
            new ConfiguratorSecurityHeaderExpanderPlugin(),
        ];
    }

    /**
     * @return array<\Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface>
     */
    protected function getApplicationPlugins(): array
    {
        return [
            new OpenTelemetrySetupPlugin(),
        ];
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     * @throws FrozenServiceException
     */
    public function provideDependencies(Container $container) {
        $container = parent::provideDependencies($container);

        $container->set(static::SERVICE_OPENTELEMETRY, fn() => $container->getLocator()->openTelemetry()->service());

        return $container;
    }
}
