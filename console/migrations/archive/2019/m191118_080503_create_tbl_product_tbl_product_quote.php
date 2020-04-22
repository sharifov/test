<?php

use yii\db\Migration;

/**
 * Class m191118_080503_create_tbl_product_tbl_product_quote
 */
class m191118_080503_create_tbl_product_tbl_product_quote extends Migration
{
    public $routes = [
        '/product/*',
        '/product-quote/*',
        '/order/*',
        '/offer/*',
        '/offer-product/*',
        '/invoice/*',
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


        $this->createTable('{{%offer}}',	[
            'of_id'                     => $this->primaryKey(),
            'of_gid'                    => $this->string(32)->notNull()->unique(),
            'of_uid'                    => $this->string(15)->unique(),
            'of_name'                   => $this->string(40),
            'of_lead_id'                => $this->integer()->notNull(),
            'of_status_id'              => $this->tinyInteger(),
            'of_owner_user_id'          => $this->integer(),
            'of_created_user_id'        => $this->integer(),
            'of_updated_user_id'        => $this->integer(),
            'of_created_dt'             => $this->dateTime(),
            'of_updated_dt'             => $this->dateTime()
        ], $tableOptions);


        $this->addForeignKey('FK-offer-of_lead_id', '{{%offer}}', ['of_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-offer-of_owner_user_id', '{{%offer}}', ['of_owner_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-offer-of_created_user_id', '{{%offer}}', ['of_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-offer-of_updated_user_id', '{{%offer}}', ['of_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createIndex('IND-offer-of_gid', '{{%offer}}', ['of_gid'], true);
        $this->createIndex('IND-offer-of_uid', '{{%offer}}', ['of_uid'], true);
        $this->createIndex('IND-offer-of_status_id', '{{%offer}}', ['of_status_id']);


        $this->createTable('{{%order}}',	[
            'or_id'                     => $this->primaryKey(),
            'or_gid'                    => $this->string(32)->notNull()->unique(),
            'or_uid'                    => $this->string(15)->unique(),
            'or_name'                   => $this->string(40),
            'or_lead_id'                => $this->integer()->notNull(),
            'or_description'            => $this->text(),
            'or_status_id'              => $this->tinyInteger(),
            'or_pay_status_id'          => $this->tinyInteger(),

            'or_app_total'              => $this->decimal(8,2),
            'or_app_markup'             => $this->decimal(8,2),
            'or_agent_markup'           => $this->decimal(8,2),


            'or_client_total'           => $this->decimal(8,2),
            'or_client_currency'        => $this->string(3),
            'or_client_currency_rate'   => $this->decimal(8,5),

            'or_owner_user_id'          => $this->integer(),
            'or_created_user_id'        => $this->integer(),
            'or_updated_user_id'        => $this->integer(),
            'or_created_dt'             => $this->dateTime(),
            'or_updated_dt'             => $this->dateTime()
        ], $tableOptions);


        $this->addForeignKey('FK-order-or_lead_id', '{{%order}}', ['or_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-order-pr_owner_user_id', '{{%order}}', ['or_owner_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-order-pr_created_user_id', '{{%order}}', ['or_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-order-pr_updated_user_id', '{{%order}}', ['or_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createIndex('IND-order-or_gid', '{{%order}}', ['or_gid'], true);
        $this->createIndex('IND-order-or_uid', '{{%order}}', ['or_uid'], true);
        $this->createIndex('IND-order-or_status_id', '{{%order}}', ['or_status_id']);
        $this->createIndex('IND-order-or_pay_status_id', '{{%order}}', ['or_pay_status_id']);


        $this->createTable('{{%product}}',	[
            'pr_id'                 => $this->primaryKey(),
            'pr_type_id'            => $this->integer()->notNull(),
            'pr_name'               => $this->string(40),
            'pr_lead_id'            => $this->integer()->notNull(),
            'pr_description'        => $this->text(),
            'pr_status_id'          => $this->tinyInteger(),

            'pr_service_fee_percent'    => $this->decimal(5,2),

            'pr_created_user_id'    => $this->integer(),
            'pr_updated_user_id'    => $this->integer(),
            'pr_created_dt'         => $this->dateTime(),
            'pr_updated_dt'         => $this->dateTime()
        ], $tableOptions);


        $this->addForeignKey('FK-product-pr_lead_id', '{{%product}}', ['pr_lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-product-pr_created_user_id', '{{%product}}', ['pr_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product-pr_updated_user_id', '{{%product}}', ['pr_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createIndex('IND-product-pr_status_id', '{{%product}}', ['pr_status_id']);


        $this->createTable('{{%product_quote}}',	[
            'pq_id'                => $this->primaryKey(),
            'pq_gid'               => $this->string(32)->notNull()->unique(),
            'pr_name'              => $this->string(40),
            'pq_product_id'        => $this->integer()->notNull(),
            'pq_order_id'           => $this->integer(),
            'pq_description'        => $this->text(),
            'pq_status_id'          => $this->tinyInteger(),

            'pq_price'              => $this->decimal(8,2),
            'pq_origin_price'       => $this->decimal(8,2),
            'pq_client_price'       => $this->decimal(8,2),
            'pq_service_fee_sum'    => $this->decimal(8,2),

            'pq_origin_currency'    => $this->string(3),
            'pq_client_currency'    => $this->string(3),

            'pq_origin_currency_rate'  => $this->decimal(8,5),
            'pq_client_currency_rate'  => $this->decimal(8,5),

            'pq_owner_user_id'      => $this->integer(),
            'pq_created_user_id'    => $this->integer(),
            'pq_updated_user_id'    => $this->integer(),
            'pq_created_dt'         => $this->dateTime(),
            'pq_updated_dt'         => $this->dateTime()
        ], $tableOptions);

        $this->createIndex('IND-product_quote-pq_gid', '{{%product_quote}}', ['pq_gid'], true);
        $this->createIndex('IND-product_quote-pq_status_id', '{{%product_quote}}', ['pq_status_id']);

        $this->addForeignKey('FK-product_quote-pq_origin_currency', '{{%product_quote}}', ['pq_origin_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote-pq_client_currency', '{{%product_quote}}', ['pq_client_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');

        $this->addForeignKey('FK-product_quote-pq_product_id', '{{%product_quote}}', ['pq_product_id'], '{{%product}}', ['pr_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-product_quote-pq_order_id', '{{%product_quote}}', ['pq_order_id'], '{{%order}}', ['or_id'], 'SET NULL', 'CASCADE');

        $this->addForeignKey('FK-product_quote-pq_owner_user_id', '{{%product_quote}}', ['pq_owner_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote-pq_created_user_id', '{{%product_quote}}', ['pq_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-product_quote-pq_updated_user_id', '{{%product_quote}}', ['pq_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');




        $this->createTable('{{%offer_product}}',	[
            'op_offer_id'               => $this->integer()->notNull(),
            'op_product_quote_id'       => $this->integer()->notNull(),
            'op_created_user_id'        => $this->integer(),
            'op_created_dt'             => $this->dateTime(),
        ], $tableOptions);


        $this->addPrimaryKey('PK-offer_product', '{{%offer_product}}', ['op_offer_id', 'op_product_quote_id']);

        $this->addForeignKey('FK-offer_product-op_offer_id', '{{%offer_product}}', ['op_offer_id'], '{{%offer}}', ['of_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-offer_product-op_product_quote_id', '{{%offer_product}}', ['op_product_quote_id'], '{{%product_quote}}', ['pq_id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-offer_product-op_created_user_id', '{{%offer_product}}', ['op_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');



        $this->createTable('{{%invoice}}',	[
            'inv_id'                    => $this->primaryKey(),
            'inv_gid'                   => $this->string(32)->notNull()->unique(),
            'inv_uid'                   => $this->string(15)->unique(),
            'inv_order_id'              => $this->integer()->notNull(),
            'inv_status_id'             => $this->tinyInteger(),

            'inv_sum'                   => $this->decimal(8,2)->notNull(),
            'inv_client_sum'            => $this->decimal(8,2)->notNull(),
            'inv_client_currency'       => $this->string(3),
            'inv_currency_rate'         => $this->decimal(8,5),

            'inv_description'           => $this->text(),

            'inv_created_user_id'       => $this->integer(),
            'inv_updated_user_id'       => $this->integer(),
            'inv_created_dt'            => $this->dateTime(),
            'inv_updated_dt'            => $this->dateTime()
        ], $tableOptions);


        $this->addForeignKey('FK-invoice-inv_lead_id', '{{%invoice}}', ['inv_order_id'], '{{%order}}', ['or_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-invoice-inv_client_currency', '{{%invoice}}', ['inv_client_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');

        $this->addForeignKey('FK-invoice-inv_created_user_id', '{{%invoice}}', ['inv_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-invoice-inv_updated_user_id', '{{%invoice}}', ['inv_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createIndex('IND-invoice-inv_gid', '{{%invoice}}', ['inv_gid'], true);
        $this->createIndex('IND-invoice-inv_status_id', '{{%invoice}}', ['inv_status_id']);


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

        $this->dropTable('{{%invoice}}');
        $this->dropTable('{{%offer_product}}');
        $this->dropTable('{{%product_quote}}');
        $this->dropTable('{{%product}}');
        $this->dropTable('{{%order}}');
        $this->dropTable('{{%offer}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);

        //Yii::$app->db->getSchema()->refreshTableSchema('{{%product_type}}');

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
