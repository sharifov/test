<?php

namespace common\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * This is the model class for table "info_block".
 *
 * @property int $ib_id
 * @property string $ib_title
 * @property string $ib_key
 * @property string|null $ib_description
 * @property boolean $ib_enabled
 * @property string|null $ib_created_dt
 * @property string|null $ib_updated_dt
 * @property int|null $ib_created_user_id
 * @property int|null $ib_updated_user_id
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class InfoBlock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'info_block';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ib_created_dt', 'ib_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ib_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ib_created_user_id',
                'updatedByAttribute' => 'ib_updated_user_id',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ib_title', 'ib_key'], 'required'],
            [['ib_description'], 'string'],
            [['ib_enabled'], 'boolean'],
            [['ib_created_user_id', 'ib_updated_user_id'], 'integer'],
            [['ib_created_dt', 'ib_updated_dt'], 'safe'],
            [['ib_title'], 'string', 'max' => 255],
            [['ib_key'], 'string', 'max' => 50],
            [['ib_key'], 'unique'],
            ['ib_key', 'filter', 'filter' => static function ($value) {
                return Inflector::slug($value, '_');
            }],
            [['ib_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['ib_updated_user_id' => 'id']],
            [['ib_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['ib_created_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ib_id' => 'ID',
            'ib_title' => 'Title',
            'ib_key' => 'Key',
            'ib_description' => 'Description',
            'ib_enabled' => 'Enabled',
            'ib_created_dt' => 'Created Date',
            'ib_updated_dt' => 'Updated Date',
            'ib_created_user_id' => 'Created User',
            'ib_updated_user_id' => 'Updated User',
        ];
    }

    /**
     * Gets query for [[CreatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::className(), ['id' => 'ib_created_user_id']);
    }

    /**
     * Gets query for [[UpdatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::className(), ['id' => 'ib_updated_user_id']);
    }
}
