<?php
namespace modules\rbacImportExport\src\useCase\export;

use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\forms\ExportForm;
use modules\rbacImportExport\src\helpers\RbacDataHelper;
use modules\rbacImportExport\src\traits\ModuleTrait;
use yii\rbac\Role;

/**
 * Class ExportService
 * @package modules\rbacImportExport\src\useCase\export
 *
 * @property RbacRepository $repository
 *
 * @property string $binFile
 * @property string $zipFile
 */
class ExportService
{
	use ModuleTrait;

	private const FILE_EXT = '';

	private $binFile;

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
		$this->authManager = $this->getAuthManager();
	}

	public function create(ExportForm $form): AuthImportExport
	{
		$transaction = \Yii::$app->db->beginTransaction();
		$dto = new RbacExportImportDataDTO(
			AuthImportExport::TYPE_EXPORT,
			count($form->roles ?: [])
		);
		try {
			if (is_array($form->roles)) {
				foreach ($form->roles as $role) {
					$roleInfo = $this->authManager->getRole($role);

					if ($roleInfo === null) {
						continue;
					}

					$this->getRbacData($form, $roleInfo);

					$rbacRoleDto = new RbacRoleExportDataDTO(
						$this->usersByRole,
						$this->permissionsByRole,
						$this->childByRole,
						$roleInfo
					);

					$dto->data['roles'][$roleInfo->name] = $rbacRoleDto;
					$dto->cntChild += count((array)$rbacRoleDto->childByRole);
				}
			}


			if (is_array($form->section) && in_array(AuthImportExport::SECTION_GENERAL_RULES, $form->section, false)) {
				$dto->data['rules'] = $this->authManager->getRules();
			}
			if (is_array($form->section) && in_array(AuthImportExport::SECTION_GENERAL_PERMISSION, $form->section, false)) {
				$dto->data['permissions'] = $this->authManager->getPermissions();
			}

			$dto->cntRules = count($dto->data['rules'] ?? []);
			$dto->cntPermissions = count($dto->data['permissions'] ?? []);
			$this->createZipFile($dto);
			$this->removeFiles();

			$authImportExport = AuthImportExport::create($dto);
			$this->repository->save($authImportExport);

			$transaction->commit();

			return $authImportExport;
		} catch (\Throwable $e) {
			$transaction->rollBack();
			$this->removeFiles();
			throw $e;
		}
	}

	public function download(int $id): string
	{
		$model = $this->repository->find($id);

		$dto = new RbacExportImportDataDTO();
		$dto->fillByModel($model);

		$this->createZipFile($dto);

		return $this->zipFile;
	}

	private function createZipFile(RbacExportImportDataDTO $dto): void
	{
		$binFileName = $this->generateBINFileName();

		$binFile = $this->createBINFile($binFileName, RbacDataHelper::encode($dto->data));

		$zipPath = ($this->getModule())->params['tmpDir'] . '/' . $dto->fileName;

		$zipArchive = new \ZipArchive();
		if($zipArchive->open($zipPath, \ZipArchive::CREATE) !== true) {
			throw new \RuntimeException('Cannot create a zip file');
		}

		$zipArchive->addFile($binFile, $binFileName);
		$zipArchive->close();

		$dto->fileSize = RbacDataHelper::getFileSize($zipPath);

		$this->binFile = $binFile;
		$this->zipFile = $zipPath;
	}

	private function createBINFile(string $fileName, string $data): string
	{
		$filePath = ($this->getModule())->params['tmpDir'] . '/' . $fileName;

		$file = fopen($filePath, 'wb');
		file_put_contents($filePath, $data);
		fclose($file);
		return $filePath;
	}

	private function generateBINFileName(): string
	{
		return md5(uniqid('tmp_bin', true)) . self::FILE_EXT;
	}

	public function removeFiles(): void
	{
		if ($this->binFile && file_exists($this->binFile)) {
			unlink($this->binFile);
		}
		if ($this->zipFile && file_exists($this->zipFile)) {
			unlink($this->zipFile);
		}
	}

	private function getRbacData(ExportForm $form, Role $roleInfo): void
	{
		$this->usersByRole = [];
		$this->permissionsByRole = [];
		$this->childByRole = [];

		if (is_array($form->section)) {
			if (in_array(AuthImportExport::SECTION_USERS, $form->section, false)) {
				$this->usersByRole = $this->authManager->getUserIdsByRole($roleInfo->name);
			}
			if (in_array(AuthImportExport::SECTION_PERMISSIONS, $form->section, false)) {
				$this->permissionsByRole = $this->authManager->getPermissionsByRole($roleInfo->name);
			}
			if (in_array(AuthImportExport::SECTION_CHILD, $form->section, false)) {
				$this->childByRole = $this->authManager->getChildRoles($roleInfo->name);
			}
		}
	}
}