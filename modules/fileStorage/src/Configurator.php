<?php

namespace modules\fileStorage\src;

interface Configurator
{
    public function getUploadConfig(): array;
}
