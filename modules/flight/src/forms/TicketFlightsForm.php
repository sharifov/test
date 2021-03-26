<?php

namespace modules\flight\src\forms;

use yii\base\Model;

/**
 * Class TicketFlightsForm
 * @property string|null $uniqueId
 * @property int|null $status
 */
class TicketFlightsForm extends Model
{
    public $uniqueId;
    public $status;

    public const
        STATUS_CHECKOUT = 1,        // STATUS_CHECKOUT      STATUS_PENDING
        STATUS_PROCESSING = 2,      // STATUS_PROCESSING    STATUS_OPEN
        STATUS_ISSUED = 3,          // STATUS_ISSUED        STATUS_SUCCESS
        STATUS_ERROR = 4,           // STATUS_ERROR
        STATUS_EXPIRED = 5,         // STATUS EXPIRED
        STATUS_CANCELED = 6,        // STATUS_CANCELED
        STATUS_RESERVED = 7,        // STATUS_RESERVED
        STATUS_VOID_REQUEST = 8,    // STATUS_VOID_REQUEST  STATUS_WAITING_CANCEL
        STATUS_VOID = 9,            // STATUS_VOID
        STATUS_CC_DECLINED = 10,    // STATUS_CC_DECLINED
        STATUS_SEND_BOOK_RQ = 11,   // STATUS_SHARED
        STATUS_WAIT_PAYMENT = 12,   // currently for Alipay
        STATUS_PAYMENT_RELEASED = 13; // currently for Alipay

    public const STATUS_LIST = [
        self::STATUS_CHECKOUT => 'CHECKOUT',
        self::STATUS_PROCESSING => 'PROCESSING',
        self::STATUS_ISSUED => 'ISSUED',
        self::STATUS_ERROR => 'ERROR',
        self::STATUS_EXPIRED => 'EXPIRED',
        self::STATUS_CANCELED => 'CANCELED',
        self::STATUS_RESERVED => 'RESERVED',
        self::STATUS_VOID_REQUEST => 'VOID_REQUEST',
        self::STATUS_VOID => 'VOID',
        self::STATUS_CC_DECLINED => 'CC_DECLINED',
        self::STATUS_SEND_BOOK_RQ => 'SEND_BOOK_RQ',
        self::STATUS_WAIT_PAYMENT => 'WAIT_PAYMENT',
        self::STATUS_PAYMENT_RELEASED => 'PAYMENT_RELEASED',
    ];

    public const SUCCESS_STATUS = 3;

    public function rules(): array
    {
        return [
            ['uniqueId', 'required'],
            ['uniqueId', 'trim'],
            ['uniqueId', 'string', 'max' => 100],

            ['status', 'required'],
            ['status', 'integer'],
            ['status', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
