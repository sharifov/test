<?php

use yii\db\Migration;

/**
 * Class m200911_120256_add_indexes_tbl_conference_participant_stats
 */
class m200911_120256_add_indexes_tbl_conference_participant_stats extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-cps-cps_duration', '{{%conference_participant_stats}}', ['cps_duration']);
        $this->createIndex('IND-cps-cps_talk_time', '{{%conference_participant_stats}}', ['cps_talk_time']);
        $this->createIndex('IND-cps-cps_hold_time', '{{%conference_participant_stats}}', ['cps_hold_time']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-cps-cps_hold_time', '{{%conference_participant_stats}}');
        $this->dropIndex('IND-cps-cps_talk_time', '{{%conference_participant_stats}}');
        $this->dropIndex('IND-cps-cps_duration', '{{%conference_participant_stats}}');
    }
}
