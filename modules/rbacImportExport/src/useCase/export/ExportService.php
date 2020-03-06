<?php
namespace modules\rbacImportExport\src\useCase\export;

use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\forms\ExportForm;
use modules\rbacImportExport\src\traits\ModuleTrait;
use sales\auth\Auth;
use yii\rbac\ManagerInterface;
use yii\rbac\Role;

/**
 * Class ExportService
 * @package modules\rbacImportExport\src\useCase\export
 *
 * @property RbacRepository $repository
 *
 * @property string $jsonFile
 * @property string $zipFile
 */
class ExportService
{
	use ModuleTrait;

	private const JSON_FILE_EXT = '.json';

	private $jsonFile;

	private $zipFile;

	private $usersByRole = [];

	private $permissionsByRole = [];

	private $childByRole = [];

	/**
	 * @var RbacRepository
	 */
	private $repository;

	public function __construct(RbacRepository $repository)
	{
		$this->repository = $repository;
	}

	public function create(ExportForm $form, ManagerInterface $authManager): AuthImportExport
	{
		$transaction = \Yii::$app->db->beginTransaction();
		$dto = new RbacExportDataDTO(
			AuthImportExport::TYPE_EXPORT,
			count($form->roles ?: [])
		);
		try {
			if (is_array($form->roles)) {
				foreach ($form->roles as $role) {
					$roleInfo = $authManager->getRole($role);

					if ($roleInfo === null) {
						continue;
					}

					$this->getRbacData($authManager, $form, $roleInfo);

					$rbacRoleDto = new RbacRoleExportDataDTO(
						$this->usersByRole,
						$this->permissionsByRole,
						$this->childByRole,
						$roleInfo
					);

					$dto->exportData['roles'][$roleInfo->name] = $rbacRoleDto;
					$dto->cntPermissions += count($rbacRoleDto->permissionsByRole);
					$dto->cntChild += count((array)$rbacRoleDto->childByRole);
				}
			}


			if (is_array($form->section) && in_array(AuthImportExport::SECTION_GENERAL_RULES, $form->section, false)) {
				$dto->exportData['rules'] = $authManager->getRules();
			}
			if (is_array($form->section) && in_array(AuthImportExport::SECTION_GENERAL_PERMISSION, $form->section, false)) {
				$dto->exportData['permissions'] = $authManager->getPermissions();
			}


			$dto->cntRules = count($authManager->getRules());
			$this->createZipFile($dto);
			$this->removeFiles();

			$authImportExport = AuthImportExport::create($dto);
			$this->repository->save($authImportExport);


			$transaction->commit();

			return $authImportExport;
		} catch (\Throwable $e) {
			$transaction->rollBack();
			throw $e;
		}
	}

	public function download(int $id): string
	{
		$model = $this->repository->find($id);

		$dto = new RbacExportDataDTO();
		$dto->fillByModel($model);

		$this->createZipFile($dto);

		return $this->zipFile;
	}

	private function createZipFile(RbacExportDataDTO $dto): void
	{
		$jsonFileName = $this->generateJSONFileName();
		$jsonFile = $this->createJSONFile($jsonFileName, json_encode($dto->exportData));

		$zipPath = ($this->getModule())->params['tmpDir'] . '/' . $dto->fileName;

		$zipArchive = new \ZipArchive();
		if($zipArchive->open($zipPath, \ZipArchive::CREATE) !== true) {
			throw new \RuntimeException('Cannot create a zip file');
		}

		$zipArchive->addFile($jsonFile, $jsonFileName);
		$zipArchive->close();

		$dto->fileSize = filesize($zipPath);

		$this->jsonFile = $jsonFile;
		$this->zipFile = $zipPath;
	}

	private function createJSONFile(string $fileName, string $data): string
	{
		$filePath = ($this->getModule())->params['tmpDir'] . '/' . $fileName;

		$file = fopen($filePath, 'wb');
		file_put_contents($filePath, $data);
		fclose($file);
		return $filePath;
	}

	private function generateJSONFileName(): string
	{
		return md5(uniqid('tmp_json', true)) . self::JSON_FILE_EXT;
	}

	public function removeFiles(): void
	{
		if ($this->jsonFile && file_exists($this->jsonFile)) {
			unlink($this->jsonFile);
		}
		if ($this->zipFile && file_exists($this->zipFile)) {
			unlink($this->zipFile);
		}
	}

	private function getRbacData(ManagerInterface $authManager, ExportForm $form, Role $roleInfo)
	{
		$this->usersByRole = [];
		$this->permissionsByRole = [];
		$this->childByRole = [];

		if (is_array($form->section)) {
			if (in_array(AuthImportExport::SECTION_USERS, $form->section, false)) {
				$this->usersByRole = $authManager->getUserIdsByRole($roleInfo->name);
			}
			if (in_array(AuthImportExport::SECTION_PERMISSIONS, $form->section, false)) {
				$this->permissionsByRole = $authManager->getPermissionsByRole($roleInfo->name);
			}
			if (in_array(AuthImportExport::SECTION_CHILD, $form->section, false)) {
				$this->childByRole = $authManager->getChildRoles($roleInfo->name);
			}
		}
	}
}