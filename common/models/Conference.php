<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "conference".
 *
 * @property int $cf_id
 * @property int $cf_cr_id
 * @property string $cf_sid
 * @property int $cf_status_id
 * @property string $cf_options
 * @property string $cf_created_dt
 * @property string $cf_updated_dt
 *
 * @property ConferenceRoom $cfCr
 */
class Conference extends \yii\db\ActiveRecord
{

    public const STATUS_START   = 1;
    public const STATUS_DELAY   = 2;
    public const STATUS_END     = 3;

    public const STATUS_LIST     = [
        self::STATUS_START  => 'Start',
        self::STATUS_DELAY  => 'Delay',
        self::STATUS_END    => 'End',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conference';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cf_cr_id'], 'required'],
            [['cf_cr_id', 'cf_status_id'], 'integer'],
            [['cf_options'], 'string'],
            [['cf_created_dt', 'cf_updated_dt'], 'safe'],
            [['cf_sid'], 'string', 'max' => 34],
            [['cf_sid'], 'unique'],
            [['cf_cr_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConferenceRoom::class, 'targetAttribute' => ['cf_cr_id' => 'cr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cf_id' => 'ID',
            'cf_cr_id' => 'Cr ID',
            'cf_sid' => 'Sid',
            'cf_status_id' => 'Status ID',
            'cf_options' => 'Options',
            'cf_created_dt' => 'Created Dt',
            'cf_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cf_created_dt', 'cf_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cf_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCfCr()
    {
        return $this->hasOne(ConferenceRoom::class, ['cr_id' => 'cf_cr_id']);
    }

    /**
     * {@inheritdoc}
     * @return ConferenceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConferenceQuery(get_called_class());
    }
}
