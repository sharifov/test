<?php

use src\services\infoBlock\InfoBlockDictionary;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%info_block}}`.
 */
class m220726_054054_create_info_block_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%info_block}}', [
            'ib_id' => $this->primaryKey(),
            'ib_title' => $this->string()->notNull(),
            'ib_key' => $this->string(50)->notNull()->unique(),
            'ib_description' => $this->text(),
            'ib_enabled' => $this->boolean()->defaultValue(false),
            'ib_created_dt' => $this->dateTime(),
            'ib_updated_dt' => $this->dateTime(),
            'ib_created_user_id' => $this->integer(),
            'ib_updated_user_id' => $this->integer(),
        ]);

        $this->addForeignKey('FK-info_block-ib_created_user_id', '{{%info_block}}', 'ib_created_user_id', '{{%employees}}', 'id', 'SET NULL');
        $this->addForeignKey('FK-db_data_sensitive-ib_updated_user_id', '{{%info_block}}', 'ib_updated_user_id', '{{%employees}}', 'id', 'SET NULL');

        $this->insert(
            '{{%info_block}}',
            [
                'ib_key' => InfoBlockDictionary::KEY_HEAT_MAP_AGENT_REPORT,
                'ib_title' => 'Info block for Heat Map Agent Report',
                'ib_description' => '<p><strong>Heat Map Agent Report</strong> displays ShiftScheduleEvent on dates and hours.<br />
                    All ShiftScheduleEvent are displayed in status <strong>APPROVED and DONE</strong>, also&nbsp; shiftScheduleType = <strong>WORK_TIME</strong>. ShiftScheduleEvent are <strong>excluded</strong> only at User in Deleted Status.<br />
                    Filtration:<br />
                    1) <strong>DateRange(From/To)</strong> - range of DateTime in timeframe ShiftSceduleEvent (between startTime and EndTime)<br />
                    2) <strong>Shift</strong> - Shift selection, to which&nbsp; ShiftSceduleEvent refers<br />
                    3) <strong>UserGroup</strong> of User which has ShiftSceduleEvent for these dates<br />
                    4) <strong>Roles</strong> of User which has ShiftSceduleEvent for these dates<br />
                    5) <strong>Timezone</strong> of authorized User</p>',
                'ib_created_dt' => date('Y-m-d H:i:s'),
                'ib_enabled' => true
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%info_block}}');
    }
}
