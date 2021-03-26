<?php

namespace modules\order\src\processManager\phoneToBook\events;

interface Statusable
{
    public function getStatusName(): string;
}
