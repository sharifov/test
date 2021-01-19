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
 * @property int $fs_status
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
    public static function createByUpload(
        string $name,
        ?string $title,
        Path $path,
        int $size,
        Uid $uid,
        string $mimeType,
        string $hash,
        bool $private,
        \DateTimeImmutable $createdDt
    ): self {
        $file = new static();
        $file->fs_name = $name;
        $file->fs_title = $title;
        $file->fs_path = $path->getValue();
        $file->fs_size = $size;
        $file->fs_uid = $uid->getValue();
        $file->fs_mime_type = $mimeType;
        $file->fs_md5_hash = $hash;
        $file->fs_private = $private;
        $file->fs_created_dt = $createdDt->format('Y-m-d H:i:s');
        $file->fs_status = FileStorageStatus::PENDING;
        return $file;
    }

    public function edit(?string $title, ?bool $private, \DateTimeImmutable $expiredDt): void
    {
        $this->fs_title = $title;
        $this->fs_private = $private;
        $this->fs_expired_dt = $expiredDt->format('Y-m-d H:i:s');
    }

    public function rename(string $name): void
    {
        $this->fs_name = $name;
        $this->changePath();
    }

    private function changePath(): void
    {
        $positionLastChunk = strrpos($this->fs_path, '/');
        if ($positionLastChunk === false) {
            throw new \DomainException('Path value is error.');
        }
        $this->fs_path = substr($this->fs_path, 0, $positionLastChunk) . '/' . $this->fs_name;
    }

    public function uploaded(): void
    {
        $this->fs_status = FileStorageStatus::UPLOADED;
    }

    public function failed(): void
    {
        $this->fs_status = FileStorageStatus::FAILED;
    }

    public function rules(): array
    {
        return [
            ['fs_expired_dt', 'datetime', 'php:Y-m-d H:i:s'],

            ['fs_mime_type', 'trim'],
            ['fs_mime_type', 'string', 'max' => 127],

            ['fs_name', 'trim'],
            ['fs_name', 'string', 'max' => 100],

            ['fs_path', 'trim'],
            ['fs_path', 'string', 'max' => 250],

            ['fs_private', 'boolean'],

            ['fs_size', 'default', 'value' => null],
            ['fs_size', 'integer'],

            ['fs_title', 'trim'],
            ['fs_title', 'string', 'max' => 100],

            ['fs_uid', 'trim'],
            ['fs_uid', 'string', 'max' => 32],
            ['fs_uid', 'unique'],

            ['fs_md5_hash', 'trim'],
            ['fs_md5_hash', 'string', 'max' => 32],

            ['fs_status', 'integer'],
            ['fs_status', 'in', 'range' => array_keys(FileStorageStatus::getList())],
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
            'fs_status' => 'Status',
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
