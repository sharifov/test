<?php

namespace modules\fileStorage\src;

/**
 * Class AwsS3Configurator
 *
 * @property array $uploadConfig
 */
class AwsS3Configurator implements Configurator
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
