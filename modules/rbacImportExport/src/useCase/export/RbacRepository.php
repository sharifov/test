<?php
namespace modules\rbacImportExport\src\useCase\export;

use modules\rbacImportExport\src\entity\AuthImportExport;

class RbacRepository
{
	public function find(int $id): AuthImportExport
	{
		$row = AuthImportExport::findOne($id);
		if (!$row) {
			throw new \RuntimeException('Data not found');
		}
		return $row;
	}

	public function save(AuthImportExport $authImportExport): int
	{
		if(!$authImportExport->save()) {
			throw new \RuntimeException($authImportExport->getErrorSummary(false)[0]);
		}
		return $authImportExport->aie_id;
	}
}