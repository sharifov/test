<?php

use yii\db\Migration;

/**
 * Class m191118_070030_create_tbl_product_type
 */
class m191118_070030_create_tbl_product_type extends Migration
{
    public $routes = [
        '/product-type/*',
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



        $this->createTable('{{%product_type}}',	[
            'pt_id'              => $this->integer()->notNull()->unique(),
            'pt_key'           => $this->string(20)->notNull()->unique(),
            'pt_name'           => $this->string(50)->notNull(),
            'pt_description'    => $this->text(),
            'pt_settings'       => $this->json(),
            'pt_enabled'        => $this->boolean()->defaultValue(false),
            'pt_service_fee_percent'  => $this->decimal(5,2),
            'pt_created_dt'     => $this->dateTime(),
            'pt_updated_dt'     => $this->dateTime()
        ], $tableOptions);

        $this->addPrimaryKey('PK-product_type-pt_id', '{{%product_type}}', ['pt_id']);

        $this->insert('{{%product_type}}', [
            'pt_id'             => 1,
            'pt_key'            => 'flight',
            'pt_name'           => 'Flight',
            'pt_enabled'        => false,
            'pt_service_fee_percent'    => 3.5,
            'pt_created_dt'     => date('Y-m-d H:i:s'),
            'pt_updated_dt'     => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%product_type}}', [
            'pt_id'             => 2,
            'pt_key'            => 'hotel',
            'pt_name'           => 'Hotel',
            'pt_enabled'        => false,
            'pt_service_fee_percent'    => 3.5,
            'pt_created_dt'     => date('Y-m-d H:i:s'),
            'pt_updated_dt'     => date('Y-m-d H:i:s'),
        ]);

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('{{%product_type}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);

        Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }


}
