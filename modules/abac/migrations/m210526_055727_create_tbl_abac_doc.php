<?php

namespace modules\abac\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use Yii;
use yii\db\Migration;

/**
 * Class m210526_055727_create_tbl_abac_doc
 */
class m210526_055727_create_tbl_abac_doc extends Migration
{
    private $routes = [
        '/abac-doc/view',
        '/abac-doc/index',
        '/abac-doc/create',
        '/abac-doc/updated',
        '/abac-doc/delete',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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

        $this->createTable('{{%abac_doc}}', [
            'ad_id' => $this->primaryKey(),
            'ad_file' => $this->string(100)->notNull(),
            'ad_line' => $this->integer()->notNull(),
            'ad_subject' => $this->string(50),
            'ad_object' => $this->string(50),
            'ad_action' => $this->string(50),
            'ad_description' => $this->string(50),
            'ad_created_dt' => $this->dateTime(),
        ], $tableOptions);

        (new RbacMigrationService())->up($this->routes, $this->roles);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%abac_doc}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
