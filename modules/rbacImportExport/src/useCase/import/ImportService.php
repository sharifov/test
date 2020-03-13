<?php
namespace modules\rbacImportExport\src\useCase\import;

use modules\rbacImportExport\src\entity\AuthImportExport;
use modules\rbacImportExport\src\forms\ImportForm;
use modules\rbacImportExport\src\helpers\RbacDataHelper;
use modules\rbacImportExport\src\rbac\DbManager;
use modules\rbacImportExport\src\traits\ModuleTrait;
use modules\rbacImportExport\src\useCase\export\RbacExportImportDataDTO;
use modules\rbacImportExport\src\useCase\export\RbacRepository;
use modules\rbacImportExport\src\useCase\export\RbacRoleExportDataDTO;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\rbac\Role;
use yii\rbac\Rule;

/**
 * Class ImportService
 * @package modules\rbacImportExport\src\useCase\import
 *
 * @property string $generatedZipFilePath
 * @property string $generatedZipFileName
 * @property string $extractedFile
 * @property int $dataByteLength
 * @property RbacRepository $repository
 */
class ImportService
{
	use ModuleTrait;

	private const COMPARE_EXCEPT_FIELDS = [
		'createdAt',
		'updatedAt'
	];

	/**
	 * @var string
	 */
	public $generatedZipFilePath;

	/**
	 * @var string
	 */
	public $generatedZipFileName;

	/**
	 * @var int
	 */
	public $dataByteLength;

	/**
	 * @var string
	 */
	private $extractedFile;

	/**
	 * @var array
	 */
	private $rulesList = [];

	/**
	 * @var array
	 */
	private $warnings = [];

	/**
	 * @var RbacRepository
	 */
	private $repository;

	/**
	 * @var string
	 */
	public $fileSize;

	public function __construct(RbacRepository $repository)
	{
		$this->authManager = $this->getAuthManager();
		$this->repository = $repository;
	}

	/**
	 * @param ImportForm $importForm
	 * @return array|null
	 * @throws \Throwable
	 */
	public function getRbacDifference(ImportForm $importForm): array
	{
		try {
			$this->saveUploadedFile($importForm)->unzipArchive();

			$rbacImportedData = $this->getImportedData();

			$rbacCurrentData = $this->getCurrentRbacData();

			$this->fileSize = RbacDataHelper::getFileSize($this->generatedZipFilePath);

			$this->removeFiles();

			$differences = $this->compareData(ArrayHelper::toArray($rbacCurrentData), ArrayHelper::toArray($rbacImportedData));

			$this->dataByteLength = StringHelper::byteLength(serialize(ArrayHelper::toArray($rbacImportedData)));

			return $differences;
		} catch (\Throwable $e) {
			$this->removeFiles();
			throw $e;
		}
	}

