<?php

use yii\db\Migration;

/**
 * Class m220802_113920_create_tbl_user_activity
 */
class m220802_113920_create_tbl_user_activity extends Migration
{
    /**
        Int8 — [-128 : 127]
        Int16 — [-32768 : 32767]
        Int32 — [-2147483648 : 2147483647]
        Int64 — [-9223372036854775808 : 9223372036854775807]
        Int128 — [-170141183460469231731687303715884105728 : 170141183460469231731687303715884105727]

        Int8 — TINYINT, BOOL, BOOLEAN, INT1.
        Int16 — SMALLINT, INT2.
        Int32 — INT, INT4, INTEGER.
        Int64 — BIGINT.

        UInt8 — [0 : 255]
        UInt16 — [0 : 65535]
        UInt32 — [0 : 4294967295]
        UInt64 — [0 : 18446744073709551615]
        UInt128 — [0 : 340282366920938463463374607431768211455]
        UInt256 — [0 : 115792089237316195423570985008687907853269984665640564039457584007913129639935]

     */

    public function init()
    {
        $this->db = 'clickhouse';
        parent::init();
    }

    public function up()
    {
        $tableOptions =
            'ENGINE = MergeTree() 
        PARTITION BY toYYYYMM(ua_start_dt)
        PRIMARY KEY (ua_start_dt, ua_user_id, ua_object_event, ua_object_id) 
        ORDER BY (ua_start_dt, ua_user_id, ua_object_event, ua_object_id) 
        TTL ua_start_dt + INTERVAL 6 MONTH DELETE';

        //$this->dropTable('user_activity');

        $this->createTable('user_activity', [
            //'ua_id' => $this->bigInteger()->unsigned(),
            // 'ua_uuid' => 'UUID',
            'ua_user_id' => $this->integer()->unsigned(),
            'ua_object_event' => $this->string(),
            'ua_object_id' => $this->integer()->unsigned(),
            'ua_start_dt' => $this->dateTime(),
            'ua_end_dt' => $this->dateTime(),
            'ua_type_id' => $this->smallInteger()->unsigned(),
            'ua_shift_event_id' => $this->integer()->unsigned(),
            'ua_description' => $this->string()
        ], $tableOptions);


        //Yii::$app->db->getSchema()->refreshTableSchema('{{%user_activity}}');
    }


    public function down()
    {
        $this->dropTable('user_activity');
    }
}
