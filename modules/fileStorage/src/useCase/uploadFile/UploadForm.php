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
 * @property $fs_title
 */
class UploadForm extends Model
{
    public $file;
    public $fs_title;

    public function rules(): array
    {
        return [
            ['file', 'required'],
            ['file', FileValidator::class],
            ['file', 'validateName','skipOnError' => true],

            ['fs_title', 'default', 'value' => null],
            ['fs_title', 'string', 'max' => 100],
        ];
    }

    public function validateName(): void
    {
        /** @var UploadedFile $file */
        $file = $this->file;

        $validator = new StringValidator([
            'min' => 1,
            'max' => 100,
        ]);
        if (!$validator->validate($file->name, $error)) {
            $this->addError('file', 'Filename: ' . $error);
        }
    }

    public function attributeLabels(): array
    {
        return [
            'fs_title' => 'Title',
        ];
    }
}
