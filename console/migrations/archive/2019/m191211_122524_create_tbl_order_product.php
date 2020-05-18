<?php

use yii\db\Migration;

/**
 * Class m191211_122524_create_tbl_order_product
 */
class m191211_122524_create_tbl_order_product extends Migration
{
    public $routes = [
        '/order-product/*',
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

        $this->createTable('{{%order_product}}',	[
            'orp_order_id'               => $this->integer()->notNull(),
            'orp_product_quote_id'       => $this->integer()->notNull(),
            'orp_created_user_id'        => $this->integer(),
            'orp_created_dt'             => $this->dateTime(),
        ], $tableOptions);


        $this->addPrimaryKey('PK-order_product', '{{%order_product}}', ['orp_order_id', 'orp_product_quote_id']);

        $this->addForeignKey('FK-order_product-orp_order_id', '{{%order_product}}', ['orp_order_id'], '{{%order}}', ['or_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-order_product-orp_product_quote_id', '{{%order_product}}', ['orp_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-order_product-orp_created_user_id', '{{%order_product}}', ['orp_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);

        //Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order_product}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);

        //Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
