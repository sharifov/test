<?php

namespace console\socket\Commands;

interface Connectionable
{
    public function setConnectionId(int $connectionId);
}
