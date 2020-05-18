<?php
use yii\db\Migration;

/**
 * Class m200316_133613_add_permission_for_rbac_export_import
 */
class m200316_133613_add_permission_for_rbac_export_import extends Migration
{
	private $route = '/rbac-import-export/*';

	private $routesForDeleting = [
		'/rbac-import-export/default/index',
		'/rbac-import-export/export/import-view',
		'/rbac-import-export/import-export/delete',
		'/rbac-import-export/import-export/download',
		'/rbac-import-export/import-export/export-view',
		'/rbac-import-export/import-export/import-view',
		'/rbac-import-export/import-export/index',
		'/rbac-import-export/import-export/view',
		'/rbac-import-export/import/import-view',
	];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$authManager = Yii::$app->getAuthManager();

    	$permission = $authManager->createPermission($this->route);
    	$authManager->add($permission);

		(new \console\migrations\RbacMigrationService())->down($this->routesForDeleting, [\common\models\Employee::ROLE_SUPER_ADMIN, \common\models\Employee::ROLE_ADMIN]);

		foreach ($this->routesForDeleting as $route) {
			$permission = $authManager->getPermission($route);
			if ($permission) {
				$authManager->remove($permission);
			}
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$authManager = Yii::$app->getAuthManager();

		$permission = $authManager->getPermission($this->route);
		if ($permission) {
			$authManager->remove($permission);
		}
    }
}
