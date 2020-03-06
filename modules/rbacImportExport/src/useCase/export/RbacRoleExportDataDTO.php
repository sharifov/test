<?php
namespace modules\rbacImportExport\src\useCase\export;

use yii\rbac\Permission;
use yii\rbac\Role;

/**
 * Class RbacRoleExportDataDTO
 * @package modules\rbacImportExport\src\useCase\export
 *
 * @property array $roleInfo
 * @property array $userIdsByRole
 * @property array $permissionsByRole
 * @property array $childByRole
 * @property Role $rulesByRole
 */
class RbacRoleExportDataDTO
{
	public $roleInfo;
	public $userIdsByRole;
	public $permissionsByRole;
	public $childByRole;
//	public $rulesByRole;

	public function __construct(array $userIdsByRole, array $permissionsByRole, array $childByRole, Role $roleInfo)
	{
		$this->userIdsByRole = $userIdsByRole;
		$this->permissionsByRole = $permissionsByRole;
		$this->childByRole = $childByRole;
//		$this->rulesByRole = $rulesByRole;
		$this->roleInfo = $roleInfo;
	}
}