<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * This is the model class for table "date_sensitive".
 *
 * @property int $da_id
 * @property string $da_key
 * @property string|null $da_name
 * @property string|null $da_source
 * @property string|null $da_created_dt
 * @property string|null $da_updated_dt
 * @property int|null $da_created_user_id
 * @property int|null $da_updated_user_id
 *
 * @property Employee $daCreatedUser
 * @property Employee $daUpdatedUser
 */
class DateSensitive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'date_sensitive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['da_key', 'da_name'], 'required'],
            [['da_source', 'da_created_dt', 'da_updated_dt'], 'safe'],
            [['da_created_user_id', 'da_updated_user_id'], 'integer'],
            [['da_key', 'da_name'], 'string', 'max' => 50],
            [['da_key'], 'unique'],
            [['da_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['da_created_user_id' => 'id']],
            [['da_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['da_updated_user_id' => 'id']],
            ['da_key', 'filter', 'filter' => static function ($value) {
                return Inflector::slug($value, '_');
            }]
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['da_created_dt', 'da_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['da_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'da_created_user_id',
                'updatedByAttribute' => 'da_updated_user_id',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'da_id' => 'Da ID',
            'da_key' => 'Key',
            'da_name' => 'Name',
            'da_source' => 'Source',
            'da_created_dt' => 'Da Created Dt',
            'da_updated_dt' => 'Da Updated Dt',
            'da_created_user_id' => 'Da Created User ID',
            'da_updated_user_id' => 'Da Updated User ID',
        ];
    }

    /**
     * Gets query for [[DaCreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDaCreatedUser()
    {
        return $this->hasOne(Employee::className(), ['id' => 'da_created_user_id']);
    }

    /**
     * Gets query for [[DaUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDaUpdatedUser()
    {
        return $this->hasOne(Employee::className(), ['id' => 'da_updated_user_id']);
    }
}
