<?php

namespace sales\services;

class TransactionManager
{
    public function wrap(callable $function)
    {
        $result = null;
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $result = $function();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $result;
    }
}