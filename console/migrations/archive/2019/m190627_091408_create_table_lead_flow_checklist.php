<?php

use yii\db\Migration;

/**
 * Class m190627_091408_create_table_lead_flow_checklist
 */
class m190627_091408_create_table_lead_flow_checklist extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lead_flow_checklist}}', [
            'lfc_lf_id' => $this->integer(),
            'lfc_lc_type_id' => $this->integer(),
            'lfc_lc_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-lead_flow_checklist_lfc_lf_id_lfc_lc_type_id', '{{%lead_flow_checklist}}', ['lfc_lf_id', 'lfc_lc_type_id']);
        $this->addForeignKey('FK-lead_flow_checklist_lfc_lf_id', '{{%lead_flow_checklist}}', ['lfc_lf_id'], '{{%lead_flow}}', ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-lead_flow_checklist_lfc_lc_user_id', '{{%lead_flow_checklist}}', ['lfc_lc_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-lead_flow_checklist_lfc_lf_id', '{{%lead_flow_checklist}}');
        $this->dropForeignKey('FK-lead_flow_checklist_lfc_lc_user_id', '{{%lead_flow_checklist}}');
        $this->dropPrimaryKey('PK-lead_flow_checklist_lfc_lf_id_lfc_lc_type_id', '{{%lead_flow_checklist}}');
        $this->dropTable('{{%lead_flow_checklist}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

}
