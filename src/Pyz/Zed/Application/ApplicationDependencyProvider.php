<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Application;

use Pyz\Zed\Application\Communication\Plugin\OpenTelemetrySetupPlugin;
use Spryker\Zed\Application\ApplicationDependencyProvider as SprykerApplicationDependencyProvider;
use Spryker\Zed\Currency\Communication\Plugin\Application\CurrencyBackendGatewayApplicationPlugin;
use Spryker\Zed\ErrorHandler\Communication\Plugin\Application\ErrorHandlerApplicationPlugin;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\BackendApiEventDispatcherApplicationPlugin;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\BackendGatewayEventDispatcherApplicationPlugin;
use Spryker\Zed\EventDispatcher\Communication\Plugin\Application\EventDispatcherApplicationPlugin;
use Spryker\Zed\Form\Communication\Plugin\Application\FormApplicationPlugin;
use Spryker\Zed\Http\Communication\Plugin\Application\HttpApplicationPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Locale\Communication\Plugin\Application\LocaleApplicationPlugin;
use Spryker\Zed\Locale\Communication\Plugin\Application\LocaleBackendGatewayApplicationPlugin;
use Spryker\Zed\Messenger\Communication\Plugin\Application\MessengerApplicationPlugin;
use Spryker\Zed\Propel\Communication\Plugin\Application\PropelApplicationPlugin;
use Spryker\Zed\Router\Communication\Plugin\Application\BackendApiRouterApplicationPlugin;
use Spryker\Zed\Router\Communication\Plugin\Application\BackendGatewayRouterApplicationPlugin;
use Spryker\Zed\Router\Communication\Plugin\Application\BackofficeRouterApplicationPlugin;
use Spryker\Zed\Security\Communication\Plugin\Application\SecurityApplicationPlugin;
use Spryker\Zed\Session\Communication\Plugin\Application\MockArraySessionApplicationPlugin;
use Spryker\Zed\Session\Communication\Plugin\Application\SessionApplicationPlugin;
use Spryker\Zed\Store\Communication\Plugin\Application\BackofficeStoreApplicationPlugin;
use Spryker\Zed\Store\Communication\Plugin\Application\StoreBackendGatewayApplicationPlugin;
use Spryker\Zed\Translator\Communication\Plugin\Application\TranslatorApplicationPlugin;
use Spryker\Zed\Twig\Communication\Plugin\Application\TwigApplicationPlugin;
use Spryker\Zed\UtilNumber\Communication\Plugin\Application\NumberFormatterApplicationPlugin;
use Spryker\Zed\Validator\Communication\Plugin\Application\ValidatorApplicationPlugin;
use Spryker\Zed\WebProfiler\Communication\Plugin\Application\WebProfilerApplicationPlugin;
use Spryker\Zed\ZedRequest\Communication\Plugin\Application\RequestBackendGatewayApplicationPlugin;

class ApplicationDependencyProvider extends SprykerApplicationDependencyProvider
{

    public const SERVICE_OPENTELEMETRY = 'SERVICE_OPENTELEMETRY';

    /**
     * @deprecated Use {@link \Pyz\Zed\Application\ApplicationDependencyProvider::getBackofficeApplicationPlugins()} instead.
     *
     * @return array<\Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface>
     */
    protected function getApplicationPlugins(): array
    {
        return $this->getBackofficeApplicationPlugins();
    }

    /**
     * @return array<\Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface>
     */
    protected function getBackofficeApplicationPlugins(): array
    {
        $applicationPlugins = [
            new SessionApplicationPlugin(),
            new TwigApplicationPlugin(),
            new EventDispatcherApplicationPlugin(),
            new LocaleApplicationPlugin(),
            new TranslatorApplicationPlugin(),
            new MessengerApplicationPlugin(),
            new PropelApplicationPlugin(),
            new BackofficeRouterApplicationPlugin(),
            new HttpApplicationPlugin(),
            new ErrorHandlerApplicationPlugin(),
            new FormApplicationPlugin(),
            new ValidatorApplicationPlugin(),
            new SecurityApplicationPlugin(),
            new NumberFormatterApplicationPlugin(),
            new BackofficeStoreApplicationPlugin(),
            new OpenTelemetrySetupPlugin(),
        ];

        if (class_exists(WebProfilerApplicationPlugin::class)) {
            $applicationPlugins[] = new WebProfilerApplicationPlugin();
        }

        return $applicationPlugins;
    }

    /**
     * @return array<\Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface>
     */
    protected function getBackendGatewayApplicationPlugins(): array
    {
        return [
            new BackendGatewayEventDispatcherApplicationPlugin(),
            new RequestBackendGatewayApplicationPlugin(),
            new StoreBackendGatewayApplicationPlugin(),
            new LocaleBackendGatewayApplicationPlugin(),
            new CurrencyBackendGatewayApplicationPlugin(),
            new MockArraySessionApplicationPlugin(),
            new TranslatorApplicationPlugin(),
            new TwigApplicationPlugin(),
            new PropelApplicationPlugin(),
            new BackendGatewayRouterApplicationPlugin(),
            new HttpApplicationPlugin(),
            new OpenTelemetrySetupPlugin(),
        ];
    }

    /**
     * @return array<\Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface>
     */
    protected function getBackendApiApplicationPlugins(): array
    {
        return [
            new BackendApiEventDispatcherApplicationPlugin(),
            new LocaleApplicationPlugin(),
            new TranslatorApplicationPlugin(),
            new PropelApplicationPlugin(),
            new BackendApiRouterApplicationPlugin(),
            new HttpApplicationPlugin(),
            new ErrorHandlerApplicationPlugin(),
            new ValidatorApplicationPlugin(),
            new OpenTelemetrySetupPlugin(),
        ];
    }

    public function provideCommunicationLayerDependencies(Container $container)
    {

        $container = parent::provideCommunicationLayerDependencies($container);

        $container->set(static::SERVICE_OPENTELEMETRY, fn() => $container->getLocator()->openTelemetry()->service());

        return $container;
    }
}
