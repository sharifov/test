<?php

use yii\db\Migration;

/**
 * Class m191217_065524_create_tbl_product_option
 */
class m191217_065524_create_tbl_product_option extends Migration
{
    public $routes = [
        '/product-option/*',
        '/product-quote-option/*',
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

        $this->createTable('{{%product_option}}',	[
            'po_id'                     => $this->primaryKey(),
            'po_key'                    => $this->string(30)->unique()->notNull(),
            'po_product_type_id'        => $this->integer()->notNull(),
            'po_name'                   => $this->string(50)->notNull(),
            'po_description'            => $this->text(),
            'po_price_type_id'          => $this->tinyInteger(1)->defaultValue(1),
            'po_max_price'              => $this->decimal(8, 2),
            'po_min_price'              => $this->decimal(8, 2),
            'po_price'                  => $this->decimal(8, 2),
            'po_enabled'                => $this->boolean()->defaultValue(true),
            'po_created_user_id'        => $this->integer(),
            'po_updated_user_id'        => $this->integer(),
            'po_created_dt'             => $this->dateTime(),
            'po_updated_dt'             => $this->dateTime(),
        ], $tableOptions);


        $this->createIndex('IND-product_option-po_key', '{{%product_option}}', ['po_key']);

        //$this->addPrimaryKey('PK-order_product', '{{%order_product}}', ['orp_order_id', 'orp_product_quote_id']);

        $this->addForeignKey('FK-product_option-po_product_type_id', '{{%product_option}}', ['po_product_type_id'], '{{%product_type}}', ['pt_id'], 'CASCADE', 'CASCADE');

        //$this->addForeignKey('FK-product_option-orp_product_quote_id', '{{%product_option}}', ['orp_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-product_option-po_created_user_id', '{{%product_option}}', ['po_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_option-po_updated_user_id', '{{%product_option}}', ['po_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');



        $this->createTable('{{%product_quote_option}}',	[
            'pqo_id'                    => $this->primaryKey(),
            'pqo_product_quote_id'      => $this->integer()->notNull(),
            'pqo_product_option_id'     => $this->integer(),
            'pqo_name'                  => $this->string(50)->notNull(),
            'pqo_description'           => $this->text(),
            //'pqo_price'                 => $this->decimal(8, 2),
            'pqo_status_id'             => $this->tinyInteger(1),

            'pqo_price'                 => $this->decimal(8,2),
            'pqo_client_price'          => $this->decimal(8,2),
            'pqo_extra_markup'          => $this->decimal(8,2),

            'pqo_created_user_id'       => $this->integer(),
            'pqo_updated_user_id'       => $this->integer(),
            'pqo_created_dt'            => $this->dateTime(),
            'pqo_updated_dt'            => $this->dateTime(),
        ], $tableOptions);


        $this->createIndex('IND-product_quote_option-pqo_status_id', '{{%product_quote_option}}', ['pqo_status_id']);
        $this->createIndex('IND-product_quote_option-pqo_created_user_id', '{{%product_quote_option}}', ['pqo_created_user_id']);

        $this->addForeignKey('FK-product_quote_option-pqo_product_quote_id', '{{%product_quote_option}}', ['pqo_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-product_quote_option-pqo_product_option_id', '{{%product_quote_option}}', ['pqo_product_option_id'], '{{%product_option}}', ['po_id'], 'SET NULL', 'CASCADE');

        $this->addForeignKey('FK-product_quote_option-pqo_created_user_id', '{{%product_quote_option}}', ['pqo_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote_option-pqo_updated_user_id', '{{%product_quote_option}}', ['pqo_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


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
        $this->dropTable('{{%product_quote_option}}');
        $this->dropTable('{{%product_option}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);

        //Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
