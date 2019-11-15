<?php

use yii\db\Migration;

/**
 * Class m191114_080518_create_tbl_currency
 */
class m191114_080518_create_tbl_currency extends Migration
{
    public $routes = [
        '/currency/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%currency}}',	[
            'cur_code'           => $this->string(3)->notNull()->unique(),
            'cur_name'           => $this->string(34)->notNull(),
            'cur_symbol'         => $this->string(3)->notNull(),
            'cur_rate'           => $this->decimal(8, 5)->defaultValue(1),
            'cur_system_rate'    => $this->decimal(8, 5)->defaultValue(1),
            'cur_enabled'        => $this->boolean()->defaultValue(true),
            'cur_default'        => $this->boolean()->defaultValue(false),
            'cur_sort_order'     => $this->smallInteger()->defaultValue(3),
            'cur_created_dt'     => $this->dateTime(),
            'cur_updated_dt'     => $this->dateTime(),
            'cur_synch_dt'       => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-currency-cur_code', '{{%currency}}', ['cur_code']);

        $this->insert('{{%currency}}', [
            'cur_code'      =>   'USD',
            'cur_name'      =>   'US dollar',
            'cur_symbol'    => '$',
            'cur_rate'      => 1,
            'cur_enabled'   => true,
            'cur_default'        => true,
            'cur_created_dt'     => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%currency}}', [
            'cur_code'      =>   'EUR',
            'cur_name'      =>   'Euro',
            'cur_symbol'    =>    '€',
            'cur_rate'      =>   0.7,
            'cur_enabled'   => true,
            'cur_default'        => false,
            'cur_created_dt'     => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%currency}}', [
            'cur_code'      =>   'GBP',
            'cur_name'      =>   'Pounds sterling',
            'cur_symbol'    => '£',
            'cur_rate'      => 0.6,
            'cur_enabled'   => false,
            'cur_default'        => false,
            'cur_created_dt'     => date('Y-m-d H:i:s'),
        ]);

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%currency}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%currency}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%currency}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
