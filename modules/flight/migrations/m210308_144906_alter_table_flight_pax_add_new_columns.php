<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210308_144906_alter_table_flight_pax_add_new_columns
 */
class m210308_144906_alter_table_flight_pax_add_new_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_pax}}', 'fp_nationality', $this->string(5));
        $this->addColumn('{{%flight_pax}}', 'fp_gender', $this->string(1));
        $this->addColumn('{{%flight_pax}}', 'fp_email', $this->string(100));
        $this->addColumn('{{%flight_pax}}', 'fp_language', $this->string(5));
        $this->addColumn('{{%flight_pax}}', 'fp_citizenship', $this->string(5));

        $this->addForeignKey('FK-flight_pax-fp_language', '{{%flight_pax}}', ['fp_language'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-flight_pax-fp_language', '{{%flight_pax}}');
        $this->dropColumn('{{%flight_pax}}', 'fp_nationality');
        $this->dropColumn('{{%flight_pax}}', 'fp_gender');
        $this->dropColumn('{{%flight_pax}}', 'fp_email');
        $this->dropColumn('{{%flight_pax}}', 'fp_language');
        $this->dropColumn('{{%flight_pax}}', 'fp_citizenship');
    }
}
