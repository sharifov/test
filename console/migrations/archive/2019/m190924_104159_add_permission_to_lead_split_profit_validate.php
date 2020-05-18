<?php

use yii\db\Migration;

/**
 * Class m190924_104159_add_permission_to_lead_split_profit_validate
 */
class m190924_104159_add_permission_to_lead_split_profit_validate extends Migration
{
	public $routes = [
		'/lead/check-percentage-of-split-validation'
	];

	public $roles = [
		'agent', 'admin', 'supervision'
	];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$auth = Yii::$app->authManager;

		foreach ($this->routes as $route) {
			$permission = $auth->getPermission($route);
			if(!$permission) {
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
					//$auth->remove($permission);
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
