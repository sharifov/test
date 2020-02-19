<?php

use yii\db\Migration;

/**
 * Class m200219_072317_add_column_product_id_to_lead_call_expert
 */
class m200219_072317_add_column_product_id_to_lead_call_expert extends Migration
{

    public $tableName = 'lead_call_expert';
    public $columnName = 'lce_product_id';
    public $table;

    public function init()
    {
        parent::init();
        $this->table = '{{%'. $this->tableName .'}}';
    }

    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->addColumn($this->table, $this->columnName, $this->integer());

        $this->addForeignKey(
            'FK-' . $this->tableName . '-' . $this->columnName, $this->table, [$this->columnName],
            '{{%product}}', ['pr_id'], 'SET NULL', 'CASCADE');
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-' . $this->tableName . '-' . $this->columnName, $this->table);

        $this->dropColumn($this->table, $this->columnName);
    }
}
