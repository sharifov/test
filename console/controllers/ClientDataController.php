<?php

namespace console\controllers;

use src\helpers\app\AppHelper;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\clientData\entity\ClientData;
use src\model\clientDataKey\entity\ClientDataKeyDictionary;
use src\model\clientDataKey\service\ClientDataKeyService;
use Yii;
use yii\console\Controller;
use yii\db\Expression;

class ClientDataController extends Controller
{
    public function actionFixClientDataValues($limit = 50000)
    {
        try {
            Yii::$app->log->targets['db-info']->exportInterval = 1;

            $keyId = ClientDataKeyService::getIdByKeyCache(ClientDataKeyDictionary::IS_SEND_TO_WEB_ENGAGE);

            Yii::$app->db
                ->createCommand()
                ->update(ClientData::tableName(), [
                    'cd_field_value' => '1'
                ], [
                    'cd_key_id' => $keyId
                ])->execute();

            $keyId = ClientDataKeyService::getIdByKeyCache(ClientDataKeyDictionary::APP_CALL_OUT_TOTAL_COUNT);

            $clientDataCount = ClientData::find()
                ->where(['cd_key_id' => $keyId])
                ->orderBy(['cd_id' => SORT_DESC])
                ->asArray()->count();


            $iterations = (int)($clientDataCount / $limit) + 1;
            $processed = 0;
            for ($i = 0; $i < $iterations; $i++) {
                $clientData = ClientData::find()
                    ->where(['cd_key_id' => $keyId])
                    ->orderBy(['cd_id' => SORT_DESC])
                    ->limit($limit)
                    ->offset($i * $limit)
                    ->asArray()
                    ->all();

                $countItems = $clientDataCount;

                $wrongData = [];
                foreach ($clientData as $data) {
                    $totalCalls = CallLog::find()
                        ->andWhere(new Expression('cl_id = cl_group_id'))
                        ->andWhere([CallLog::tableName() . '.cl_type_id' => CallLogType::OUT])
                        ->andWhere(['cl_client_id' => $data['cd_client_id']])
                        ->count();


                    if (!$totalCalls) {
                        $wrongData[] = [
                            'id' => $data['cd_id'],
                            'clientId' => $data['cd_client_id'],
                            'value' => $data['cd_field_value']
                        ];
                    } else {
                        Yii::$app->db
                            ->createCommand()
                            ->update(ClientData::tableName(), [
                                'cd_field_value' => $totalCalls
                            ], [
                                'cd_key_id' => $keyId,
                                'cd_client_id' => $data['cd_client_id']
                            ])
                            ->execute();
                    }

                    $processed++;
                    if ($processed % $limit === 0) {
                        Yii::info([
                            'totalItems' => $countItems,
                            'currentProcessed' => $processed
                        ], 'info\console::actionFixClientDataValues');
                    }
                }

                Yii::info([
                    'totalItems' => $countItems,
                    'processed' => $processed,
                    'wrongData' => $wrongData
                ], 'info\console::actionFixClientDataValues');
            }
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableLog($throwable),
                'console::actionFixClientDataValues::Throwable'
            );
        }
    }
}
