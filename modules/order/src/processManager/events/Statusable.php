<?php

namespace modules\order\src\processManager\events;

interface Statusable
{
    public function getStatusName(): string;
}
