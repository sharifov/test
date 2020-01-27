<?php

namespace modules\offer\migrations;

use Yii;
use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200125_145013_create_permissions_offer_module
 */
class m200125_145013_create_permissions_offer_module extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/offer/offer-crud/index', '/offer/offer-crud/create', '/offer/offer-crud/view', '/offer/offer-crud/delete', '/offer/offer-crud/update',
        '/offer/offer/create-ajax', '/offer/offer/delete-ajax', '/offer/offer/list-menu-ajax', '/offer/offer/update-ajax',

        '/offer/offer-product-crud/create', '/offer/offer-product-crud/delete', '/offer/offer-product-crud/index', '/offer/offer-product-crud/update', '/offer/offer-product-crud/view',
        '/offer/offer-product/create-ajax', '/offer/offer-product/delete-ajax',
    ];

    public $oldPermissions = [
        '/offer/create', '/offer/create-ajax', '/offer/delete', '/offer/delete-ajax', '/offer/index', '/offer/list-menu-ajax', '/offer/update', '/offer/update-ajax', '/offer/view',

        '/offer-product/create', '/offer-product/create-ajax', '/offer-product/delete', '/offer-product/delete-ajax', '/offer-product/index', '/offer-product/update', '/offer-product/view',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->oldPermissions as $item) {
            if ($permission = $auth->getPermission($item)) {
                $auth->remove($permission);
            }
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);

        $auth = Yii::$app->authManager;

        foreach ($this->oldPermissions as $item) {
            if ($permission = $auth->createPermission($item)) {
                $auth->add($permission);
                foreach ($this->roles as $role) {
                    $r = $auth->getRole($role);
                    $auth->addChild($r, $permission);
                }
            }
        }
    }
}
