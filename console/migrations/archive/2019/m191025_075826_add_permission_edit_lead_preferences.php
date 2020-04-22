<?php

use yii\db\Migration;

/**
 * Class m191025_075826_add_permission_edit_lead_preferences
 */
class m191025_075826_add_permission_edit_lead_preferences extends Migration
{
	/**
	 * @var array
	 */
	public $routes = [
		'/lead-view/ajax-edit-lead-preferences-modal-content',
		'/lead-view/ajax-edit-lead-preferences'
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
