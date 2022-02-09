<?php

namespace src\model\leadUserRating\entity;

use common\models\Employee;
use common\models\Lead;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_user_rating".
 *
 * @property int $lur_lead_id
 * @property int $lur_user_id
 * @property int $lur_rating
 * @property string|null $lur_created_dt
 * @property string|null $lur_updated_dt
 *
 * @property Lead $lead
 * @property Employee $user
 */
class LeadUserRating extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'lead_user_rating';
    }

    public const RATING_1 = 1;
    public const RATING_2 = 2;
    public const RATING_3 = 3;
    public const RATING_4 = 4;
    public const RATING_5 = 5;

    public const RATING_LIST = [
        'Rating 1' => self::RATING_1,
        'Rating 2' => self::RATING_2,
        'Rating 3' => self::RATING_3,
        'Rating 4' => self::RATING_4,
        'Rating 5' => self::RATING_5,
    ];


    public function rules(): array
    {
        return [
            [['lur_lead_id', 'lur_user_id'], 'unique', 'targetAttribute' => ['lur_lead_id', 'lur_user_id']],

            ['lur_lead_id', 'required'],
            ['lur_lead_id', 'integer'],
            ['lur_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lur_lead_id' => 'id']],

            ['lur_rating', 'required'],
            ['lur_rating', 'integer'],

            [['lur_updated_dt', 'lur_created_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['lur_user_id', 'required'],
            ['lur_user_id', 'integer'],
            ['lur_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lur_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lur_created_dt', 'lur_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lur_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lur_lead_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'lur_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lur_lead_id' => 'Lead ID',
            'lur_user_id' => 'User',
            'lur_rating' => 'Rating',
            'lur_created_dt' => 'Created Dt',
            'lur_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): LeadUserRatingScopes
    {
        return new LeadUserRatingScopes(static::class);
    }

    public static function create(
        int $leadId,
        int $userId,
        int $rating
    ): LeadUserRating {
        $model = new self();
        $model->lur_lead_id = $leadId;
        $model->lur_user_id = $userId;
        $model->lur_rating  = $rating;
        return $model;
    }
}
