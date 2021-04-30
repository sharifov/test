<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210429_051628_create_tbl_project_relation
 */
class m210429_051628_create_tbl_project_relation extends Migration
{
    private $routes = [
        '/project-relation-crud/view',
        '/project-relation-crud/index',
        '/project-relation-crud/create',
        '/project-relation-crud/updated',
        '/project-relation-crud/delete',
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

        $this->createTable('{{%project_relation}}', [
            'prl_project_id' => $this->integer()->notNull(),
            'prl_related_project_id' => $this->integer()->notNull(),
            'prl_created_user_id' => $this->integer(),
            'prl_updated_user_id' => $this->integer(),
            'prl_created_dt' => $this->dateTime(),
            'prl_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addPrimaryKey('PK-project_relation-project_id-related', '{{%project_relation}}', ['prl_project_id', 'prl_related_project_id']);
        $this->addForeignKey(
            'FK-project_relation-prl_project_id-id',
            '{{%project_relation}}',
            ['prl_project_id'],
            '{{%projects}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-project_relation-prl_related_project_id-id',
            '{{%project_relation}}',
            ['prl_related_project_id'],
            '{{%projects}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-project_relation-prl_related_project_id-id', '{{%project_relation}}');
        $this->dropForeignKey('FK-project_relation-prl_project_id-id', '{{%project_relation}}');

        $this->dropTable('{{%project_relation}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
