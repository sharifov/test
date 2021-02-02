<?php

namespace modules\fileStorage\src\entity\fileLog;

use modules\fileStorage\src\entity\fileShare\FileShare;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use Yii;

/**
 * This is the model class for table "{{%file_log}}".
 *
 * @property int $fl_id
 * @property int|null $fl_fs_id
 * @property int|null $fl_fsh_id
 * @property int|null $fl_type_id
 * @property string|null $fl_created_dt
 * @property string|null $fl_ip_address
 * @property string|null $fl_user_agent
 *
 * @property FileStorage $file
 * @property FileShare $share
 */
class FileLog extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['fl_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['fl_fs_id', 'required'],
            ['fl_fs_id', 'integer', 'min' => 1, 'max' => 2147483647, 'tooBig' => '{attribute} is out of range for type integer'],
            ['fl_fs_id', 'exist', 'skipOnError' => true, 'targetClass' => FileStorage::class, 'targetAttribute' => ['fl_fs_id' => 'fs_id']],

            ['fl_fsh_id', 'default', 'value' => null],
            ['fl_fsh_id', 'integer'],
            ['fl_fsh_id', 'exist', 'skipOnError' => true, 'targetClass' => FileShare::class, 'targetAttribute' => ['fl_fsh_id' => 'fsh_id']],

            ['fl_ip_address', 'ip'],

            ['fl_type_id', 'default', 'value' => null],
            ['fl_type_id', 'integer'],
            ['fl_type_id', 'in', 'range' => array_keys(FileLogType::getList())],

            ['fl_user_agent', 'string', 'max' => 500],
        ];
    }
    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileStorage::class, ['fs_id' => 'fl_fs_id']);
    }
    public function getShare(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileShare::class, ['fsh_id' => 'fl_fsh_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fl_id' => 'ID',
            'fl_fs_id' => 'File ID',
            'fl_fsh_id' => 'Share ID',
            'fl_type_id' => 'Type',
            'fl_created_dt' => 'Created Dt',
            'fl_ip_address' => 'Ip Address',
            'fl_user_agent' => 'User Agent',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%file_log}}';
    }

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('db_postgres');
    }
}
