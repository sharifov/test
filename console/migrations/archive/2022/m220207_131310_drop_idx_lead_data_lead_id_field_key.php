<?php

use yii\db\Migration;
use yii\helpers\Console;

/**
 * Class m220207_131310_drop_idx_lead_data_lead_id_field_key
 */
class m220207_131310_drop_idx_lead_data_lead_id_field_key extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $db = Yii::$app->getDb();
        $schema = $db->createCommand('select database()')->queryScalar();
        $table = 'lead_data';
        $indKey = 'IND-lead_data-lead_id-field_key';

        $isExistIndex = $db->createCommand(
            '
            SELECT 
                COUNT(1) AS cnt 
            FROM 
                information_schema.table_constraints 
            WHERE 
                constraint_name=:indKey 
            AND 
                table_name=:tableName
            AND 
                constraint_schema=:schema',
            [
                ':indKey' => $indKey,
                ':tableName' => $table,
                ':schema' => $schema,
            ]
        )->queryScalar();

        if ((bool) $isExistIndex) {
            $this->dropIndex('IND-lead_data-lead_id-field_key', '{{%lead_data}}');
        } else {
            echo Console::renderColoredString('%y --- Index (IND-lead_data-lead_id-field_key) not found %n'), PHP_EOL;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220207_131310_drop_idx_lead_data_lead_id_field_key cannot be reverted.\n";
    }
}
