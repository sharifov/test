<?php

namespace sales\model\lead\useCases\lead\import;

use yii\base\Model;

/**
 * Class LeadImportUploadForm
 *
 * @property $file
 */
class LeadImportUploadForm extends Model
{
    public $file;

    public function rules(): array
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => ['csv'], 'checkExtensionByMimeType'=>false, 'maxSize' => 1024 * 1024 * 10],
        ];
    }
}
