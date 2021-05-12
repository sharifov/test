<?php

namespace modules\flight\src\entities\flightQuoteBooking\serializer;

use common\models\Airline;
use modules\flight\models\FlightQuoteBooking;
use sales\entities\serializer\Serializer;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteBookingSerializer
 *
 * @property FlightQuoteBooking $model
 */
class FlightQuoteBookingSerializer extends Serializer
{
    /**
     * @param FlightQuoteBooking $model
     */
    public function __construct(FlightQuoteBooking $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'fqb_booking_id',
            'fqb_pnr',
            'fqb_gds',
            'fqb_validating_carrier',
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        $data['paxes'] = [];
        if ($this->model->fqtPaxes) {
            foreach ($this->model->fqtPaxes as $keyPax => $flightPax) {
                $data['paxes'][$keyPax] = $flightPax->serialize();
                $data['paxes'][$keyPax]['ticketNumber'] = $flightPax->flightQuoteTicket->fqt_ticket_number ?? '';
            }
        }

        $data['airlines'] = [];
        if ($this->model->flightQuoteBookingAirlines) {
            foreach ($this->model->flightQuoteBookingAirlines as $keyAirline => $bookingAirline) {
                $data['airlines'][$keyAirline]['record_locator'] = $bookingAirline->fqba_record_locator;
                $data['airlines'][$keyAirline]['airline_code'] = $bookingAirline->fqba_airline_code;
                $data['airlines'][$keyAirline]['airline'] = '';
                if ($airline = Airline::findIdentity($bookingAirline->fqba_airline_code)) {
                    $data['airlines'][$keyAirline]['airline'] = $airline->name;
                }
            }
        }
        return $data;
    }
}
