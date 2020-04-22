<?php

use yii\db\Migration;

/**
 * Class m191121_135916_add_permission_for_department_email_project
 */
class m191121_135916_add_permission_for_department_email_project extends Migration
{
	public $routes = [
		'/department-email-project/*',
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

			if(!$auth->hasChild($auth->getRole('admin'), $permission)) {
				$auth->addChild($auth->getRole('admin'), $permission);
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
			if ($permission = $auth->getPermission($route)) {
				$auth->remove($permission);
			}
		}

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
