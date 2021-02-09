<?php

namespace modules\fileStorage\src\services\configurator;

interface Configurator
{
    public function getUploadConfig(): array;
}
