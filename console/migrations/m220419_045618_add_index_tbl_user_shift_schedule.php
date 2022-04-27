<?php

use yii\db\Migration;

/**
 * Class m220419_045618_add_index_tbl_user_shift_schedule
 */
class m220419_045618_add_index_tbl_user_shift_schedule extends Migration
{
    public const TABLE_NAME = 'user_shift_schedule';
    public const TABLE = '{{%' . self::TABLE_NAME . '}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createIndex(
            'IND-' . self::TABLE_NAME . '-user-start_end_utc_dt',
            self::TABLE,
            ['uss_user_id', 'uss_start_utc_dt', 'uss_end_utc_dt']
        );

        $this->createIndex(
            'IND-' . self::TABLE_NAME . '-user-start_end_dt-ssr_id',
            self::TABLE,
            ['uss_user_id', 'uss_start_utc_dt', 'uss_end_utc_dt', 'uss_ssr_id']
        );

        $this->createIndex(
            'IND-' . self::TABLE_NAME . '-user-start_end_dt-status_id',
            self::TABLE,
            ['uss_user_id', 'uss_start_utc_dt', 'uss_end_utc_dt', 'uss_status_id']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'IND-' . self::TABLE_NAME . '-user-start_end_utc_dt',
            self::TABLE
        );

        $this->dropIndex(
            'IND-' . self::TABLE_NAME . '-user-start_end_dt-ssr_id',
            self::TABLE
        );

        $this->dropIndex(
            'IND-' . self::TABLE_NAME . '-user-start_end_dt-status_id',
            self::TABLE
        );
    }
}
