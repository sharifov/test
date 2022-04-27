<?php

use common\models\Lead;
use yii\db\Migration;

/**
 * Class m220126_110633_update_date_for_leads_in_status_snooze
 */
class m220126_110633_update_date_for_leads_in_status_snooze extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $date = new DateTimeImmutable('+1 day');
        $this->update('{{%leads}}', ['snooze_for' => $date->format('Y-m-d H:i:s')], ['status' => Lead::STATUS_SNOOZE, 'snooze_for' => null]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
