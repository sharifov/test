<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200219_072317_add_column_product_id_to_lead_call_expert
 */
class m200219_072317_add_column_product_id_to_lead_call_expert extends Migration
{

    public $routes = [
        '/product/product/update-ajax',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

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
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $this->addColumn($this->table, $this->columnName, $this->integer());

        $this->addForeignKey(
            'FK-' . $this->tableName . '-' . $this->columnName, $this->table, [$this->columnName],
            '{{%product}}', ['pr_id'], 'SET NULL', 'CASCADE');

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-' . $this->tableName . '-' . $this->columnName, $this->table);

        $this->dropColumn($this->table, $this->columnName);

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
