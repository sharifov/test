<?php

namespace modules\hotel\models;

use common\models\Currency;
use modules\hotel\models\query\HotelQuoteRoomQuery;
use sales\helpers\email\TextConvertingHelper;
use modules\hotel\src\entities\hotelQuoteRoom\events\HotelQuoteRoomCloneCreatedEvent;
use modules\hotel\src\entities\hotelQuoteRoom\serializer\HotelQuoteRoomSerializer;
use sales\entities\EventTrait;
use sales\entities\serializer\Serializable;
use sales\helpers\text\CleanTextHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "hotel_quote_room".
 *
 * @property int $hqr_id
 * @property int $hqr_hotel_quote_id
 * @property string|null $hqr_room_name
 * @property string|null $hqr_key
 * @property string|null $hqr_code
 * @property string|null $hqr_class
 * @property float|null $hqr_amount
 * @property string|null $hqr_currency
 * @property float|null $hqr_cancel_amount
 * @property string|null $hqr_cancel_from_dt
 * @property string|null $hqr_payment_type
 * @property string|null $hqr_board_code
 * @property string|null $hqr_board_name
 * @property int|null $hqr_rooms
 * @property int|null $hqr_adults
 * @property int|null $hqr_children
 * @property string|null $hqr_children_ages
 * @property string|null $hqr_rate_comments_id
 * @property string|null $hqr_rate_comments
 * @property int $hqr_type
 * @property float $hqr_service_fee_percent
 * @property float $hqr_system_mark_up
 * @property float $hqr_agent_mark_up
 *
 * @property Currency $hqrCurrency
 * @property HotelQuote $hqrHotelQuote
 */
class HotelQuoteRoom extends ActiveRecord implements Serializable
{
    use EventTrait;

    public const TYPE_RECHECK = 0;
    public const TYPE_BOOKABLE = 1;

    public const TYPE_LIST = [
        self::TYPE_RECHECK => 'RECHECK',
        self::TYPE_BOOKABLE => 'BOOKABLE',
    ];

    public static function clone(HotelQuoteRoom $room, int $quoteId)
    {
        $clone = new self();

        $clone->attributes = $room->attributes;
        $clone->hqr_id = null;
        $clone->hqr_hotel_quote_id = $quoteId;
        $clone->recordEvent(new HotelQuoteRoomCloneCreatedEvent($clone));

        return $clone;
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'hotel_quote_room';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['hqr_hotel_quote_id', 'hqr_type'], 'required'],
            [['hqr_hotel_quote_id', 'hqr_rooms', 'hqr_adults', 'hqr_children', 'hqr_type'], 'integer'],
            [['hqr_amount', 'hqr_cancel_amount', 'hqr_service_fee_percent', 'hqr_system_mark_up', 'hqr_agent_mark_up'], 'number'],
            [['hqr_cancel_from_dt'], 'safe'],
            [['hqr_room_name'], 'string', 'max' => 150],
            [['hqr_key'], 'string', 'max' => 255],
            [['hqr_class'], 'string', 'max' => 5],
            [['hqr_currency'], 'string', 'max' => 3],
            [['hqr_payment_type', 'hqr_code'], 'string', 'max' => 10],
            [['hqr_board_code'], 'string', 'max' => 2],
            [['hqr_board_name'], 'string', 'max' => 100],
            [['hqr_children_ages', 'hqr_rate_comments_id'], 'string', 'max' => 50],
            [['hqr_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['hqr_currency' => 'cur_code']],
            [['hqr_hotel_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelQuote::class, 'targetAttribute' => ['hqr_hotel_quote_id' => 'hq_id']],
            [['hqr_rate_comments'], 'trim'],
            [['hqr_rate_comments'], 'filter', 'filter' => static function ($value) {
                return self::cleanRateComments($value);
            }],
            [['hqr_rate_comments'], 'string', 'max' => 1000],
        ];
    }

    public static function cleanRateComments(string $text): string
    {
        return StringHelper::truncate(stripslashes(strip_tags($text)), 999, '');
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'hqr_id' => 'ID',
            'hqr_hotel_quote_id' => 'Hotel Quote ID',
            'hqr_room_name' => 'Room Name',
            'hqr_key' => 'Key',
            'hqr_code' => 'Code',
            'hqr_class' => 'Class',
            'hqr_amount' => 'Amount',
            'hqr_currency' => 'Currency',
            'hqr_cancel_amount' => 'Cancel Amount',
            'hqr_cancel_from_dt' => 'Cancel From Dt',
            'hqr_payment_type' => 'Payment Type',
            'hqr_board_code' => 'Board Code',
            'hqr_board_name' => 'Board Name',
            'hqr_rooms' => 'Rooms',
            'hqr_adults' => 'Adults',
            'hqr_children' => 'Children',
            'hqr_service_fee_percent' => 'Service Fee Percent',
            'hqr_system_mark_up' => 'System mark up',
            'hqr_agent_mark_up' => 'Agent mark up',
        ];
    }


    public function afterFind()
    {
        parent::afterFind();
        $this->hqr_amount = $this->hqr_amount === null ? null : (float) $this->hqr_amount;
    }

    /**
     * @return ActiveQuery
     */
    public function getHqrCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'hqr_currency']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHqrHotelQuote(): ActiveQuery
    {
        return $this->hasOne(HotelQuote::class, ['hq_id' => 'hqr_hotel_quote_id']);
    }

    /**
     * @return HotelQuoteRoomQuery the active query used by this AR class.
     */
    public static function find(): HotelQuoteRoomQuery
    {
        return new HotelQuoteRoomQuery(static::class);
    }

    public function serialize(): array
    {
        return (new HotelQuoteRoomSerializer($this))->getData();
    }

    /**
     * @param array $room
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function setAdditionalInfo(array $room): bool
    {
        $this->hqr_rate_comments_id = $room['rateCommentsId'] ?? null;
        $this->hqr_type = ($room['type'] == self::TYPE_LIST[self::TYPE_BOOKABLE]) ?
            self::TYPE_BOOKABLE : self::TYPE_RECHECK;
        $this->hqr_rate_comments = $this->prepareRateComments($room);

        return $this->save();
    }

    /**
     * @param array $room
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function prepareRateComments(array $room): string
    {
        $rateComments = (isset($room['rateComments'])) ? TextConvertingHelper::htmlToText($room['rateComments']) : '';
        if (isset($room['cancellationPolicies']) && count($room['cancellationPolicies'])) {
            $rateComments .= '  Cancellation Policies:';
            foreach ($room['cancellationPolicies'] as $policy) {
                $rateComments .= ' From: ' . \Yii::$app->formatter->asDatetime(strtotime($policy['from']));
                $rateComments .= ' Amount: ' . \Yii::$app->db->quoteValue($policy['amount']);
            }
        }
        return $rateComments;
    }

    /**
     * @param int $hotelQuoteId
     * @return HotelQuoteRoom[]
     */
    public static function getRoomsByQuoteId(int $hotelQuoteId): array
    {
        return self::find()->where(['hqr_hotel_quote_id' => $hotelQuoteId])->all();
    }
}
