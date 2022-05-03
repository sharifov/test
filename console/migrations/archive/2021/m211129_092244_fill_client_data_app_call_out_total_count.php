<?php

use common\models\Client;
use src\helpers\app\AppHelper;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\clientDataKey\entity\ClientDataKeyDictionary;
use src\model\clientDataKey\service\ClientDataKeyService;
use yii\db\Expression;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m211129_092244_fill_client_data_app_call_out_total_count
 */
class m211129_092244_fill_client_data_app_call_out_total_count extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $keyId = ClientDataKeyService::getIdByKeyCache(ClientDataKeyDictionary::APP_CALL_OUT_TOTAL_COUNT);

            $fillData = CallLog::find()
                ->select([
                    'cl_client_id',
                     new Expression('COUNT(cl_id) AS cnt'),
                     new Expression("{$keyId} AS key_id"),
                ])
                ->innerJoin(Client::tableName(), Client::tableName() . '.id = cl_client_id')
                ->andWhere(['IS NOT', 'cl_client_id', null])
                ->andWhere(new Expression('cl_id = cl_group_id'))
                ->andWhere([CallLog::tableName() . '.cl_type_id' => CallLogType::OUT])
                ->groupBy(['cl_client_id'])
                ->asArray()
                ->all();

            Yii::$app->db->createCommand()->batchInsert(
                '{{%client_data}}',
                ['cd_client_id', 'cd_field_value', 'cd_key_id'],
                $fillData
            )->execute();
            echo Console::renderColoredString('%g --- Added to client_data (' . count($fillData) . ') %n'), PHP_EOL;
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableLog($throwable),
                'm211129_092244_fill_client_data_app_call_out_total_count:Throwable'
            );
            echo Console::renderColoredString('%R --- Migration (m211129_092244_fill_client_data_app_call_out_total_count) is failed %n'), PHP_EOL;
            echo Console::renderColoredString('%R --- Error : ' . $throwable->getMessage() . ' %n'), PHP_EOL;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%client_data}}', ['IN', 'cd_key_id', [
            ClientDataKeyService::getIdByKeyCache(ClientDataKeyDictionary::APP_CALL_OUT_TOTAL_COUNT)
        ]]);
    }
}
