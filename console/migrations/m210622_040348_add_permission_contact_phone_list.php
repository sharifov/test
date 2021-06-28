<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210622_040348_add_permission_contact_phone_list
 */
class m210622_040348_add_permission_contact_phone_list extends Migration
{
    private $routes = [
        '/contact-phone-list-crud/index',
        '/contact-phone-list-crud/create',
        '/contact-phone-list-crud/view',
        '/contact-phone-list-crud/update',
        '/contact-phone-list-crud/delete',

        '/contact-phone-data-crud/index',
        '/contact-phone-data-crud/create',
        '/contact-phone-data-crud/view',
        '/contact-phone-data-crud/update',
        '/contact-phone-data-crud/delete',

        '/contact-phone-service-info-crud/index',
        '/contact-phone-service-info-crud/create',
        '/contact-phone-service-info-crud/view',
        '/contact-phone-service-info-crud/update',
        '/contact-phone-service-info-crud/delete',

        '/tools/check-phone',
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
        (new RbacMigrationService())->up($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
        Yii::$app->cache->flush();
    }
}
