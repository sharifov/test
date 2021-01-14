<?php

namespace modules\fileStorage\src\entity\fileCase;

use modules\fileStorage\src\entity\fileStorage\FileStorage;
use sales\entities\cases\Cases;
use Yii;

/**
 * This is the model class for table "{{%file_case}}".
 *
 * @property int $fc_fs_id
 * @property int $fc_case_id
 *
 * @property FileStorage $file
 */
class FileCase extends \yii\db\ActiveRecord
{
    public static function create(int $fileId, int $caseId): self
    {
        $file = new static();
        $file->fc_fs_id = $fileId;
        $file->fc_case_id = $caseId;
        return $file;
    }

    public function rules(): array
    {
        return [
            [['fc_fs_id', 'fc_case_id'], 'unique', 'targetAttribute' => ['fc_fs_id', 'fc_case_id']],

            ['fc_case_id', 'required'],
            ['fc_case_id', 'integer'],
            ['fc_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['fc_case_id' => 'cs_id']],

            ['fc_fs_id', 'required'],
            ['fc_fs_id', 'integer'],
            ['fc_fs_id', 'exist', 'skipOnError' => true, 'targetClass' => FileStorage::class, 'targetAttribute' => ['fc_fs_id' => 'fs_id']],
        ];
    }
    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileStorage::class, ['fs_id' => 'fc_fs_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fc_fs_id' => 'File ID',
            'fc_case_id' => 'Case ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%file_case}}';
    }

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('db_postgres');
    }
}
