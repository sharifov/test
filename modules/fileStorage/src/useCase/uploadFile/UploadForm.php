<?php

namespace modules\fileStorage\src\useCase\uploadFile;

use modules\fileStorage\src\validator\FileValidator;
use yii\base\Model;
use yii\validators\StringValidator;
use yii\web\UploadedFile;

/**
 * Class UploadForm
 *
 * @property $file
 */
class UploadForm extends Model
{
    public $file;

    public function rules(): array
    {
        return [
            ['file', 'required'],
            ['file', FileValidator::class],
            ['file', 'validateName','skipOnError' => true],
        ];
    }

    public function validateName(): void
    {
        /** @var UploadedFile $file */
        $file = $this->file;

        $validator = new StringValidator([
            'max' => 100
        ]);
        if (!$validator->validate($file->name, $error)) {
            $this->addError('file', 'Filename: ' . $error);
        }
    }
}
