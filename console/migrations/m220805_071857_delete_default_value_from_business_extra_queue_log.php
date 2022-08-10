<?php

use src\helpers\app\AppHelper;
use yii\db\Migration;

/**
 * Class m220805_071857_delete_default_value_from_business_extra_queue
 */
class m220805_071857_delete_default_value_from_business_extra_queue_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->alterColumn('{{%lead_business_extra_queue_log}}', 'lbeql_lead_owner_id', $this->integer()->unsigned()->null());
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220805_071857_delete_default_value_from_business_extra_queue_log:safeUp:Throwable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->alterColumn('{{%lead_business_extra_queue_log}}', 'lbeql_lead_owner_id', $this->integer());
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'm220805_071857_delete_default_value_from_business_extra_queue_log:safeDown:Throwable');
        }
    }
}
