<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200901_061707_add_permissions_for_phone_line_crud_pages
 */
class m200901_061707_add_permissions_for_phone_line_crud_pages extends Migration
{
	private $routes = [
		'/phone-line-crud/index',
		'/phone-line-crud/create',
		'/phone-line-crud/view',
		'/phone-line-crud/update',
		'/phone-line-crud/delete',

		'/phone-line-phone-number-crud/index',
		'/phone-line-phone-number-crud/create',
		'/phone-line-phone-number-crud/update',
		'/phone-line-phone-number-crud/delete',
		'/phone-line-phone-number-crud/view',

		'/phone-line-user-assign-crud/index',
		'/phone-line-user-assign-crud/create',
		'/phone-line-user-assign-crud/update',
		'/phone-line-user-assign-crud/delete',
		'/phone-line-user-assign-crud/view',

		'/phone-line-user-group-crud/index',
		'/phone-line-user-group-crud/create',
		'/phone-line-user-group-crud/update',
		'/phone-line-user-group-crud/delete',
		'/phone-line-user-group-crud/view',

		'/user-personal-phone-number-crud/index',
		'/user-personal-phone-number-crud/create',
		'/user-personal-phone-number-crud/update',
		'/user-personal-phone-number-crud/delete',
		'/user-personal-phone-number-crud/view',
	];

	private $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		(new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		(new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
