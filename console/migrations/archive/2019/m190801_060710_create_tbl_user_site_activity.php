<?php

use yii\db\Migration;

/**
 * Class m190801_060710_create_tbl_user_site_activity
 */
class m190801_060710_create_tbl_user_site_activity extends Migration
{
    public $routes = ['/user-site-activity/*'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_site_activity}}', [
            'usa_id' => $this->primaryKey(),
            'usa_user_id' => $this->integer(),
            'usa_request_url' => $this->string(500),
            'usa_page_url' => $this->string(255),
            'usa_ip' => $this->string(40),
            'usa_request_type' => $this->smallInteger(),
            'usa_request_get' => $this->text(),
            'usa_request_post' => $this->text(),
            'usa_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-user_site_activity_usa_user_id', '{{%user_site_activity}}', ['usa_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->createIndex('IND-user_site_activity', '{{%user_site_activity}}', ['usa_user_id', 'usa_created_dt', 'usa_request_url']);
        $this->createIndex('IND-user_site_activity_usa_user_id_usa_created_dt', '{{%user_site_activity}}', ['usa_user_id', 'usa_created_dt']);
        $this->createIndex('IND-user_site_activity_usa_page_url', '{{%user_site_activity}}', ['usa_page_url']);


        $auth = Yii::$app->authManager;

        foreach ($this->routes as $route) {
            $permission = $auth->createPermission($route);
            $auth->add($permission);
            $auth->addChild($auth->getRole('admin'), $permission);
            //$auth->addChild($auth->getRole('agent'), $permission);
            //$auth->addChild($auth->getRole('supervision'), $permission);
            //$auth->addChild($auth->getRole('qa'), $permission);
            //$auth->addChild($auth->getRole('userManager'), $permission);
        }


        $this->insert('{{%setting}}', [
            's_key' => 'user_site_activity_time',
            's_name' => 'User site Activity time (seconds)',
            's_type' => 'int',
            's_value' => 60,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'user_site_activity_count',
            's_name' => 'User site Activity maximum count',
            's_type' => 'int',
            's_value' => 6,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'user_site_activity_log_history_days',
            's_name' => 'User site Activity Log History (days)',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 3,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);


        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_site_activity}}');

        $auth = Yii::$app->authManager;

        foreach ($this->routes as $route) {
            if ($permission = $auth->getPermission($route)) {
                $auth->remove($permission);
            }
        }

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'user_site_activity_time', 'user_site_activity_count', 'user_site_activity_log_history_days'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
