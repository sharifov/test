<?php

use yii\db\Migration;

/**
 * Class m220419_053410_alter_tbl_user_shift_schedule_year_month
 */
class m220419_053410_alter_tbl_user_shift_schedule_year_month extends Migration
{
    public const TABLE_NAME = 'user_shift_schedule';
    public const TABLE = '{{%' . self::TABLE_NAME . '}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'uss_year_start', 'YEAR NOT NULL');
        $this->addColumn(self::TABLE, 'uss_month_start', $this->tinyInteger(2)->notNull());

        $this->createIndex(
            'IND-' . self::TABLE_NAME . '-uss_year_start-uss_month_start',
            self::TABLE,
            ['uss_year_start', 'uss_month_start']
        );

        Yii::$app->db->getSchema()->refreshTableSchema(self::TABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropIndex(
            'IND-' . self::TABLE_NAME . '-uss_year_start-uss_month_start',
            self::TABLE
        );

        $this->dropColumn(self::TABLE, 'uss_year_start');
        $this->dropColumn(self::TABLE, 'uss_month_start');

        Yii::$app->db->getSchema()->refreshTableSchema(self::TABLE);
    }
}
