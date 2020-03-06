<?php
namespace modules\rbacImportExport\src\forms;

use modules\rbacImportExport\src\entity\AuthImportExport;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class ExportForm
 * @package modules\rbacImportExport\src\forms
 *
 * @property array $roles
 * @property array $section
 */
class ExportForm extends Model
{
	public $roles;

	public $section;

	public function rules(): array
	{
		$authManager = \Yii::$app->getAuthManager();
		$roles = $authManager->getRoles();

		return [
			[['roles', 'section'], 'safe'],
//			[['roles'], 'required'],
			['roles', 'each', 'rule' => ['in', 'range' => array_column(ArrayHelper::toArray($roles), 'name', 'name')]],
			['section', 'each', 'rule' => ['in', 'range' => array_keys(AuthImportExport::getSectionList())]],
		];
	}
}