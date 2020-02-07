<?php

namespace modules\hotel\src\entities\hotelQuoteServiceLog;

use common\models\Employee;
use modules\hotel\models\HotelQuote;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 *
 * @property int $hqsl_id
 * @property int $hqsl_hotel_quote_id
 * @property int $hqsl_action_type_id
 * @property int $hqsl_status_id
 * @property string|null $hqsl_message
 * @property int|null $hqsl_created_user_id
 * @property string|null $hqsl_created_dt
 * @property string|null $hqsl_updated_dt
 *
 * @property ActiveQuery $hotelQuote
 * @property ActiveQuery $createdUser
 */
class HotelQuoteServiceLog extends ActiveRecord
{
    public const STATUS_SEND_REQUEST    = 1;
    public const STATUS_SUCCESS         = 2;
    public const STATUS_FAIL            = 3; // Api response with error
    public const STATUS_ERROR           = 4; // System request error (404 etc.)

    public const ACTION_TYPE_BOOK       = 1;
    public const ACTION_TYPE_CHECK      = 2;
    public const ACTION_TYPE_CANCEL     = 3;

    public const STATUS_LIST = [
    	self::STATUS_SEND_REQUEST   => 'Send request',
        self::STATUS_SUCCESS        => 'Success',
        self::STATUS_FAIL => 'Fail',
		self::STATUS_ERROR => 'Error',
    ];

    public const ACTION_TYPE_LIST = [
    	self::ACTION_TYPE_BOOK => 'Book',
        self::ACTION_TYPE_CHECK => 'Check',
		self::ACTION_TYPE_CANCEL => 'Cancel',
    ];

    public const URL_METHOD_ACTION_TYPE_MAP = [
        'booking/book_post' => self::ACTION_TYPE_BOOK,
        'booking/checkrate_post' => self::ACTION_TYPE_CHECK,
        'booking/book_delete' => self::ACTION_TYPE_CANCEL,
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'hotel_quote_service_log';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['hqsl_hotel_quote_id', 'hqsl_action_type_id', 'hqsl_status_id'], 'required'],
            [['hqsl_hotel_quote_id', 'hqsl_action_type_id', 'hqsl_status_id', 'hqsl_created_user_id'], 'integer'],
            [['hqsl_message'], 'string'],
            [['hqsl_created_dt', 'hqsl_updated_dt'], 'safe'],
            [['hqsl_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['hqsl_created_user_id' => 'id']],
            [['hqsl_hotel_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelQuote::class, 'targetAttribute' => ['hqsl_hotel_quote_id' => 'hq_id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'hqsl_id' => 'ID',
            'hqsl_hotel_quote_id' => 'Hotel Quote',
            'hqsl_action_type_id' => 'Action Type',
            'hqsl_status_id' => 'Status',
            'hqsl_message' => 'Message',
            'hqsl_created_user_id' => 'Created User',
            'hqsl_created_dt' => 'Created',
            'hqsl_updated_dt' => 'Updated',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['hqsl_created_dt', 'hqsl_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['hqsl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'hqsl_created_user_id',
                'updatedByAttribute' => false
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'hqsl_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotelQuote(): ActiveQuery
    {
        return $this->hasOne(HotelQuote::class, ['hq_id' => 'hqsl_hotel_quote_id']);
    }

    /**
     * @return Scopes|ActiveQuery
     */
    public static function find()
    {
        return new Scopes(static::class);
    }

}
