<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Class m220106_091158_add_tbl_user_feedback
 */
class m220106_091158_add_tbl_user_feedback extends Migration
{
    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    private array $routes = [
        '/user-feedback-crud/*',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $tableOptions = null;
//        if ($this->db->driverName === 'mysql') {
//            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
//        }

        $tableOptions = 'PARTITION BY RANGE (uf_created_dt)';

        $this->createTable('{{%user_feedback}}', [
            'uf_id' => $this->integer()->unsigned()->notNull()->append('generated always as identity'),
            'uf_type_id' => $this->tinyInteger(1)->notNull(),
            'uf_status_id' => $this->tinyInteger(1)->notNull(),
            'uf_title' => $this->string(255)->notNull(),
            'uf_message' => $this->text(),
            // 'uf_data_json' => $this->json(),
            'uf_data_json' => 'JSONB NOT NULL',
            'uf_created_dt' => $this->dateTime()->notNull(),
            'uf_updated_dt' => $this->dateTime(),
            'uf_created_user_id' => $this->integer(),
            'uf_updated_user_id' => $this->integer(),
        ], $tableOptions);


//        $this->addForeignKey(
//            'FK-user_feedback-uf_created_user_id',
//            '{{%user_feedback}}',
//            ['uf_created_user_id'],
//            '{{%employees}}',
//            ['id'],
//            'SET NULL',
//            'CASCADE'
//        );
//        $this->addForeignKey(
//            'FK-user_feedback-uf_updated_user_id',
//            '{{%user_feedback}}',
//            ['uf_updated_user_id'],
//            '{{%employees}}',
//            ['id'],
//            'SET NULL',
//            'CASCADE'
//        );


        $this->addPrimaryKey('PK-user_feedback-uf_id', '{{%user_feedback}}', ['uf_id', 'uf_created_dt']);
        $this->createIndex('IND-user_feedback-uf_created_dt', '{{%user_feedback}}', 'uf_created_dt');
        $this->createIndex('IND-user_feedback-uf_type_id', '{{%user_feedback}}', 'uf_type_id');
        $this->createIndex('IND-user_feedback-uf_status_id', '{{%user_feedback}}', 'uf_status_id');
        $this->createIndex('IND-user_feedback-uf_created_user_id', '{{%user_feedback}}', 'uf_created_user_id');

        $now = date_create("now");
        $dates = DBHelper::partitionDatesFrom($now);
        DBHelper::createMonthlyPartition($this->getDb(), 'user_feedback', $dates[0], $dates[1]);

        $tableOptions = null;

        $this->createTable('{{%user_feedback_file}}', [
            'uff_id' => $this->primaryKey()->unsigned(),
            'uff_uf_id' => $this->integer()->unsigned()->notNull(),
            'uff_mimetype' => $this->string(100)->notNull(),
            'uff_size' => $this->bigInteger()->unsigned(),
            'uff_filename' => $this->string(255),
            'uff_title' => $this->string(255),
//            'uff_blob' => $this->binary(16777215), // MEDIUMBLOB = 16 Mb
            'uff_blob' => 'BYTEA NOT NULL', // MEDIUMBLOB = 16 Mb
            'uff_created_dt' => $this->dateTime(),
            'uff_created_user_id' => $this->integer(),
        ], $tableOptions);

//        $this->addForeignKey(
//            'FK-user_feedback_file-uff_uf_id',
//            '{{%user_feedback_file}}',
//            ['uff_uf_id'],
//            '{{%user_feedback}}',
//            ['uf_id'],
//            'CASCADE',
//            'CASCADE'
//        );

        $this->createIndex(
            'IND-user_feedback_file-uff_uf_id',
            '{{%user_feedback_file}}',
            ['uff_uf_id']
        );

        $this->createIndex(
            'IND-user_feedback_file-uff_created_dt',
            '{{%user_feedback_file}}',
            ['uff_created_dt']
        );



//        $this->addForeignKey(
//            'FK-user_feedback_file-uff_created_user_id',
//            '{{%user_feedback_file}}',
//            ['uff_created_user_id'],
//            '{{%employees}}',
//            ['id'],
//            'SET NULL',
//            'CASCADE'
//        );

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
        $this->dropTable('{{%user_feedback_file}}');
        $this->dropTable('{{%user_feedback}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
