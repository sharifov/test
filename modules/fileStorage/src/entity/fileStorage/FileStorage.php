<?php

namespace modules\fileStorage\src\entity\fileStorage;

use modules\fileStorage\src\entity\fileCase\FileCase;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLog\FileLog;
use modules\fileStorage\src\entity\fileShare\FileShare;
use modules\fileStorage\src\entity\fileUser\FileUser;
use Yii;

/**
 * This is the model class for table "{{%file_storage}}".
 *
 * @property int $fs_id
 * @property string|null $fs_uid
 * @property string|null $fs_mime_type
 * @property string|null $fs_name
 * @property string|null $fs_title
 * @property string|null $fs_path
 * @property int|null $fs_size
 * @property bool|null $fs_private
 * @property string $fs_md5_hash
 * @property string|null $fs_expired_dt
 * @property string|null $fs_created_dt
 *
 * @property FileCase[] $cases
 * @property FileClient[] $clients
 * @property FileLead[] $leads
 * @property FileLog[] $logs
 * @property FileShare[] $shares
 * @property FileUser[] $users
 */
class FileStorage extends \yii\db\ActiveRecord
{
    public static function createByLead(
        string $name,
        Path $path,
        int $size,
        Uid $uid,
        string $mimeType,
        string $hash,
        \DateTimeImmutable $createdDt
    ): self {
        $file = new static();
        $file->fs_name = $name;
        $file->fs_path = $path->getValue();
        $file->fs_size = $size;
        $file->fs_uid = $uid->getValue();
        $file->fs_mime_type = $mimeType;
        $file->fs_md5_hash = $hash;
        $file->fs_created_dt = $createdDt->format('Y-m-d H:i:s');
        return $file;
    }

    public function rules(): array
    {
        return [
            ['fs_expired_dt', 'safe'],

            ['fs_mime_type', 'string', 'max' => 127],

            ['fs_name', 'string', 'max' => 100],

            ['fs_path', 'string', 'max' => 250],

            ['fs_private', 'boolean'],

            ['fs_size', 'default', 'value' => null],
            ['fs_size', 'integer'],

            ['fs_title', 'string', 'max' => 100],

            ['fs_uid', 'string', 'max' => 32],
            ['fs_uid', 'unique'],

            ['fs_md5_hash', 'string', 'max' => 32],
        ];
    }
    public function getCases(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FileCase::class, ['fc_fs_id' => 'fs_id']);
    }
    public function getClients(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FileClient::class, ['fcl_fs_id' => 'fs_id']);
    }
    public function getLeads(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FileLead::class, ['fld_fs_id' => 'fs_id']);
    }
    public function getLogs(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FileLog::class, ['fl_fs_id' => 'fs_id']);
    }
    public function getShares(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FileShare::class, ['fsh_fs_id' => 'fs_id']);
    }
    public function getUsers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FileUser::class, ['fus_fs_id' => 'fs_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fs_id' => 'ID',
            'fs_uid' => 'Uid',
            'fs_mime_type' => 'Mime Type',
            'fs_name' => 'Name',
            'fs_title' => 'Title',
            'fs_path' => 'Path',
            'fs_size' => 'Size',
            'fs_private' => 'Private',
            'fs_md5_hash' => 'Hash',
            'fs_expired_dt' => 'Expired Dt',
            'fs_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%file_storage}}';
    }

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('db_postgres');
    }
}
