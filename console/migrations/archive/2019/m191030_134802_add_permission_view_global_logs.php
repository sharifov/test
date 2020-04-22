<?php

use yii\db\Migration;

/**
 * Class m191030_134802_add_permission_view_global_logs
 */
class m191030_134802_add_permission_view_global_logs extends Migration
{
	/**
	 * @var array
	 */
	public $routes = [
		'/global-log/ajax-view-general-lead-log'
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
