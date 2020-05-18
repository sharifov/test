<?php

use yii\db\Migration;

/**
 * Class m190815_084624_create_tbl_case_sale
 */
class m190815_084624_create_tbl_case_sale extends Migration
{

    public  $routes = ['/case-sale/*'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%case_sale}}', [
            'css_cs_id' => $this->integer()->notNull(),
            'css_sale_id' => $this->integer()->notNull(),
            'css_sale_book_id' => $this->string(8),
            'css_sale_pnr' => $this->string(8),
            'css_sale_pax' => $this->smallInteger(),
            'css_sale_created_dt' => $this->dateTime(),
            'css_sale_data' => $this->json()->notNull(),
            'css_created_user_id' => $this->integer(),
            'css_updated_user_id' => $this->integer(),
            'css_created_dt' => $this->dateTime(),
            'css_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-case_sale', '{{%case_sale}}', ['css_cs_id', 'css_sale_id']);
        $this->addForeignKey('FK-case_sale_css_cs_id', '{{%case_sale}}', ['css_cs_id'], '{{%cases}}', ['cs_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-case_sale_css_created_user_id', '{{%case_sale}}', ['css_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-case_sale_css_updated_user_id', '{{%case_sale}}', ['css_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        $auth = Yii::$app->authManager;
        $admin = $auth->getRole('admin');
        foreach ($this->routes as $route) {
            $permission = $auth->createPermission($route);
            $auth->add($permission);
            $auth->addChild($admin, $permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-case_sale_css_cs_id', '{{%case_sale}}');
        $this->dropForeignKey('FK-case_sale_css_created_user_id', '{{%case_sale}}');
        $this->dropForeignKey('FK-case_sale_css_updated_user_id', '{{%case_sale}}');
        $this->dropPrimaryKey('PK-case_sale', '{{%case_sale}}');
        $this->dropTable('{{%case_sale}}');

        $auth = Yii::$app->authManager;
        foreach ($this->routes as $route) {
            $permission = $auth->getPermission($route);
            $auth->remove($permission);
        }

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
