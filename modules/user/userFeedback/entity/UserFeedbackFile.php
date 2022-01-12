<?php

namespace modules\user\userFeedback\entity;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "user_feedback_file".
 *
 * @property int $uff_id
 * @property int $uff_uf_id
 * @property string $uff_mimetype
 * @property int|null $uff_size
 * @property string|null $uff_filename
 * @property string|null $uff_title
 * @property resource $uff_blob
 * @property string|null $uff_created_dt
 * @property int|null $uff_created_user_id
 */
class UserFeedbackFile extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_feedback_file';
    }

    /**
     * @return Connection the database connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('db_postgres');
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['uff_uf_id', 'uff_mimetype', 'uff_blob'], 'required'],
            [['uff_uf_id', 'uff_size', 'uff_created_user_id'], 'default', 'value' => null],
            [['uff_uf_id', 'uff_size', 'uff_created_user_id'], 'integer'],
            [['uff_blob'], 'string'],
            [['uff_created_dt'], 'safe'],
            [['uff_mimetype'], 'string', 'max' => 100],
            [['uff_filename', 'uff_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'uff_id' => 'File ID',
            'uff_uf_id' => 'Feedback ID',
            'uff_mimetype' => 'MimeType',
            'uff_size' => 'Size',
            'uff_filename' => 'FileName',
            'uff_title' => 'Title',
            'uff_blob' => 'Blob',
            'uff_created_dt' => 'Created Dt',
            'uff_created_user_id' => 'Created User ID',
        ];
    }
}
