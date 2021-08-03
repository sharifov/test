<?php

namespace modules\flight\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use Yii;
use yii\db\Migration;

/**
 * Class m210802_131652_create_tbl_flight_request
 */
class m210802_131652_create_tbl_flight_request extends Migration
{
    private $routes = [
        '/flight-request-crud/index',
        '/flight-request-crud/create',
        '/flight-request-crud/view',
        '/flight-request-crud/update',
        '/flight-request-crud/delete',
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

        $this->createTable('{{%flight_request}}', [
            'fr_id' => $this->integer()->notNull(),
            'fr_hash' => $this->string(32),
            'fr_type_id' => $this->tinyInteger()->notNull(),
            'fr_data_json' => $this->json(),
            'fr_created_api_user_id' => $this->integer(),
            'fr_status_id' => $this->tinyInteger(),
            'fr_job_id' => $this->integer(),
            'fr_created_dt' => $this->dateTime()->notNull(),
            'fr_updated_dt' => $this->dateTime(),
            'fr_year' => $this->smallInteger()->notNull(),
            'fr_month' => $this->tinyInteger()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-flight_request', '{{%flight_request}}', ['fr_id', 'fr_year', 'fr_month']);

        $this->createIndex('IND-flight_request-fr_hash', '{{%flight_request}}', ['fr_hash']);
        $this->createIndex('IND-flight_request-fr_type_id', '{{%flight_request}}', ['fr_type_id']);
        $this->createIndex('IND-flight_request-fr_created_api_user_id', '{{%flight_request}}', ['fr_created_api_user_id']);
        $this->createIndex('IND-flight_request-fr_status_id', '{{%flight_request}}', ['fr_status_id']);
        $this->createIndex('IND-flight_request-fr_created_dt', '{{%flight_request}}', ['fr_created_dt']);
        $this->createIndex('IND-flight_request-fr_year', '{{%flight_request}}', ['fr_year']);
        $this->createIndex('IND-flight_request-fr_month', '{{%flight_request}}', ['fr_month']);

        Yii::$app->db->createCommand('ALTER TABLE `flight_request` PARTITION BY RANGE (`fr_year`)
            SUBPARTITION BY LINEAR HASH (`fr_month`)
            SUBPARTITIONS 12
            (
                PARTITION y21 VALUES LESS THAN (2021) ENGINE = InnoDB,
                PARTITION y22 VALUES LESS THAN (2022) ENGINE = InnoDB,
                PARTITION y23 VALUES LESS THAN (2023) ENGINE = InnoDB,
                PARTITION y24 VALUES LESS THAN (2024) ENGINE = InnoDB,
                PARTITION y25 VALUES LESS THAN (2025) ENGINE = InnoDB,
                PARTITION y26 VALUES LESS THAN (2026) ENGINE = InnoDB,
                PARTITION y27 VALUES LESS THAN (2027) ENGINE = InnoDB,
                PARTITION y28 VALUES LESS THAN (2028) ENGINE = InnoDB,
                PARTITION y29 VALUES LESS THAN (2029) ENGINE = InnoDB,
                PARTITION y30 VALUES LESS THAN (2030) ENGINE = InnoDB,
                PARTITION y VALUES LESS THAN MAXVALUE
            );
        ')->execute();

        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%flight_request}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
