<?php

namespace modules\fileStorage\src\useCase\fileStorage\rename;

use modules\fileStorage\src\entity\fileStorage\FileStorage;
use yii\base\Model;

/**
 * Class RenameForm
 *
 * @property $fs_id
 * @property $fs_name
 * @property $extension
 * @property $originalName
 */
class RenameForm extends Model
{
    public $fs_id;
    public $fs_name;
    private $extension;
    private $originalName;

    public function __construct(FileStorage $file, $config = [])
    {
        $this->fs_id = $file->fs_id;
        $pathInfo = pathinfo($file->fs_name);
        $this->originalName = $pathInfo['filename'];
        $this->fs_name = $this->originalName;
        $this->extension = $pathInfo['extension'] ?? null;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['fs_name', 'required'],
            ['fs_name', 'trim'],
            ['fs_name', 'string', 'max' => 90],
            ['fs_name', 'validateChangeName', 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function validateChangeName(): void
    {
        if ($this->fs_name === $this->originalName) {
            $this->addError('fs_name', 'Name has not changed.');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'fs_name' => 'Name',
        ];
    }

    public function getName(): string
    {
        if ($this->extension) {
            return $this->fs_name . '.' . $this->extension;
        }
        return $this->fs_name;
    }
}
