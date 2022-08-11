<?php

use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Class m220622_104848_alter_table_user_profile_drop_column_up_2fa_timestamp
 */
class m220622_104848_alter_table_user_profile_drop_column_up_2fa_timestamp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (DBHelper::isColumnExist('{{%user_profile}}', 'up_2fa_timestamp')) {
            $this->dropColumn('{{%user_profile}}', 'up_2fa_timestamp');
            Yii::$app->db->getSchema()->refreshTableSchema('{{%user_profile}}');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (!DBHelper::isColumnExist('{{%user_profile}}', 'up_2fa_timestamp')) {
            $this->addColumn('{{%user_profile}}', 'up_2fa_timestamp', $this->timestamp());
            Yii::$app->db->getSchema()->refreshTableSchema('{{%user_profile}}');
        }
    }
}
