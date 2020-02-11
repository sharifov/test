<?php

namespace modules\hotel\src\entities\hotelQuoteServiceLog;

/**
 * Class HotelQuoteServiceLogStatus
 * @package modules\hotel\src\entities\hotelQuoteServiceLog
 */
class HotelQuoteServiceLogStatus
{
    public const STATUS_SEND_REQUEST    = 1;
    public const STATUS_SUCCESS         = 2;
    public const STATUS_FAIL_WITH_ERROR = 3; // Api response with error
    public const STATUS_FAIL            = 4; // Api response did not send expected data
    public const STATUS_ERROR_RESPONSE  = 5; // System response error (404 etc.)
    public const STATUS_ERROR           = 6; // Throwable error

    public const ACTION_TYPE_BOOK       = 1;
    public const ACTION_TYPE_CHECK      = 2;
    public const ACTION_TYPE_CANCEL     = 3;

    public const STATUS_LIST = [
    	self::STATUS_SEND_REQUEST       => 'Send request',
        self::STATUS_SUCCESS            => 'Success',
        self::STATUS_FAIL_WITH_ERROR    => 'Fail with error',
        self::STATUS_FAIL               => 'Fail',
		self::STATUS_ERROR_RESPONSE     => 'Error response',
		self::STATUS_ERROR              => 'Error',
    ];

    public const STATUS_LIST_TITLE = [
    	self::STATUS_SEND_REQUEST       => 'Send request',
        self::STATUS_SUCCESS            => 'Process completed successfully',
        self::STATUS_FAIL_WITH_ERROR    => 'Api responded with an error',
        self::STATUS_FAIL               => 'Api did not send expected data',
        self::STATUS_ERROR_RESPONSE     => 'Api response error',
		self::STATUS_ERROR              => 'Hotel booking request api throwable error',
    ];

    public const ACTION_TYPE_LIST = [
    	self::ACTION_TYPE_BOOK => 'Book',
        self::ACTION_TYPE_CHECK => 'Check',
		self::ACTION_TYPE_CANCEL => 'Cancel',
    ];

    /**
     * @param int|null $status
     * @return string
     */
    public static function getTitle(?int $status): string
    {
        return self::STATUS_LIST_TITLE[$status] ?? ($status ? 'Undefined' : '');
    }

}
