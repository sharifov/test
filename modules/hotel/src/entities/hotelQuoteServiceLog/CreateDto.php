<?php

namespace modules\hotel\src\entities\hotelQuoteServiceLog;

use yii\helpers\VarDumper;

/**
 * Class CreateDto
 * @package modules\hotel\src\entities\hotelQuoteServiceLog
 */
class CreateDto
{
    public $hqsl_hotel_quote_id;
    public $hqsl_message;
    public $hqsl_status_id;
    public $hqsl_action_type_id;

    /**
     * CreateDto constructor.
     * @param int $hqsl_hotel_quote_id
     * @param int $hqsl_status_id
     * @param int $hqsl_action_type_id
     * @param $hqsl_message
     * @param bool $toString
     */
    public function __construct(
        int $hqsl_hotel_quote_id,
        int $hqsl_action_type_id,
        $hqsl_message,
        int $hqsl_status_id = HotelQuoteServiceLogStatus::STATUS_SEND_REQUEST,
        $toString = true
    )
    {
        $this->hqsl_hotel_quote_id = $hqsl_hotel_quote_id;
        $this->hqsl_action_type_id = $hqsl_action_type_id;
        $this->hqsl_message = ($toString) ? VarDumper::dumpAsString($hqsl_message) : $hqsl_message;
        $this->hqsl_status_id = $hqsl_status_id;
    }
}
