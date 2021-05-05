<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210414_062014_drop_columns_from_flight_quote_flight
 */
class m210414_062014_drop_columns_from_flight_quote_flight extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%flight_quote_flight}}', 'fqf_record_locator');
        $this->dropColumn('{{%flight_quote_flight}}', 'fqf_gds_pcc');
        $this->dropColumn('{{%flight_quote_flight}}', 'fqf_gds');

        $this->truncateTable('{{%flight_quote_ticket}}');

        $this->dropForeignKey('FK-flight_quote_ticket-fqt_flight_id', '{{%flight_quote_ticket}}');
        $this->dropForeignKey('FK-flight_quote_ticket-fqt_pax_id', '{{%flight_quote_ticket}}');
        $this->dropPrimaryKey('PK-flight_quote_ticket-fqt_pax_id-fqt_flight_id', '{{%flight_quote_ticket}}');
        $this->dropColumn('{{%flight_quote_ticket}}', 'fqt_flight_id');

        $this->addColumn('{{%flight_quote_ticket}}', 'fqt_fqb_id', $this->integer()->notNull());
        $this->addPrimaryKey('PK-flight_quote_ticket-fqt_pax_id-fqt_fqb_id', '{{%flight_quote_ticket}}', ['fqt_pax_id', 'fqt_fqb_id']);
        $this->addForeignKey(
            'FK-flight_quote_ticket-fqt_fqb_id',
            '{{%flight_quote_ticket}}',
            ['fqt_fqb_id'],
            '{{%flight_quote_booking}}',
            ['fqb_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-flight_quote_ticket-fqt_pax_id',
            '{{%flight_quote_ticket}}',
            ['fqt_pax_id'],
            '{{%flight_pax}}',
            ['fp_id'],
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%flight_quote_flight}}', 'fqf_record_locator', $this->string(8));
        $this->addColumn('{{%flight_quote_flight}}', 'fqf_gds_pcc', $this->string(10));
        $this->addColumn('{{%flight_quote_flight}}', 'fqf_gds', $this->string(2));

        $this->truncateTable('{{%flight_quote_ticket}}');

        $this->dropForeignKey('FK-flight_quote_ticket-fqt_fqb_id', '{{%flight_quote_ticket}}');

        $this->dropForeignKey('FK-flight_quote_ticket-fqt_pax_id', '{{%flight_quote_ticket}}');
        $this->dropPrimaryKey('PK-flight_quote_ticket-fqt_pax_id-fqt_fqb_id', '{{%flight_quote_ticket}}');
        $this->dropColumn('{{%flight_quote_ticket}}', 'fqt_fqb_id');

        $this->addColumn('{{%flight_quote_ticket}}', 'fqt_flight_id', $this->integer()->notNull());
        $this->addPrimaryKey('PK-flight_quote_ticket-fqt_pax_id-fqt_flight_id', '{{%flight_quote_ticket}}', ['fqt_pax_id', 'fqt_flight_id']);
        $this->addForeignKey(
            'FK-flight_quote_ticket-fqt_flight_id',
            '{{%flight_quote_ticket}}',
            ['fqt_flight_id'],
            '{{%flight_quote_flight}}',
            ['fqf_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-flight_quote_ticket-fqt_pax_id',
            '{{%flight_quote_ticket}}',
            ['fqt_pax_id'],
            '{{%flight_pax}}',
            ['fp_id'],
            'CASCADE',
            'CASCADE'
        );
    }
}
