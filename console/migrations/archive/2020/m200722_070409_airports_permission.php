<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200722_070409_airports_permission
 */
class m200722_070409_airports_permission extends Migration
{
    public $route = [
        '/airports/*',
        '/airports/synchronization',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%airports}}', 'a_icao', $this->string(4));
        $this->addColumn('{{%airports}}', 'a_country_code', $this->string(2));
        $this->addColumn('{{%airports}}', 'a_city_code', $this->string(3));
        $this->addColumn('{{%airports}}', 'a_state', $this->string(80));
        $this->addColumn('{{%airports}}', 'a_rank', $this->decimal(15, 1));
        $this->addColumn('{{%airports}}', 'a_multicity', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%airports}}', 'a_close', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%airports}}', 'a_disabled', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%airports}}', 'a_created_dt', $this->dateTime());
        $this->addColumn('{{%airports}}', 'a_updated_dt', $this->dateTime());
        $this->addColumn('{{%airports}}', 'a_created_user_id', $this->integer());
        $this->addColumn('{{%airports}}', 'a_updated_user_id', $this->integer());

        $this->addForeignKey('FK-airports-a_created_user_id', '{{%airports}}', ['a_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-airports-a_updated_user_id', '{{%airports}}', ['a_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createIndex('IND-airports-a_disabled', '{{%airports}}', ['a_disabled']);
        $this->createIndex('IND-airports-a_close', '{{%airports}}', ['a_close']);

        $this->createIndex('IND-airports-name', '{{%airports}}', ['name']);
        $this->createIndex('IND-airports-city', '{{%airports}}', ['city']);
        $this->createIndex('IND-airports-country', '{{%airports}}', ['country']);

        $this->dropColumn('{{%airports}}', 'countryId');

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%airports}}', 'a_icao');
        $this->dropColumn('{{%airports}}', 'a_country_code');
        $this->dropColumn('{{%airports}}', 'a_city_code');
        $this->dropColumn('{{%airports}}', 'a_state');
        $this->dropColumn('{{%airports}}', 'a_rank');
        $this->dropColumn('{{%airports}}', 'a_multicity');
        $this->dropColumn('{{%airports}}', 'a_close');
        $this->dropColumn('{{%airports}}', 'a_disabled');
        $this->dropColumn('{{%airports}}', 'a_created_dt');
        $this->dropColumn('{{%airports}}', 'a_updated_dt');
        $this->dropColumn('{{%airports}}', 'a_created_user_id');
        $this->dropColumn('{{%airports}}', 'a_updated_user_id');

        $this->dropIndex('IND-airports-name', '{{%airports}}');
        $this->dropIndex('IND-airports-city', '{{%airports}}');
        $this->dropIndex('IND-airports-country', '{{%airports}}');

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
