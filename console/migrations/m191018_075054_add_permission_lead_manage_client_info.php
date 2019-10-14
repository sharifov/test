<?php

use yii\db\Migration;

/**
 * Class m191018_075054_add_permission_lead_manage_client_info
 */
class m191018_075054_add_permission_lead_manage_client_info extends Migration
{
	/**
	 * @var array
	 */
	public $routes = [
		'/lead-view/ajax-add-client-phone-modal-content',
		'/lead-view/ajax-update-client-phones',
		'/lead-view/ajax-add-client-phone-validation',
		'/lead-view/ajax-edit-client-phone-modal-content',
		'/lead-view/ajax-edit-client-phone-validation',
		'/lead-view/ajax-edit-client-phone',
		'/lead-view/ajax-add-client-email-modal-content',
		'/lead-view/ajax-add-client-email-validation',
		'/lead-view/ajax-add-client-email',
		'/lead-view/ajax-edit-client-email-modal-content',
		'/lead-view/ajax-edit-client-email-validation',
		'/lead-view/ajax-edit-client-email',
		'/lead-view/ajax-edit-client-name-modal-content',
		'/lead-view/ajax-edit-client-name-validation',
		'/lead-view/ajax-edit-client-name'
	];

	/**
	 * @var array
	 */
	public $roles = [
		'admin', 'agent', 'supervision', 'ex_agent', 'ex_super'
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$auth = Yii::$app->authManager;

		foreach ($this->routes as $route) {

			if (!$permission = $auth->getPermission($route)) {
				$permission = $auth->createPermission($route);
				$auth->add($permission);
			}

			foreach ($this->roles as $role) {
				if (!$auth->hasChild($auth->getRole($role), $permission)) {
					$auth->addChild($auth->getRole($role), $permission);
				}
			}
		}

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$auth = Yii::$app->authManager;

		foreach ($this->routes as $route) {
			foreach ($this->roles as $role) {
				if ($permission = $auth->getPermission($route)) {
					if ($auth->hasChild($auth->getRole($role), $permission)) {
						$auth->removeChild($auth->getRole($role), $permission);
					}
				}
			}
		}

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
