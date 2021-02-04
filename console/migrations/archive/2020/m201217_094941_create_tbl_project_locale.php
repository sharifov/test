<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201217_094941_create_tbl_project_locale
 */
class m201217_094941_create_tbl_project_locale extends Migration
{
    private $routes = [
        '/project-locale/*',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%project_locale}}', [
            'pl_project_id' => $this->integer(),
            'pl_language_id' => $this->string(5),
            'pl_default' => $this->boolean()->defaultValue(false),
            'pl_enabled' => $this->boolean()->defaultValue(true),
            'pl_params' => $this->json(),
            'pl_created_user_id' => $this->integer(),
            'pl_updated_user_id' => $this->integer(),
            'pl_created_dt' => $this->dateTime(),
            'pl_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addPrimaryKey('PK-project_locale', '{{%project_locale}}', ['pl_project_id', 'pl_language_id']);
        $this->createIndex('IDX-project_locale-pl_enabled', '{{%project_locale}}', 'pl_enabled');
        $this->createIndex('IDX-project_locale-pl_default', '{{%project_locale}}', 'pl_default');

        $this->addForeignKey(
            'FK-project_locale-pl_project_id',
            '{{%project_locale}}',
            'pl_project_id',
            '{{%projects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-project_locale-pl_language_id',
            '{{%project_locale}}',
            'pl_language_id',
            '{{%language}}',
            'language_id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-project_locale-pl_created_user_id',
            '{{%project_locale}}',
            'pl_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-project_locale-pl_updated_user_id',
            '{{%project_locale}}',
            'pl_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $this->dropTable('{{%project_locale}}');
        (new RbacMigrationService())->down($this->routes, $this->roles);

        if (\Yii::$app->cache) {
            \Yii::$app->cache->flush();
        }
    }
}
