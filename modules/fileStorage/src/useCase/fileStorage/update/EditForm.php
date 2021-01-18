<?php

namespace modules\fileStorage\src\useCase\fileStorage\update;

use modules\fileStorage\src\entity\fileStorage\FileStorage;
use yii\base\Model;

/**
 * Class EditForm
 *
 * @property $fs_id
 * @property $fs_title
 * @property $fs_private
 * @property $fs_expired_dt
 */
class EditForm extends Model
{
    public $fs_id;
    public $fs_title;
    public $fs_private;
    public $fs_expired_dt;

    public function __construct(FileStorage $file, $config = [])
    {
        $this->fs_id = $file->fs_id;
        $this->fs_title = $file->fs_title;
        $this->fs_private = $file->fs_private;
        $this->fs_expired_dt = $file->fs_expired_dt;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['fs_title', 'trim'],
            ['fs_title', 'string', 'max' => 100],

            ['fs_private', 'boolean'],

            ['fs_expired_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'fs_title' => 'Title',
            'fs_private' => 'Private',
            'fs_expired_dt' => 'Expired',
        ];
    }
}
