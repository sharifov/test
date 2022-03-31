<?php

use yii\db\Migration;
use src\helpers\app\AppHelper;
use src\helpers\app\DBHelper;

/**
 * Class m220330_130618_add_fields_for_resolution_to_user_feedback
 */
class m220330_130618_add_fields_for_resolution_to_user_feedback extends Migration
{
    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            if (!DBHelper::isColumnExist('user_feedback', 'uf_resolution')) {
                $this->addColumn('{{%user_feedback}}', 'uf_resolution', $this->string(500));
            }
            if (!DBHelper::isColumnExist('user_feedback', 'uf_resolution_user_id')) {
                $this->addColumn('{{%user_feedback}}', 'uf_resolution_user_id', $this->integer());
            }
            if (!DBHelper::isColumnExist('user_feedback', 'uf_resolution_dt')) {
                $this->addColumn('{{%user_feedback}}', 'uf_resolution_dt', $this->dateTime());
            }
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableLog($throwable),
                'm220330_130618_add_fields_for_resolution_to_user_feedback:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            if (DBHelper::isColumnExist('user_feedback', 'uf_resolution')) {
                $this->dropColumn('{{%user_feedback}}', 'uf_resolution');
            }
            if (DBHelper::isColumnExist('user_feedback', 'uf_resolution_user_id')) {
                $this->dropColumn('{{%user_feedback}}', 'uf_resolution_user_id');
            }
            if (DBHelper::isColumnExist('user_feedback', 'uf_resolution_dt')) {
                $this->dropColumn('{{%user_feedback}}', 'uf_resolution');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220330_130618_add_fields_for_resolution_to_user_feedback:safeDown:Throwable');
        }
    }
}