	public function import(array $data, int $fileSize): void
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {

			$this->rulesList = $this->authManager->getRulesNames();
			$dto = new RbacExportImportDataDTO(
				AuthImportExport::TYPE_IMPORT,
				count($this->rulesList)
			);
			$dto->fileName = 'rbac-import_' . time() . '.zip';
			if (!empty($data['permissions'])) {
				$this->importPermissions($data['permissions']);
				$dto->cntPermissions = count($data['permissions']);
			}

			if (!empty($data['roles'])) {
				foreach ($data['roles'] as $roleName => $role) {
					$existRole = $this->authManager->getRole($roleName);

					if (!empty($role['roleInfo'])) {
						$existRole = $this->importRoleInfo($role['roleInfo'], $existRole);
					}

					if (!empty($role['permissionsByRole'])) {
						$this->importChildByRole($role['permissionsByRole'], $existRole);
						$dto->cntChild += count($role['permissionsByRole']);
					}

					if (!empty($role['childByRole'])) {
						$this->importChildRoles($role['childByRole'], $existRole);
					}

					if (!empty($role['userIdsByRole'])) {
						$this->importUserAssignment($role['userIdsByRole'], $existRole);
					}
				}
				$dto->cntRoles = count($data['roles']);
			}

			$dto->data = $data;
			$dto->fileSize = StringHelper::byteLength(RbacDataHelper::encode($dto->data));

			$authImportExport = AuthImportExport::create($dto);
			$this->repository->save($authImportExport);

			$transaction->commit();

			if (Yii::$app->cache) {
				Yii::$app->cache->flush();
			}
		} catch (\Throwable $e) {
			$transaction->rollBack();
			throw $e;
		}
	}

	private function saveUploadedFile(ImportForm $importForm): self
	{
		$tmpDir = ($this->getModule())->params['tmpDir'];
		$this->generatedZipFileName = $this->generateZipFileName()  . '.' . $importForm->zipFile->extension;
		$this->generatedZipFilePath = $tmpDir . '/' . $this->generatedZipFileName;
		$importForm->zipFile->saveAs($this->generatedZipFilePath);
		return $this;
	}

	private function generateZipFileName(): string
	{
		return md5(uniqid('zip_', true));
	}

	private function getImportedData(): RbacImportedDataDTO
	{
		$data = RbacDataHelper::decode((file_get_contents($this->extractedFile)));

		return new RbacImportedDataDTO($data);
	}

	private function unzipArchive(): ImportService
	{
		if (file_exists($this->generatedZipFilePath)) {
			$zip = new \ZipArchive();
			$res = $zip->open($this->generatedZipFilePath);
			if ($res !== true) {
				throw new \RuntimeException('Unzip false');
			}

			$tmpDir = ($this->getModule())->params['tmpDir'];
			for($i=0; $i < $zip->numFiles; $i++) {

				$extractedFileName = $zip->getNameIndex($i);

				if ( $this->validateExtractedExtension($extractedFileName) ) {
					$zip->extractTo($tmpDir);
					$this->extractedFile = $tmpDir . '/' . $extractedFileName;
					break;
				}
			}

			if (empty($this->extractedFile)) {
				throw new \RuntimeException('There is no BIN file with data in the archive.');
			}

			$zip->close();
		}

		return $this;
	}

	private function validateExtractedExtension($filename): bool
	{
		return preg_match('/^[A-Za-z0-9]+$/', $filename);
	}

	private function getCurrentRbacData(): array
	{
		/**@var DbManager $manager */
		$manager = $this->getModule()->authManager;

		$roles = $manager->getRoles();
		$dto = new RbacExportImportDataDTO(
			AuthImportExport::TYPE_EXPORT,
			count($roles ?: [])
		);


		foreach ($roles as $role) {
			$roleInfo = $manager->getRole($role->name);

			if ($roleInfo === null) {
				continue;
			}

			$usersByRole = $manager->getUserIdsByRole($roleInfo->name);
			$permissionsByRole = $manager->getPermissionsByRole($roleInfo->name);
			$childByRole = $manager->getChildRoles($roleInfo->name);

			$rbacRoleDto = new RbacRoleExportDataDTO(
				$usersByRole,
				$permissionsByRole,
				$childByRole,
				$roleInfo
			);

			$dto->data['roles'][$roleInfo->name] = $rbacRoleDto;
			$dto->cntPermissions += count($rbacRoleDto->permissionsByRole);
			$dto->cntChild += count((array)$rbacRoleDto->childByRole);
		}

		$dto->data['rules'] = $manager->getRules();
		$dto->data['permissions'] = $manager->getPermissions();

		return $dto->data;
	}

	private function compareData(array $oldData, array $newData): array
	{
		$difference = [];
		foreach ($newData as $firstKey => $firstValue) {
			if (is_array($firstValue)) {
				if (!array_key_exists($firstKey, $oldData) || !is_array($oldData[$firstKey])) {
					if (empty($firstValue) && empty($oldData[$firstKey])) {
						continue;
					}
					$difference[$firstKey] = $firstValue;
				} else {
					$newDiff = $this->compareData( $oldData[$firstKey], $firstValue);
					if (!empty($newDiff)) {
						$difference[$firstKey] = $newDiff;
					}
				}
			} elseif ((!RbacDataHelper::isAssoc($newData) && !RbacDataHelper::isAssoc($oldData))) {
				if (!in_array($firstKey, self::COMPARE_EXCEPT_FIELDS, false) && !in_array($firstValue, $oldData, false)) {
					$difference['action'] = 'update';
					$difference[$firstKey] = $firstValue;
				}
			} elseif (!in_array($firstKey, self::COMPARE_EXCEPT_FIELDS, false) && (!array_key_exists($firstKey, $oldData) || $oldData[$firstKey] != $firstValue)) {
				$difference['action'] = 'update';
				$difference[$firstKey] = $firstValue;
			}
		}
		return $difference;
	}

	private function importRules(array &$rules): void
	{
		$batch = [];
		foreach($rules as $ruleKey => $rule) {
			$class = $rule['class'] ?? '';

			if (!$this->authManager->getRule($rule['name'] ?? $ruleKey) && $class && $class !== '\__PHP_Incomplete_Class' && class_exists($class)) {
				/** @var Rule $class */
				$class = Yii::createObject($class);
				$class->name = $rule['name'];
				$class->createdAt = $rule['createdAt'];
				$class->updatedAt = $rule['updatedAt'];
				$batch[] = $class;
				unset($rules[$ruleKey]);
			}
		}

		if (!empty($batch)) {
			$this->authManager->addBatchRules($batch);
		}
	}

	private function importPermissions(array $permissions): void
	{
		$batch = [];
		$action = [];
		foreach ($permissions as $permissionKey => $permission) {

			$newPermission = $this->authManager->getPermission($permission['name'] ?? $permissionKey);

			if (!$newPermission) {
				$newPermission = $this->authManager->createPermission($permission['name'] ?? $permissionKey);
			}
			$ruleName = array_key_exists('ruleName', $permission) ? (string)$permission['ruleName'] : $this->checkIfRuleExists((string)$newPermission->ruleName);

			if ($ruleName !== null) {
				$newPermission->description = array_key_exists('description', $permission) ? $permission['description'] : $newPermission->description;
				$newPermission->ruleName = $ruleName ?: null;
				$newPermission->data = array_key_exists('data', $permission) ? $permission['data'] : $newPermission->data;
				$newPermission->createdAt = $permission['createdAt'] ?? $newPermission->createdAt;
				$newPermission->updatedAt = $permission['updatedAt'] ?? $newPermission->updatedAt;

				$batch[] = $newPermission;
				$action[$newPermission->name] = $permission['action'] ?? 'insert';
			}
		}
		$this->authManager->addBatchPermissions($batch, $action);
	}

	private function importRoleInfo(array $roleInfo, ?Role $role): Role
	{
		if (!$role) {
			$role = $this->authManager->createRole($roleInfo['name']);
		}

		$role->description = $roleInfo['description'] ?? null;
		$role->ruleName = $this->checkIfRuleExists($roleInfo['ruleName'] ?? '') ?: null;
		$role->data = $roleInfo['data'] ?? null;
		$role->createdAt = $roleInfo['createdAt'] ?? null;
		$role->updatedAt = $roleInfo['updatedAt'] ?? null;

		$this->authManager->add($role);

		return $role;
	}

	private function importChildByRole(array $child, ?Role $role): void
	{
		if ($role) {
			$batch = [];
			foreach ($child as $itemKey => $item) {
				$permission = $this->authManager->getPermission($itemKey);

				if ($permission && $this->authManager->canAddChild($role, $permission)) {
					$batch[] = [
						$role,
						$permission
					];
				}
			}

			if(!empty($batch)) {
				$this->authManager->addBatchChild($batch);
			}
		}
	}

	private function importChildRoles(array $childRoles, ?Role $role): void
	{
		if ($role) {
			$batch = [];
			foreach ($childRoles as $childRole) {
				$parentRole = $this->authManager->getRole($childRole['name']);

				if (!$parentRole) {
					$parentRole = $this->authManager->createRole($childRole['name']);
				}

				$parentRole->description = $childRole['description'] ?? null;
				$parentRole->ruleName = $this->checkIfRuleExists($childRole['ruleName'] ?? '') ?: null;
				$parentRole->data = $childRole['data'] ?? null;
				$parentRole->createdAt = $childRole['createdAt'] ?? null;
				$parentRole->updatedAt = $childRole['updatedAt'] ?? null;

				if ($this->authManager->canAddChild($role, $parentRole)) {
					$batch[] = [
						$role,
						$parentRole
					];
				}
			}
			if(!empty($batch)) {
				$this->authManager->addBatchChild($batch);
			}
		}
	}

	public function removeFiles(): void
	{
		if ($this->generatedZipFilePath && file_exists($this->generatedZipFilePath)) {
			unlink($this->generatedZipFilePath);
		}
		if ($this->extractedFile && file_exists($this->extractedFile)) {
			unlink($this->extractedFile);
		}
	}

	private function importUserAssignment(array $users, ?Role $role): void
	{
		if ($role) {
			$this->authManager->assignBatch($users, $role);
		}
	}

	private function checkIfRuleExists(string $ruleName): ?string
	{
		if (in_array($ruleName, $this->rulesList, false)) {
			return $ruleName;
		}

		if (!$ruleName) {
			return '';
		}
		return null;
	}
}