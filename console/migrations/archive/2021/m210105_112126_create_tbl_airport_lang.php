<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210105_112126_create_tbl_airport_lang
 */
class m210105_112126_create_tbl_airport_lang extends Migration
{
    private $routes = [
        '/airport-lang/*',
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

        $this->createTable('{{%airport_lang}}', [
            'ail_iata' => $this->string(3),
            'ail_lang' => $this->string(2),
            'ail_name' => $this->string(255),
            'ail_city' => $this->string(40),
            'ail_country' => $this->string(40),
            'ail_created_user_id' => $this->integer(),
            'ail_updated_user_id' => $this->integer(),
            'ail_created_dt' => $this->dateTime(),
            'ail_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addPrimaryKey('PK-airport_lang', '{{%airport_lang}}', ['ail_iata', 'ail_lang']);

        $this->addForeignKey(
            'FK-airport_lang-ail_iata',
            '{{%airport_lang}}',
            'ail_iata',
            '{{%airports}}',
            'iata',
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
         $this->dropTable('{{%airport_lang}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
