<?php

namespace modules\fileStorage\src\entity\fileLead;

use common\models\Lead;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use Yii;

/**
 * This is the model class for table "{{%file_lead}}".
 *
 * @property int $fld_fs_id
 * @property int $fld_lead_id
 *
 * @property FileStorage $file
 */
class FileLead extends \yii\db\ActiveRecord
{
    public static function create(int $fileId, int $leadId): self
    {
        $file = new static();
        $file->fld_fs_id = $fileId;
        $file->fld_lead_id = $leadId;
        return $file;
    }

    public function rules(): array
    {
        return [
            [['fld_fs_id', 'fld_lead_id'], 'unique', 'targetAttribute' => ['fld_fs_id', 'fld_lead_id']],

            ['fld_fs_id', 'required'],
            ['fld_fs_id', 'integer'],
            ['fld_fs_id', 'exist', 'skipOnError' => true, 'targetClass' => FileStorage::class, 'targetAttribute' => ['fld_fs_id' => 'fs_id']],

            ['fld_lead_id', 'required'],
            ['fld_lead_id', 'integer'],
            ['fld_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['fld_lead_id' => 'id']],
        ];
    }
    public function getFile(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FileStorage::class, ['fs_id' => 'fld_fs_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fld_fs_id' => 'File ID',
            'fld_lead_id' => 'Lead ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%file_lead}}';
    }

    public static function getDb(): \yii\db\Connection
    {
        return Yii::$app->get('db_postgres');
    }
}
