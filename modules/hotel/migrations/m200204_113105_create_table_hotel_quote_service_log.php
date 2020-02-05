<?php

namespace modules\hotel\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use Yii;
use yii\db\Migration;

/**
 * Class m200204_113105_create_table_hotel_quote_service_log
 */
class m200204_113105_create_table_hotel_quote_service_log extends Migration
{
    public $routes = [
        '/hotel/hotel-quote-service-log/*',
        '/hotel/hotel-quote-service-log-crud/*',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $tableName = '{{%hotel_quote_service_log}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'hqsl_id' => $this->primaryKey(),
            'hqsl_hotel_quote_id' => $this->integer()->notNull(),
            'hqsl_action_type_id' => $this->integer()->notNull(),
            'hqsl_status_id' => $this->integer()->notNull(),
            'hqsl_message' => $this->text(),
            'hqsl_created_user_id' => $this->integer(),
            'hqsl_created_dt' => $this->dateTime(),
            'hqsl_updated_dt' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'FK-hotel-quote-service-log_hotel-quote', $this->tableName,'hqsl_hotel_quote_id',
            '{{%hotel_quote}}','hq_id','CASCADE','CASCADE'
        );
        $this->addForeignKey(
            'FK-hotel-quote-service-log_employees', $this->tableName,'hqsl_created_user_id',
            '{{%employees}}','id','SET NULL','CASCADE'
        );

        (new RbacMigrationService())->up($this->routes, $this->roles);

        $this->_flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);

        $this->dropForeignKey('FK-hotel-quote-service-log_hotel-quote', $this->tableName);
        $this->dropForeignKey('FK-hotel-quote-service-log_employees', $this->tableName);

        $this->dropTable($this->tableName);

        $this->_flush();
    }

    private function _flush(): void
    {
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}
