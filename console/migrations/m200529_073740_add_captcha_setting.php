<?php

use common\models\Employee;
use common\models\SettingCategory;
use console\migrations\RbacMigrationService;
use yii\db\Migration;
use yii\helpers\Inflector;

/**
 * Class m200529_073740_add_captcha_setting
 */
class m200529_073740_add_captcha_setting extends Migration
{
    public array $route = [
        '/user-failed-login/*',
    ];

    public array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($category = SettingCategory::findOne(['sc_name' => 'Two factor auth'])) {
            $category->sc_name = 'Authentication';
            $category->save();
        }

        $this->insert('{{%setting}}', [
            's_key' => 'captcha_login_enable',
            's_name' => 'Captcha Login Enable',
            's_type' => \common\models\Setting::TYPE_BOOL,
            's_value' => false,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);
        $this->insert('{{%setting}}', [
            's_key' => 'captcha_login_attempts',
            's_name' => 'Captcha Login attempts',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);
        $this->insert('{{%setting}}', [
            's_key' => 'user_notify_failed_login_attempts',
            's_name' => 'User Notify failed login attempts',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);
        $this->insert('{{%setting}}', [
            's_key' => 'user_block_attempts',
            's_name' => 'User Block attempts',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 0,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_category_id' => $category ? $category->sc_id : null,
        ]);

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_failed_login}}',	[
            'ufl_id' => $this->primaryKey(),
            'ufl_username' => $this->string(150),
            'ufl_user_id' => $this->integer(),
            'ufl_ua' => $this->string(200),
            'ufl_ip' => $this->string(40)->notNull(),
            'ufl_session_id' => $this->string(100),
            'ufl_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('IND-user_failed_login-active', '{{%user_failed_login}}', 'ufl_active');
        $this->createIndex('IND-user_failed_login-ufl_ip', '{{%user_failed_login}}', 'ufl_ip');
        $this->addForeignKey('FK-user_failed_login-ufl_user_id',
            '{{%user_failed_login}}', ['ufl_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        (new RbacMigrationService())->up($this->route, $this->roles);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($category = SettingCategory::findOne(['sc_name' => 'Authentication'])) {
            $category->sc_name = 'Two factor auth';
            $category->save();
        }

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'captcha_login_enable', 'captcha_login_attempts', 'user_notify_failed_login_attempts', 'user_block_attempts',
        ]]);

        $this->dropIndex('IND-user_failed_login-active', '{{%user_failed_login}}');
        $this->dropIndex('IND-user_failed_login-ufl_ip', '{{%user_failed_login}}');
        $this->dropTable('{{%user_failed_login}}');

        (new RbacMigrationService())->down($this->route, $this->roles);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
