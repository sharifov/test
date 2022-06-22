<?php

use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Class m220622_083544_alter_table_userprofile_add_columns
 */
class m220622_083544_alter_table_userprofile_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $schemaRefresh = false;
        if (!DBHelper::isColumnExist('{{%user_profile}}', 'up_otp_hashed_code')) {
            $this->addColumn('{{%user_profile}}', 'up_otp_hashed_code', $this->string(32));
            $schemaRefresh = true;
        }

        if (!DBHelper::isColumnExist('{{%user_profile}}', 'up_otp_expired_dt')) {
            $this->addColumn('{{%user_profile}}', 'up_otp_expired_dt', $this->dateTime());
        }

        if ($schemaRefresh) {
            Yii::$app->db->getSchema()->refreshTableSchema('{{%user_profile}}');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $schemaRefresh = false;
        if (DBHelper::isColumnExist('{{%user_profile}}', 'up_otp_hashed_code')) {
            $this->dropColumn('{{%user_profile}}', 'up_otp_hashed_code');
            $schemaRefresh = true;
        }

        if (DBHelper::isColumnExist('{{%user_profile}}', 'up_otp_expired_dt')) {
            $this->dropColumn('{{%user_profile}}', 'up_otp_expired_dt');
            $schemaRefresh = true;
        }

        if ($schemaRefresh) {
            Yii::$app->db->getSchema()->refreshTableSchema('{{%user_profile}}');
        }
    }
}
