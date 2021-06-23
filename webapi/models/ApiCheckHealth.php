<?php

namespace webapi\models;

use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

class ApiCheckHealth extends ActiveRecord
{
    /**
     * @throws \yii\httpclient\Exception
     */
    public static function quickCheckHealth()
    {
        $response = [];
        try {
            $test_row = \Yii::$app->db->createCommand('SELECT id FROM employees LIMIT 1')->queryOne();

            if ($test_row['id'] > 0) {
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
        } catch (\yii\db\Exception $e) {
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
        } catch (\Throwable $e) {
            $response['redis'] = false;
        }

        try {
            $cache = \Yii::$app->cacheFile;
            $key = 'health_check';
            $cache->set($key, 'test_passed');
            if ($cache->get($key) == 'test_passed') {
                $response['cacheFile'] = true;
            } else {
                $response['cacheFile'] = false;
            }
        } catch (\Throwable $e) {
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
