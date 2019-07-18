<?php

namespace sales\forms\api\communication\voice\finish;

use yii\base\Model;

/**
 * Class CallDataForm
 * @property $sid
 * @property $date_created;
 * @property $date_updated;
 * @property $account_sid;
 * @property $to;
 * @property $to_formatted;
 * @property $from;
 * @property $from_formatted;
 * @property $phone_number_sid;
 * @property $status;
 * @property $start_time;
 * @property $end_time;
 * @property $duration;
 * @property $price;
 * @property $price_unit;
 * @property $direction;
 * @property $api_version;
 * @property $forwarded_from;
 * @property $uri;
 * @property $subresource_uris;
 */
class CallDataForm extends Model
{

    public $sid;
    public $date_created;
    public $date_updated;
    public $account_sid;
    public $to;
    public $to_formatted;
    public $from;
    public $from_formatted;
    public $phone_number_sid;
    public $status;
    public $start_time;
    public $end_time;
    public $duration;
    public $price;
    public $price_unit;
    public $direction;
    public $api_version;
    public $forwarded_from;
    public $uri;
    public $subresource_uris;

    /**
     * @return bool
     */
    public function isEmptySid(): bool
    {
        return $this->sid ? false : true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['sid', 'string'],
            ['date_created', 'string'],
            ['date_updated', 'string'],
            ['account_sid', 'string'],
            ['to', 'string'],
            ['to_formatted', 'string'],
            ['from', 'string'],
            ['from_formatted', 'string'],
            ['phone_number_sid', 'string'],
            ['status', 'in', 'range' => ['completed']],
            ['start_time', 'string'],
            ['end_time', 'string'],
            ['duration', 'integer'],
            ['price', 'double'],
            ['price_unit', 'in', 'range' => ['USD']],
            ['direction', 'in', 'range' => ['inbound']],
            ['api_version', 'date', 'format' => 'Y-m-d'],
            ['forwarded_from', 'string'],
            ['uri', 'string'],
            ['subresource_uris', 'safe'],
        ];
    }

}