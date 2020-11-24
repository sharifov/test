<?php

use yii\db\Migration;

/**
 * Class m200722_090252_alter_tbl_client_chat_visitor
 */
class m200722_090252_alter_tbl_client_chat_visitor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%client_chat_visitor}}', 'ccv_visitor_rc_id');

        $this->addColumn('{{%client_chat_visitor}}', 'ccv_cvd_id', $this->integer()->after('ccv_id'));
        $this->addColumn('{{%client_chat_visitor}}', 'ccv_cch_id', $this->integer()->after('ccv_id'));

        $this->addForeignKey('FK-client_chat_visitor-ccv_cvd_id', '{{%client_chat_visitor}}', ['ccv_cvd_id'], '{{%client_chat_visitor_data}}', ['cvd_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-client_chat_visitor-ccv_cch_id', '{{%client_chat_visitor}}', ['ccv_cch_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');

        $this->dropForeignKey('FK-client_chat_visitor_data-cvd_ccv_id', '{{%client_chat_visitor_data}}');
        $this->dropColumn('{{%client_chat_visitor_data}}', 'cvd_ccv_id');

        $this->addColumn('{{%client_chat_visitor_data}}', 'cvd_visitor_rc_id', $this->string(50)->unique());

        $this->createIndex('UNI-client_chat_visitor-cch_id-cvd_id', '{{%client_chat_visitor}}', ['ccv_cch_id', 'ccv_cvd_id'], true);

        $this->addColumn('{{%visitor_log}}', 'vl_cvd_id', $this->integer());
        $this->createIndex('IND-visitor_log-vl_cvd_id', '{{%visitor_log}}', ['vl_cvd_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%client_chat_visitor}}', 'ccv_visitor_rc_id', $this->string(50)->unique());

        $this->dropForeignKey('FK-client_chat_visitor-ccv_cvd_id', '{{%client_chat_visitor}}');
        $this->dropForeignKey('FK-client_chat_visitor-ccv_cch_id', '{{%client_chat_visitor}}');

        $this->dropColumn('{{%client_chat_visitor}}', 'ccv_cvd_id');
        $this->dropColumn('{{%client_chat_visitor}}', 'ccv_cch_id');

        $this->addColumn('{{%client_chat_visitor_data}}', 'cvd_ccv_id', $this->integer()->unique()->after('cvd_id'));
        $this->addForeignKey('FK-client_chat_visitor_data-cvd_ccv_id', '{{%client_chat_visitor_data}}', ['cvd_ccv_id'], '{{%client_chat_visitor}}', ['ccv_id'], 'SET NULL', 'CASCADE');

        $this->dropColumn('{{%client_chat_visitor_data}}', 'cvd_visitor_rc_id');

        $this->dropColumn('{{%visitor_log}}', 'vl_cvd_id');
    }
}
