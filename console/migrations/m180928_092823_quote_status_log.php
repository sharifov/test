<?php
use yii\db\Migration;

/**
 * Class m180928_092823_quote_status_log
 */
class m180928_092823_quote_status_log extends Migration
{

    /**
     *
     * {@inheritdoc}
     *
     */
    public function up()
    {
        $this->createTable('quote_status_log', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer(),
            'quote_id' => $this->integer(),
            'status' => $this->integer(),
            'created' => $this->dateTime()
                ->defaultExpression('NOW()')
        ]);

        $this->addForeignKey('fk-quote_status_log-employee', 'quote_status_log', 'employee_id', 'employees', 'id');
        $this->addForeignKey('fk-quote_status_log-quote', 'quote_status_log', 'quote_id', 'quotes', 'id');
        $this->createIndex('idx-quote_status_log-status', 'quote_status_log', ['status','quote_id']);
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function down()
    {
        $this->dropForeignKey('fk-quote_status_log-employee', 'quote_status_log');
        $this->dropForeignKey('fk-quote_status_log-quote', 'quote_status_log');
        $this->dropIndex('idx-quote_status_log-status', 'quote_status_log');

        $this->dropTable('quote_status_log');
    }
}
