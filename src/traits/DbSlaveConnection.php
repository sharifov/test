<?php

namespace src\traits;

trait DbSlaveConnection
{
    private static string $dbComponent = 'db_slave';

    public static function getDb()
    {
        return \Yii::$app->get(self::$dbComponent);
    }

    public function setDb(): self
    {
        self::$dbComponent = 'db';
        return $this;
    }
}
