<?php

namespace modules\hotel\src\entities\hotelQuoteServiceLog;

use common\models\Employee;
use modules\hotel\models\HotelQuote;
use modules\hotel\src\entities\hotelQuoteServiceLog\Scopes;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
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
 * @property \yii\db\ActiveQuery $hotelQuote
 * @property \yii\db\ActiveQuery $createdUser
 */
class HotelQuoteServiceLog extends ActiveRecord
{
    public const STATUS_SEND = 1;
    public const STATUS_SUCCESS = 2;
    public const STATUS_ERROR = 3;

    public const ACTION_TYPE_BOOK = 1;
    public const ACTION_TYPE_CHECK = 2;
    public const ACTION_TYPE_CANCEL = 3;

    public const STATUS_LIST = [
    	self::STATUS_SEND => 'Send',
        self::STATUS_SUCCESS => 'Success',
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

    public const EVENT_CREATE_LOG = 'eventCreateLog';

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
    public function attributeLabels()
    {
        return [
            'hqsl_id' => 'ID',
            'hqsl_hotel_quote_id' => 'Hotel Quote ID',
            'hqsl_action_type_id' => 'Action Type',
            'hqsl_status_id' => 'Status',
            'hqsl_message' => 'Message',
            'hqsl_created_user_id' => 'Created User ID',
            'hqsl_created_dt' => 'Created Dt',
            'hqsl_updated_dt' => 'Updated Dt',
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
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'hqsl_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotelQuote()
    {
        return $this->hasOne(HotelQuote::class, ['hq_id' => 'hqsl_hotel_quote_id']);
    }

    /**
     * @return Scopes|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }

}
