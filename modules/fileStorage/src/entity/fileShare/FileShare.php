<?php

namespace modules\fileStorage\src\entity\fileShare;

use common\models\Employee;
use modules\fileStorage\src\entity\fileLog\FileLog;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use Yii;

/**
 * This is the model class for table "{{%file_share}}".
 *
 * @property int $fsh_id
 * @property int|null $fsh_fs_id
 * @property string|null $fsh_code
 * @property string|null $fsh_expired_dt
 * @property string|null $fsh_created_dt
 * @property int|null $fsh_created_user_id
 *
 * @property FileLog[] $logs
 * @property FileStorage $file
 * @property Employee $createdUser
 */
class FileShare extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['fsh_code', 'required'],
            ['fsh_code', 'string', 'max' => 32, 'min' => 32],
            ['fsh_code', 'unique'],

            ['fsh_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['fsh_created_user_id', 'default', 'value' => null],
            ['fsh_created_user_id', 'integer'],
            ['fsh_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['fsh_created_user_id' => 'id']],

            ['fsh_expired_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['fsh_fs_id', 'required'],
            ['fsh_fs_id', 'integer'],
            ['fsh_fs_id', 'exist', 'skipOnError' => true, 'targetClass' => FileStorage::class, 'targetAttribute' => ['fsh_fs_id' => 'fs_id']],
        ];
    }

    public function getLogs(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FileLog::class, ['fl_fsh_id' => 'fsh_id']);
    }

    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileStorage::class, ['fs_id' => 'fsh_fs_id']);
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'fsh_created_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fsh_id' => 'ID',
            'fsh_fs_id' => 'File ID',
            'fsh_code' => 'Code',
            'fsh_expired_dt' => 'Expired Dt',
            'fsh_created_dt' => 'Created Dt',
            'fsh_created_user_id' => 'Created User',
            'createdUser.nickname' => 'Created User',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%file_share}}';
    }

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('db_postgres');
    }
}
