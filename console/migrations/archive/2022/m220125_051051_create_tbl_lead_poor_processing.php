<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220125_051051_create_tbl_lead_poor_processing
 */
class m220125_051051_create_tbl_lead_poor_processing extends Migration
{
    private $routes = [
        '/lead-poor-processing-log-crud/view',
        '/lead-poor-processing-log-crud/index',
        '/lead-poor-processing-log-crud/create',
        '/lead-poor-processing-log-crud/update',
        '/lead-poor-processing-log-crud/delete',

        '/lead-poor-processing-data-crud/view',
        '/lead-poor-processing-data-crud/index',
        '/lead-poor-processing-data-crud/update',

        '/lead-poor-processing-crud/view',
        '/lead-poor-processing-crud/index',
        '/lead-poor-processing-crud/update',
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

        $this->createTable('{{%lead_poor_processing_data}}', [
            'lppd_id' => $this->primaryKey(),
            'lppd_enabled' => $this->boolean()->defaultValue(false),
            'lppd_key' => $this->string(50)->notNull()->unique(),
            'lppd_name' => $this->string(50)->notNull(),
            'lppd_description' => $this->string(500),
            'lppd_minute' => $this->integer()->notNull(),
            'lppd_params_json' => $this->json(),
            'lppd_updated_dt' => $this->dateTime(),
            'lppd_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-lead_poor_processing_data-lppd_updated_user_id',
            '{{%lead_poor_processing_data}}',
            ['lppd_updated_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%lead_poor_processing}}', [
            'lpp_lead_id' => $this->integer()->notNull(),
            'lpp_lppd_id' => $this->integer()->notNull(),
            'lpp_expiration_dt' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-lead_poor_processing', '{{%lead_poor_processing}}', ['lpp_lead_id', 'lpp_lppd_id']);
        $this->createIndex('IND-lead_poor_processing-lpp_expiration_dt', '{{%lead_poor_processing}}', 'lpp_expiration_dt');
        $this->addForeignKey(
            'FK-lead_poor_processing-lpp_lead_id',
            '{{%lead_poor_processing}}',
            ['lpp_lead_id'],
            '{{%leads}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-lead_poor_processing-lpp_lppd_id',
            '{{%lead_poor_processing}}',
            ['lpp_lppd_id'],
            '{{%lead_poor_processing_data}}',
            ['lppd_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%lead_poor_processing_log}}', [
            'lppl_id' => $this->bigPrimaryKey(),
            'lppl_lead_id' => $this->integer()->notNull(),
            'lppl_lppd_id' => $this->integer()->notNull(),
            'lppl_status' => $this->smallInteger()->notNull(),
            'lppl_owner_id' => $this->integer(),
            'lppl_created_dt' => $this->dateTime(),
            'lppl_updated_dt' => $this->dateTime(),
            'lppl_updated_user_id' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-lead_poor_processing_log-lppl_lead_id',
            '{{%lead_poor_processing_log}}',
            ['lppl_lead_id'],
            '{{%leads}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-lead_poor_processing_log-lppl_lppd_id',
            '{{%lead_poor_processing_log}}',
            ['lppl_lppd_id'],
            '{{%lead_poor_processing_data}}',
            ['lppd_id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'FK-lead_poor_processing_log-lppl_owner_id',
            '{{%lead_poor_processing_log}}',
            ['lppl_owner_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

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
        $this->dropForeignKey('FK-lead_poor_processing_data-lppd_updated_user_id', '{{%lead_poor_processing_data}}');
        $this->dropForeignKey('FK-lead_poor_processing-lpp_lead_id', '{{%lead_poor_processing}}');
        $this->dropForeignKey('FK-lead_poor_processing-lpp_lppd_id', '{{%lead_poor_processing}}');
        $this->dropForeignKey('FK-lead_poor_processing_log-lppl_lead_id', '{{%lead_poor_processing_log}}');
        $this->dropForeignKey('FK-lead_poor_processing_log-lppl_lppd_id', '{{%lead_poor_processing_log}}');

        $this->dropTable('{{%lead_poor_processing_data}}');
        $this->dropTable('{{%lead_poor_processing}}');
        $this->dropTable('{{%lead_poor_processing_log}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
