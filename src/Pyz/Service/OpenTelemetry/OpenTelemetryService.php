<?php

namespace Pyz\Service\OpenTelemetry;

use Spryker\Service\Kernel\AbstractService;


/**
 * @method OpenTelemetryServiceFactory getFactory()
 */
class OpenTelemetryService extends AbstractService implements OpenTelemetryServiceInterface
{
    public function registerInitializer(): void
    {
        $this->getFactory()->createOpenTelementryInitializer()->initialize();
    }
}
