<?php

namespace modules\fileStorage\src\services\configurator;

/**
 * Class LocalConfigurator
 *
 * @property array $uploadConfig
 */
class LocalConfigurator implements Configurator
{
    private array $uploadConfig;

    public function __construct(array $uploadConfig)
    {
        $this->uploadConfig = $uploadConfig;
    }

    public function getUploadConfig(): array
    {
        return $this->uploadConfig;
    }
}
