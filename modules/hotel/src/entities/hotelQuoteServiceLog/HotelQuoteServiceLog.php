<?php

namespace modules\hotel\src\entities\hotelQuoteServiceLog;

use common\models\Employee;
use modules\hotel\models\HotelQuote;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

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
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'hotel_quote_service_log';
    }

    /**
     * @return array
     */
    public function rules(): array
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

    /**
     * @param CreateDto $dto
     * @param bool $save
     * @return HotelQuoteServiceLog
     */
    public static function create(CreateDto $dto, bool $save = true): HotelQuoteServiceLog
    {
        $model = new self();
        $model->hqsl_hotel_quote_id = $dto->hqsl_hotel_quote_id;
        $model->hqsl_message = $dto->hqsl_message;
        $model->hqsl_status_id = $dto->hqsl_status_id;
        $model->hqsl_action_type_id = $dto->hqsl_action_type_id;

        if ($save) {
            $model->save();
        }
        return $model;
    }

    /**
     * @param int $statusId
     * @return HotelQuoteServiceLog
     */
    public function setStatus(int $statusId): HotelQuoteServiceLog
    {
        if (!array_key_exists($statusId, HotelQuoteServiceLogStatus::STATUS_LIST)) {
            throw new \InvalidArgumentException('Invalid Status');
        }

        $this->hqsl_status_id = $statusId;
        return $this;
    }

    /**
     * @param $message
     * @param bool $toString
     * @return HotelQuoteServiceLog
     */
    public function setMessage($message, bool $toString = true): HotelQuoteServiceLog
    {
        if ($toString) {
            $message = VarDumper::dumpAsString($message);
        }
        $this->hqsl_message = $message;
        return $this;
    }

    /**
     * @return bool
     */
    public function saveChanges(): bool
    {
        return $this->save();
    }
}

