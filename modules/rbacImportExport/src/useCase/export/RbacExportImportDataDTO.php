<?php
namespace modules\rbacImportExport\src\useCase\export;

use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\helpers\RbacDataHelper;

/**
 * Class RbacExportImportDataDTO
 * @package modules\rbacImportExport\src\useCase\export
 *
 * @property array $data
 * @property int $cntRoles
 * @property int $cntPermissions
 * @property int $cntRules
 * @property int $cntChild
 * @property int $type
 * @property string $fileName
 * @property int $fileSize
 */
class RbacExportImportDataDTO
{
	public $data;

	public $cntRoles = 0;

	public $cntPermissions = 0;

	public $cntRules = 0;

	public $cntChild = 0;

	public $type;

	public $fileName;

	public $fileSize;

	public function __construct(?int $type = null, ?int $cntRoles = null)
	{
		$this->type = $type;
		$this->cntRoles = $cntRoles;
		$this->fileName = 'rbac-export_' . time() . '.zip';
	}

	public function fillByModel(AuthImportExport $model): void
	{
		$this->data = RbacDataHelper::decode($model->aie_data);
		$this->fileName = $model->aie_file_name;
	}
}