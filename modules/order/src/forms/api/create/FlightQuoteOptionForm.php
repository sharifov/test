<?php

namespace modules\order\src\forms\api\create;

use common\models\Currency;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteTrip;

/**
 * Class FlightQuoteOptionForm
 * @package modules\order\src\forms\api\create
 *
 * @property string|null $segment_uid
 * @property string|null $pax_uid
 * @property string|null $trip_uid
 * @property float|null $total
 * @property string|null $currency
 * @property float|null $usd_total
 * @property float|null $base_price
 * @property float|null $markup_amount
 * @property float|null $usd_base_price
 * @property float|null $usd_markup_amount
 * @property string|null $display_name
 *
 * @property int|null $paxId
 * @property int|null $segmentId
 * @property int|null $tripId
 */
class FlightQuoteOptionForm extends \yii\base\Model
{
    public $segment_uid;

    public $pax_uid;

    public $trip_uid;

    public $total;

    public $currency;

    public $usd_total;

    public $base_price;

    public $markup_amount;

    public $usd_base_price;

    public $usd_markup_amount;

    public $display_name;

    public ?int $paxId = null;

    public ?int $segmentId = null;

    public ?int $tripId = null;

    public function rules(): array
    {
        return [
            [['segment_uid', 'pax_uid', 'trip_uid'], 'string'],

            [['segment_uid'], 'validateSegment', 'skipOnEmpty' => true],
            [['pax_uid'], 'validatePax', 'skipOnEmpty' => true],
            [['trip_uid'], 'validateTrip', 'skipOnEmpty' => true],

            [['display_name'], 'string', 'max' => 255],

            [['currency'], 'string', 'max' => 5],
            [['currency'], 'exist', 'skipOnEmpty' => true, 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code'],

            [['total', 'usd_total', 'base_price', 'markup_amount', 'usd_markup_amount'], 'filter', 'filter' => 'floatval']

        ];
    }

    public function validatePax(): bool
    {
        if ($this->pax_uid) {
            if ($pax = FlightPax::find()->select(['fp_id'])->where(['fp_uid' => $this->pax_uid])->one()) {
                $this->paxId = $pax->fp_id;
            } else {
                $this->addError('pax_uid', 'FlightPax not found by uid: ' . $this->pax_uid);
                return false;
            }
        }
        return true;
    }

    public function validateSegment(): bool
    {
        if ($this->segment_uid) {
            if ($segment = FlightQuoteSegment::find()->select(['fqs_id'])->where(['fqs_uid' => $this->segment_uid])->one()) {
                $this->segmentId = $segment->fqs_id;
            } else {
                $this->addError('segment_uid', 'FlightQuoteSegment not found by uid: ' . $this->segment_uid);
                return false;
            }
        }
        return true;
    }

    public function validateTrip(): bool
    {
        if ($this->trip_uid) {
            if ($trip = FlightQuoteTrip::find()->select(['fqt_id'])->where(['fqt_uid' => $this->trip_uid])->one()) {
                $this->tripId = $trip->fqt_id;
            } else {
                $this->addError('trip_uid', 'FlightQuoteTrip not found by uid: ' . $this->trip_uid);
                return false;
            }
        }
        return true;
    }
}
