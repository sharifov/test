<?php

namespace src\logger\db;

/**
 * Interface GlobalLogInterface
 * @package src\services\logger\db
 */
interface GlobalLogInterface
{
    public function log(LogDTO $data);
}
