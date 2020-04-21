<?php

use yii\db\Migration;

/**
 * Class m180813_071834_lead_flow_table
 */
class m180813_071834_lead_flow_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('lead_flow', [
            'id' =>  $this->primaryKey(),
            'created' => $this->dateTime()->defaultExpression('NOW()'),
            'employee_id' => $this->integer(),
            'lead_id' => $this->integer(),
            'status' => $this->integer()
        ]);
        $this->addForeignKey('fk-lead_flow-employee', 'lead_flow', 'employee_id', 'employees', 'id');
        $this->addForeignKey('fk-lead_flow-lead', 'lead_flow', 'lead_id', 'leads', 'id');

        $this->addColumn('{{%leads}}', 'request_ip', $this->string());
        $this->addColumn('{{%leads}}', 'request_ip_detail', $this->text());
        $this->addColumn('{{%leads}}', 'offset_gmt', $this->string());
        $this->addColumn('{{%leads}}', 'snooze_for', $this->dateTime());
        $this->addColumn('{{%leads}}', 'rating', $this->integer(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-lead_flow-employee', 'lead_flow');
        $this->dropForeignKey('fk-lead_flow-lead', 'lead_flow');
        $this->dropTable('lead_flow');

        $this->dropColumn('{{%leads}}', 'request_ip');
        $this->dropColumn('{{%leads}}', 'request_ip_detail');
        $this->dropColumn('{{%leads}}', 'offset_gmt');
        $this->dropColumn('{{%leads}}', 'snooze_for');
        $this->dropColumn('{{%leads}}', 'rating');
    }
}
