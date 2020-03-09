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
            ['file', 'file', 'skipOnEmpty' => true],
        ];
    }
}
