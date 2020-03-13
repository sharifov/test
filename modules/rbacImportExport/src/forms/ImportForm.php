<?php
namespace modules\rbacImportExport\src\forms;

use modules\rbacImportExport\src\traits\ModuleTrait;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportForm extends Model
{
	use ModuleTrait;

	/**
	 * @var UploadedFile
	 */
	public $zipFile;

	public function rules(): array
	{
		return [
			[['zipFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip', 'maxSize' => $this->getModule()->maxFileSizeUpload],
		];
	}
}