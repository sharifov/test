<?php

namespace modules\fileStorage\src\entity\fileClient;

use common\models\Client;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use Yii;

/**
 * This is the model class for table "{{%file_client}}".
 *
 * @property int $fcl_fs_id
 * @property int $fcl_client_id
 *
 * @property FileStorage $file
 */
class FileClient extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['fcl_fs_id', 'fcl_client_id'], 'unique', 'targetAttribute' => ['fcl_fs_id', 'fcl_client_id']],

            ['fcl_client_id', 'required'],
            ['fcl_client_id', 'integer'],
            ['fcl_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['fcl_client_id' => 'id']],

            ['fcl_fs_id', 'required'],
            ['fcl_fs_id', 'integer'],
            ['fcl_fs_id', 'exist', 'skipOnError' => true, 'targetClass' => FileStorage::class, 'targetAttribute' => ['fcl_fs_id' => 'fs_id']],
        ];
    }
    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileStorage::class, ['fs_id' => 'fcl_fs_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fcl_fs_id' => 'File ID',
            'fcl_client_id' => 'Client ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%file_client}}';
    }

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('db_postgres');
    }
}
