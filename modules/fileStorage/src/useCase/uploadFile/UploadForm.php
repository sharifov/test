<?php

namespace modules\fileStorage\src\useCase\uploadFile;

use modules\fileStorage\src\validator\FileValidator;
use yii\base\Model;
use yii\validators\StringValidator;
use yii\web\UploadedFile;

/**
 * Class UploadForm
 *
 * @property $files
 * @property $fs_title
 */
class UploadForm extends Model
{
    public $files;
    public $fs_title;

    public function rules(): array
    {
        return [
            ['files', 'required'],
            ['files', FileValidator::class, 'maxFiles' => 3],
            ['files', 'validateName','skipOnError' => true],

            ['fs_title', 'default', 'value' => null],
            ['fs_title', 'string', 'max' => 100],
        ];
    }

    public function validateName(): void
    {
        foreach ($this->files as $file) {
            /** @var UploadedFile $file */
            $validator = new StringValidator([
                'min' => 1,
                'max' => 100,
            ]);
            if (!$validator->validate($file->name, $error)) {
                $this->addError('files', 'Filename: ' . $error);
            }
        }
    }

    public function attributeLabels(): array
    {
        return [
            'fs_title' => 'Title',
        ];
    }
}
