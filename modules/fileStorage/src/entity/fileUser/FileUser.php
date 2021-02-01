<?php

namespace modules\fileStorage\src\entity\fileUser;

use common\models\Employee;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use Yii;

/**
 * This is the model class for table "{{%file_user}}".
 *
 * @property int $fus_fs_id
 * @property int $fus_user_id
 *
 * @property FileStorage $file
 */
class FileUser extends \yii\db\ActiveRecord
{
    public static function create(int $fileId, int $userId): self
    {
        $file = new static();
        $file->fus_fs_id = $fileId;
        $file->fus_user_id = $userId;
        return $file;
    }

    public function rules(): array
    {
        return [
            [['fus_fs_id', 'fus_user_id'], 'unique', 'targetAttribute' => ['fus_fs_id', 'fus_user_id']],

            ['fus_fs_id', 'required'],
            ['fus_fs_id', 'integer', 'min' => 1, 'max' => 4294967295],
            ['fus_fs_id', 'exist', 'skipOnError' => true, 'targetClass' => FileStorage::class, 'targetAttribute' => ['fus_fs_id' => 'fs_id']],

            ['fus_user_id', 'required'],
            ['fus_user_id', 'integer', 'min' => 1, 'max' => 4294967295],
            ['fus_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['fus_user_id' => 'id']],
        ];
    }

    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileStorage::class, ['fs_id' => 'fus_fs_id']);
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'fus_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fus_fs_id' => 'File ID',
            'fus_user_id' => 'User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%file_user}}';
    }

    public static function getDb()
    {
        return Yii::$app->get('db_postgres');
    }
}
