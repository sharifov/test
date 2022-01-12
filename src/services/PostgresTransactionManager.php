<?php

namespace src\services;

use src\dispatchers\DeferredEventDispatcher;

class PostgresTransactionManager
{
    private $dispatcher;

    public function __construct(DeferredEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function wrap(callable $function)
    {
        $result = null;
        $transaction = \Yii::$app->db_postgres->beginTransaction();
        try {
            $this->dispatcher->defer();
            $result = $function();
            $transaction->commit();
            $this->dispatcher->release();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->dispatcher->clean();
            throw $e;
        }
        return $result;
    }
}
