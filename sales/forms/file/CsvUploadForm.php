<?php

namespace sales\forms\file;

use yii\base\Model;

/**
 * Class CsvUploadForm
 *
 * @property $file
 * @property int $maxSize;
 */
class CsvUploadForm extends Model
{
    public $file;

    private int $maxSize = 1024 * 1024 * 10;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => ['csv'], 'checkExtensionByMimeType' => false, 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return ['file' => 'CSV File'];
    }

    public function setMaxSize(int $maxSize): void
    {
        $this->maxSize = $maxSize;
    }
}
