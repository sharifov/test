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

        $this->dropTable(self::SOURCE_TABLE);
    }

    /**
     * @return false|mixed|void
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        // Return back the `email_quotes` table
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%email_quote}}', [
            'eq_id' => $this->primaryKey(),
            'eq_email_id' => $this->integer()->notNull(),
            'eq_quote_id' => $this->integer()->notNull(),
            'eq_created_dt' => $this->timestamp(),
            'eq_created_by' => $this->integer()
        ], $tableOptions);

        $this->addForeignKey('FK-email_quote-eq_email_id', '{{%email_quote}}', 'eq_email_id', '{{%email}}', 'e_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_quote-eq_quote_id', '{{%email_quote}}', 'eq_quote_id', '{{%quotes}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-email_quote-eq_created_by', '{{%email_quote}}', 'eq_created_by', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

        $this->createIndex('IND-email_quote-eq_email_id', '{{%email_quote}}', 'eq_email_id');
        $this->createIndex('IND-email_quote-eq_quote_id', '{{%email_quote}}', 'eq_quote_id');

        // Move data from `quote_communication` into `email_quotes`
        $existQuoteCommunications = (new Query())
            ->select('*')
            ->from(self::TARGET_TABLE)
            ->all();

        $emailQuoteInsertingData = array_map(function ($item) {
            return [
                'eq_email_id' => (int)$item['qc_communication_id'],
                'eq_quote_id' => (int)$item['qc_quote_id'],
                'eq_created_dt' => $item['qc_created_dt'],
                'eq_created_by' => (int)$item['qc_created_by']
            ];
        }, $existQuoteCommunications);

        \Yii::$app
            ->db
            ->createCommand()
            ->batchInsert(
                self::SOURCE_TABLE,
                ['eq_email_id', 'eq_quote_id', 'eq_created_dt', 'eq_created_by'],
                $emailQuoteInsertingData
            )
            ->execute();

        \Yii::$app
            ->db
            ->createCommand()
            ->truncateTable(self::TARGET_TABLE)
            ->execute();
    }
}
