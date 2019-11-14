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
            'cur_name'           => $this->string(34)->unique(),
            'cur_symbol'         => $this->string(3),
            'cur_rate'           => $this->float(),
            'cur_enabled'        => $this->boolean()->defaultValue(true),
            'cur_default'        => $this->boolean()->defaultValue(false),
            'cur_created_dt'     => $this->dateTime(),
            'cur_updated_dt'     => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-currency-cur_code', '{{%currency}}', ['cur_code']);
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
