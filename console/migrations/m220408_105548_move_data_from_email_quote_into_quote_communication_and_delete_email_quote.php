<?php

use yii\db\Migration;
use yii\db\Query;
use frontend\models\CommunicationForm;

/**
 * Class m220408_105548_move_data_from_email_quote_into_quote_communication_and_delete_email_quote
 */
class m220408_105548_move_data_from_email_quote_into_quote_communication_and_delete_email_quote extends Migration
{
    const SOURCE_TABLE_NAME = 'email_quote';
    const SOURCE_TABLE = '{{%' . self::SOURCE_TABLE_NAME . '}}';

    const TARGET_TABLE_NAME = 'quote_communication';
    const TARGET_TABLE = '{{%' . self::TARGET_TABLE_NAME . '}}';

    /**
     * @return false|mixed|void
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $existEmailQuotes = (new Query())
            ->select('*')
            ->from(self::SOURCE_TABLE)
            ->all();

        $quoteCommunicationInsertingData = array_map(function ($item) {
            return [
                'qc_communication_type' => CommunicationForm::TYPE_EMAIL,
                'qc_communication_id' => (int)$item['eq_email_id'],
                'qc_quote_id' => (int)$item['eq_quote_id'],
                'qc_created_dt' => $item['eq_created_dt'],
                'qc_created_by' => (int)$item['eq_created_by']
            ];
        }, $existEmailQuotes);

        \Yii::$app
            ->db
            ->createCommand()
            ->batchInsert(
                self::TARGET_TABLE,
                ['qc_communication_type', 'qc_communication_id', 'qc_quote_id', 'qc_created_dt', 'qc_created_by'],
                $quoteCommunicationInsertingData
            )
            ->execute();
    }

    /**
     * @return false|mixed|void
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        \Yii::$app
            ->db
            ->createCommand()
            ->truncateTable(self::TARGET_TABLE)
            ->execute();
    }
}
