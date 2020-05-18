<?php

use yii\db\Migration;

/**
 * Class m190905_073451_drop_columns_tbl_call
 */
class m190905_073451_drop_columns_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%call}}', 'c_account_sid');
        $this->dropColumn('{{%call}}', 'c_api_version');
        //$this->dropColumn('{{%call}}', 'c_sequence_number');
        $this->dropColumn('{{%call}}', 'c_sip');
        $this->dropColumn('{{%call}}', 'c_sip_response_code');
        $this->dropColumn('{{%call}}', 'c_direction');
        $this->dropColumn('{{%call}}', 'c_recording_sid');
        $this->dropColumn('{{%call}}', 'c_timestamp');
        $this->dropColumn('{{%call}}', 'c_uri');

        $this->addColumn('{{%call}}', 'c_status_id', $this->smallInteger());
        $this->addColumn('{{%call}}', 'c_parent_id', $this->integer());

        $this->addForeignKey('FK-call_c_parent_id', '{{%call}}', ['c_parent_id'], '{{%call}}', ['c_id'], 'SET NULL', 'CASCADE');

        // $this->createIndex('IND-call_c_parent_id', '{{%call}}', ['c_parent_id']);
        $this->createIndex('IND-call_c_status_id', '{{%call}}', ['c_status_id']);


//        $this->createTable('{{%call}}',	[
//            'c_id'                 => $this->primaryKey(),
//            'c_call_sid'           => $this->string(34)->notNull(),
//            'c_account_sid'        => $this->string(34)->notNull(),
//            'c_call_type_id'       => $this->tinyInteger(1),
//            'c_from'               => $this->string(100),
//            'c_to'                 => $this->string(100),
//            'c_sip'                => $this->string(100),
//            'c_call_status'        => $this->string(15),
//            'c_api_version'        => $this->string(10),
//            'c_direction'          => $this->string(15),
//            'c_forwarded_from'     => $this->string(100),
//            'c_caller_name'        => $this->string(50),
//            'c_parent_call_sid'    => $this->string(34),
//            'c_call_duration'      => $this->string(20),
//            'c_sip_response_code'  => $this->string(10),
//            'c_recording_url'      => $this->string(120),
//            'c_recording_sid'      => $this->string(34),
//            'c_recording_duration' => $this->string(20),
//            'c_timestamp'          => $this->string(40),
//            'c_uri'                => $this->string(120),
//            'c_sequence_number'    => $this->string(40),
//            'c_lead_id'            => $this->integer(),
//            'c_created_user_id'    => $this->integer(),
//            'c_created_dt'         => $this->dateTime(),
//
//        ], $tableOptions);


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('FK-call_c_parent_id', '{{%call}}');
        $this->dropColumn('{{%call}}', 'c_parent_id');

        $this->addColumn('{{%call}}', 'c_account_sid', $this->string(34)->notNull());
        $this->addColumn('{{%call}}', 'c_api_version', $this->string(10));
        // $this->addColumn('{{%call}}', 'c_sequence_number', $this->string(40));
        $this->addColumn('{{%call}}', 'c_sip', $this->string(100));
        $this->addColumn('{{%call}}', 'c_sip_response_code', $this->string(10));
        $this->addColumn('{{%call}}', 'c_direction', $this->string(15));
        $this->addColumn('{{%call}}', 'c_recording_sid', $this->string(34));
        $this->addColumn('{{%call}}', 'c_timestamp', $this->string(40));
        $this->addColumn('{{%call}}', 'c_uri', $this->string(120));


        $this->dropColumn('{{%call}}', 'c_status_id');


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

        Yii::$app->db->getSchema()->refreshTableSchema('{{%call}}');

    }


}
