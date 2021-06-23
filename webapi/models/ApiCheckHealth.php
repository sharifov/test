<?php

namespace webapi\models;

use yii\helpers\VarDumper;

class ApiCheckHealth
{
    /**
     * @throws \yii\httpclient\Exception
     */
    public static function quickCheckHealth()
    {
        $response = [];
        try {
            $row = (new \yii\db\Query())
                ->select(['id'])
                ->from('employees')
                ->limit(1)
                ->one();
            if ($row > 0) {
                $response['mysql'] = true;
            } else {
                $response['mysql'] = false;
            }
        } catch (\yii\db\Exception $e) {
            $response['mysql'] = false;
        }

        try {
            $row = \Yii::$app->db_postgres->createCommand('SELECT id from log LIMIT 1')->queryAll();
            $response['postgresql'] = boolval($row > 0);
        } catch (Throwable $e) {
            $response['postgresql'] = false;
        }

        try {
            $redis = \Yii::$app->redis;
            $key = 'health_check';
            $redis->set($key, 'test_passed');
            if ($redis->get($key) == 'test_passed') {
                $response['redis'] = true;
            } else {
                $response['redis'] = false;
            }
        } catch (\Exception $e) {
            $response['redis'] = false;
        }

        $communication = \Yii::$app->communication;
        $comRequest = $communication->phoneNumberList(1, 1);
        if (!$comRequest || !empty($comRequest['error'])) {
            $response['communication'] = false;
        } else {
            $response['communication'] = true;
        }


        return $response;
    }
}
