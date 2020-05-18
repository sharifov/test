<?php

use yii\db\Migration;

/**
 * Class m191031_095230_add_permission_global_log
 */
class m191031_095230_add_permission_global_log extends Migration
{
	/**
	 * @var array
	 */
	public $routes = [
		'/global-log/*'
	];

	/**
	 * @var array
	 */
	public $roles = [
		'admin'
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
