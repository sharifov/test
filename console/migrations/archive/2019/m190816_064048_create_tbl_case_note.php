<?php

use yii\db\Migration;

/**
 * Class m190816_064048_create_tbl_case_note
 */
class m190816_064048_create_tbl_case_note extends Migration
{
    public  $routes = ['/case-note/*'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%case_note}}', [
            'cn_id' => $this->primaryKey(),
            'cn_cs_id' => $this->integer()->notNull(),
            'cn_user_id' => $this->integer(),
            'cn_text' => $this->text()->notNull(),
            'cn_created_dt' => $this->dateTime(),
            'cn_updated_dt' => $this->dateTime(),
        ], $tableOptions);


        $this->addForeignKey('FK-case_note_cn_cs_id', '{{%case_note}}', ['cn_cs_id'], '{{%cases}}', ['cs_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-case_note_cn_user_id', '{{%case_note}}', ['cn_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


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
        $this->dropForeignKey('FK-case_note_cn_cs_id', '{{%case_note}}');
        $this->dropForeignKey('FK-case_note_cn_user_id', '{{%case_note}}');
        $this->dropTable('{{%case_note}}');

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
