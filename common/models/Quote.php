<?php

namespace common\models;

use common\components\BackOffice;
use common\models\local\FlightSegment;
use common\models\local\LeadLogMessage;
use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quotes".
 *
 * @property int $id
 * @property string $uid
 * @property int $lead_id
 * @property int $employee_id
 * @property string $record_locator
 * @property string $pcc
 * @property string $cabin
 * @property string $gds
 * @property string $trip_type
 * @property string $main_airline_code
 * @property string $reservation_dump
 * @property int $status
 * @property boolean $check_payment
 * @property string $fare_type
 * @property string $created
 * @property string $updated
 * @property boolean $created_by_seller
 * @property string $employee_name
 *
 * @property QuotePrice[] $quotePrices
 * @property Employee $employee
 * @property Lead $lead
 */
class Quote extends \yii\db\ActiveRecord
{
    const SERVICE_FEE = 0.035;

    const
        GDS_SABRE = 'S',
        GDS_AMADEUS = 'A',
        GDS_WORLDSPAN = 'W';

    public CONST GDS_LIST = [
        self::GDS_SABRE => 'Sabre',
        self::GDS_AMADEUS => 'Amadeus',
        self::GDS_WORLDSPAN => 'WorldSpan',
    ];

    const
        FARE_TYPE_PUB = 'PUB',
        FARE_TYPE_SR = 'SR',
        FARE_TYPE_SRU = 'SRU',
        FARE_TYPE_COMM = 'COMM',
        FARE_TYPE_PUBC = 'PUBC';

    const
        STATUS_CREATED = 1,
        STATUS_APPLIED = 2,
        STATUS_DECLINED = 3,
        STATUS_SEND = 4,
        STATUS_OPENED = 5;


    public CONST STATUS_LIST = [
        self::STATUS_CREATED => 'Created',
        self::STATUS_APPLIED => 'Applied',
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_SEND => 'Send',
        self::STATUS_OPENED => 'Opened'
    ];

    public $itinerary = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotes';
    }

    public static function getGDSName($gds = null)
    {
        $mapping = [
            self::GDS_SABRE => 'Sabre',
            self::GDS_AMADEUS => 'Amadeus',
            self::GDS_WORLDSPAN => 'Worldspan'
        ];

        if ($gds === null) {
            return $mapping;
        }

        return isset($mapping[$gds]) ? $mapping[$gds] : $gds;
    }

    public static function createDump($flightSegments)
    {
        /**
         * @var $flightSegments FlightSegment[]
         */
        $nr = 1;
        $dump = [];
        foreach ($flightSegments as $flightSegment) {
            $daysName = self::getDayName($flightSegment->departureTime, $flightSegment->arrivalTime);

            $segment = $nr++ . self::addSpace(1);
            $segment .= $flightSegment->airlineCode;
            $segment .= self::addSpace(4 - strlen($flightSegment->flightNumber)) . $flightSegment->flightNumber;
            $segment .= $flightSegment->bookingClass . self::addSpace(1);

            $departureDate = strtoupper(date('dM', strtotime($flightSegment->departureTime)));
            $segment .= $departureDate . self::addSpace(1);

            $segment .= $flightSegment->departureAirportCode . $flightSegment->destinationAirportCode . self::addSpace(1);

            $segment .= empty($flightSegment->statusCode) ? '' : strtoupper($flightSegment->statusCode) . self::addSpace(1);

            $time = substr(str_replace(' ', '', str_replace(':', '', date('g:i A', strtotime($flightSegment->departureTime)))), 0, -1);
            $segment .= self::addSpace(5 - strlen($time)) . $time . self::addSpace(1);
            $time = substr(str_replace(' ', '', str_replace(':', '', date('g:i A', strtotime($flightSegment->arrivalTime)))), 0, -1);
            $segment .= (strlen($daysName) === 2)
                ? self::addSpace(5 - strlen($time)) . $time . self::addSpace(1)
                : self::addSpace(5 - strlen($time)) . $time . '+' . self::addSpace(1);

            $arrivalDate = strtoupper(date('dM', strtotime($flightSegment->arrivalTime)));
            $segment .= ($arrivalDate != $departureDate)
                ? $arrivalDate . self::addSpace(1) : '';

            $segment .= $daysName;
            $dump[] = $segment;
        }
        return $dump;
    }

    private static function getDayName($departureTime, $arrivalTime)
    {
        $departureDay = substr(strtoupper(date('D', strtotime($departureTime))), 0, -1);
        $arrivalDay = substr(strtoupper(date('D', strtotime($arrivalTime))), 0, -1);
        if (strcmp($departureDay, $arrivalDay) === 0) {
            return $departureDay;
        }
        return $departureDay . '/' . $arrivalDay;
    }

    private static function addSpace($n)
    {
        $space = '';
        for ($i = 0; $i < $n; $i++) {
            $space .= '&nbsp; ';
        }
        return $space;
    }

    public static function getElapsedTime($elapsedTime)
    {
        $h = $elapsedTime / 60;
        if ($h > 0) {
            $m = $elapsedTime % 60;
            $elapsedTime = (int)$h . 'hr';
            if ($m > 0) {
                $elapsedTime = $elapsedTime . ' ' . $m . 'min';
            } else {
                $elapsedTime = $elapsedTime . ' 0min';
            }
        }
        return $elapsedTime;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'reservation_dump', 'pcc', 'gds', 'main_airline_code'], 'required'],
            [['lead_id', 'status', 'check_payment'], 'integer'],
            [['created', 'updated', 'reservation_dump', 'created_by_seller', 'employee_name', 'employee_id'], 'safe'],
            [['uid', 'record_locator', 'pcc', 'cabin', 'gds', 'trip_type', 'main_airline_code', 'fare_type'], 'string', 'max' => 255],

            [['reservation_dump'], 'checkReservationDump'],
            [['status'], 'checkStatus'],

            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
        ];
    }


    public function checkReservationDump()
    {
        $dumpParser = self::parseDump($this->reservation_dump, true, $this->itinerary);
        if (empty($dumpParser)) {
            $this->addError('reservation_dump', 'Incorrect reservation dump!');
        }
    }

    public function checkStatus()
    {
        if ($this->lead_id && $this->status == self::STATUS_APPLIED) {
            $applied = self::findOne([
                'status' => self::STATUS_APPLIED,
                'lead_id' => $this->lead_id
            ]);

            if ($applied) {
                $this->addError('status', 'Exist applied quote!');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'lead_id' => 'Lead ID',
            'employee_id' => 'Employee ID',
            'record_locator' => 'Record Locator',
            'pcc' => 'Pcc',
            'cabin' => 'Cabin',
            'gds' => 'Gds',
            'trip_type' => 'Trip Type',
            'main_airline_code' => 'Main Airline Code',
            'status' => 'Status',
            'check_payment' => 'Check Payment',
            'fare_type' => 'Fare Type',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    public function behaviors() : array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuotePrices()
    {
        return $this->hasMany(QuotePrice::class, ['quote_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lead_id']);
    }



//    public function beforeValidate()
//    {
//        if ($this->isNewRecord) {
//            $this->uid = uniqid();
//            $this->employee_id = Yii::$app->user->identity->getId();
//        }
//
//        $dumpParser = self::parseDump($this->reservation_dump, true, $this->itinerary);
//        if (empty($dumpParser)) {
//            $this->addError('reservation_dump', 'Incorrect reservation dump!');
//        }
//
//        if ($this->status == self::STATUS_APPLIED) {
//            $applied = self::findOne([
//                'status' => self::STATUS_APPLIED,
//                'lead_id' => $this->lead_id
//            ]);
//            if ($applied !== null) {
//                $this->addError('status', 'Exist applied quote!');
//            }
//        }
//
//        return parent::beforeValidate();
//    }


    /*public function afterValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        if ($this->isNewRecord) {
            $this->status = self::STATUS_CREATED;
        }

        parent::afterValidate();
    }*/



    public function beforeSave($insert) : bool
    {
        if ($this->isNewRecord) {
            $this->uid = empty($this->uid) ? uniqid() : $this->uid;
            if (!Yii::$app->user->isGuest && Yii::$app->user->identityClass != 'webapi\models\ApiUser') {
                $this->employee_id = Yii::$app->user->identity->getId();
            }
        }

        if (parent::beforeSave($insert)) {


            if ($insert) {
                if(!$this->status) {
                    $this->status = self::STATUS_CREATED;
                }

                if(!$this->uid) {
                    $this->uid = uniqid();
                }

                if(!$this->employee_id && Yii::$app->user->id) {
                    $this->employee_id = Yii::$app->user->id;
                }

            }

            return true;
        }
        return false;
    }

    public static function parseDump($string, $validation = true, &$itinerary = [])
    {

        $depCity = $arrCity = null;
        $data = [];
        $segmentCount = 0;
        try {
            $rows = explode("\n", $string);
            foreach ($rows as $row) {
                $row = trim(preg_replace('!\s+!', ' ', $row));
                $rowArr = explode(' ', $row);
                if (!is_numeric($rowArr[0])) {
                    $rowArrAst = explode('*', $rowArr[0]);
                    if (count($rowArrAst) > 1) {
                        array_shift($rowArr);
                        for ($i = count($rowArrAst) - 1; $i >= 0; $i--) {
                            array_unshift($rowArr, $rowArrAst[$i]);
                        }
                    }
                }

                if (!is_numeric(intval($rowArr[0]))) continue;

                $segmentCount++;
                $carrier = substr($rowArr[1], 0, 2);
                $depAirport = '';
                $arrAirport = '';
                $depDate = '';
                $arrDate = '';
                $depDateTime = '';
                $arrDateTime = '';
                $flightNumber = '';

                $rowExpl = explode($carrier, $row);
                $rowFl = $rowExpl[1];
                preg_match('/([0-9]+)\D/', $rowFl, $matches);
                if (!empty($matches)) {
                    $flightNumber = $matches[1];
                }

                preg_match('/\W([A-Z]{6})\W/', $row, $matches);
                if (!empty($matches)) {
                    $depAirport = substr($matches[1], 0, 3);
                    $arrAirport = substr($matches[1], 3, 3);
                }

                preg_match_all("/[0-9]{2}(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)/", $row, $matches);
                if (!empty($matches)) {
                    if (empty($matches[0])) continue;
                    $depDate = $matches[0][0];
                    $arrDate = (isset($matches[0][1])) ? $matches[0][1] : $depDate;
                }

                $rowExpl = explode($depAirport . $arrAirport, $row);
                $rowTime = $rowExpl[1];
                preg_match_all('/([0-9]{3,4})(N|A|P)?(\+([0-9]))?/', $rowTime, $matches);
                if (!empty($matches)) {
                    $now = new \DateTime();
                    $matches[1][0] = substr_replace($matches[1][0], ':', -2, 0);
                    $matches[1][1] = substr_replace($matches[1][1], ':', -2, 0);

                    $date = $depDate . ' ' . $matches[1][0];
                    if ($matches[2][0] != '') {
                        $fAP = ($matches[2][0] == 'A') ? 'a' : 'A';
                        $date = $date . strtolower(str_replace('N', 'P', $matches[2][0])) . 'm';
                        $depDateTime = \DateTime::createFromFormat('jM g:i' . $fAP, $date);
                    } else {
                        $depDateTime = \DateTime::createFromFormat('jM H:i', $date);
                    }
                    if ($depDateTime == false) {
                        continue;
                    }
                    if ($now->format('m') > $depDateTime->format('m')) {
                        $depDateTime->add(\DateInterval::createFromDateString('+1 year'));
                    }

                    $date = $arrDate . ' ' . $matches[1][1];
                    if ($matches[2][1] != '') {
                        $fAP = ($matches[2][1] == 'A') ? 'a' : 'A';
                        $date = $date . strtolower(str_replace('N', 'P', $matches[2][1])) . 'm';
                        $arrDateTime = \DateTime::createFromFormat('jM g:i' . $fAP, $date);
                    } else {
                        $arrDateTime = \DateTime::createFromFormat('jM H:i', $date);
                    }
                    if ($matches[4][1] != '') {
                        $arrDateTime->add(new \DateInterval('P' . $matches[4][1] . 'D'));
                    }


                    if ($now->format('m') > $arrDateTime->format('m')) {
                        $arrDateTime->add(\DateInterval::createFromDateString('+1 year'));
                    }

                    if ($depDateTime > $arrDateTime) {
                        $arrDateTime->add(\DateInterval::createFromDateString('+1 year'));
                    }

                    $depCity = Airport::findIdentity($depAirport);
                    $arrCity = Airport::findIdentity($arrAirport);
                }

                $rowExpl = explode($depDate, $rowFl);
                $cabin = trim(str_replace($flightNumber, '', trim($rowExpl[0])));
                if ($depCity !== null && $arrCity !== null && $depCity->dst != $arrCity->dst) {
                    $flightDuration = ($arrDateTime->getTimestamp() - $depDateTime->getTimestamp()) / 60;
                    $flightDuration = intval($flightDuration) + (intval($depCity->dst) * 60) - (intval($arrCity->dst) * 60);
                } else {
                    $flightDuration = ($arrDateTime->getTimestamp() - $depDateTime->getTimestamp()) / 60;
                }

                $airline = Airline::findIdentity($carrier);

                $segment = [
                    'carrier' => $carrier,
                    'airlineName' => ($airline !== null)
                        ? $airline->name
                        : $carrier,
                    'departureAirport' => $depAirport,
                    'arrivalAirport' => $arrAirport,
                    'departureDateTime' => $depDateTime,
                    'arrivalDateTime' => $arrDateTime,
                    'flightNumber' => $flightNumber,
                    'bookingClass' => $cabin,
                    'departureCity' => $depCity,
                    'arrivalCity' => $arrCity,
                    'flightDuration' => $flightDuration,
                    'layoverDuration' => 0
                ];
                if (count($data) != 0 && isset($data[count($data) - 1])) {
                    $previewSegment = $data[count($data) - 1];
                    $segment['layoverDuration'] = ($segment['departureDateTime']->getTimestamp() - $previewSegment['arrivalDateTime']->getTimestamp()) / 60;
                }
                $data[] = $segment;
                $fSegment = new FlightSegment();
                $fSegment->airlineCode = $segment['carrier'];
                $fSegment->bookingClass = $segment['bookingClass'];
                $fSegment->flightNumber = $segment['flightNumber'];
                $fSegment->departureAirportCode = $segment['departureAirport'];
                $fSegment->destinationAirportCode = $segment['arrivalAirport'];
                $fSegment->departureTime = $segment['departureDateTime']->format('Y-m-d H:i:s');
                $fSegment->arrivalTime = $segment['arrivalDateTime']->format('Y-m-d H:i:s');
                $itinerary[] = $fSegment;
            }
            if ($validation) {
                if ($segmentCount !== count($data)) {
                    $data = [];
                }
            }
        } catch (ErrorException $ex) {
            $data = [];
        }

        return $data;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) {
            //Add logs after changed model attributes
            $leadLog = new LeadLog(new LeadLogMessage());
            $leadLog->logMessage->oldParams = $changedAttributes;
            $leadLog->logMessage->newParams = array_intersect_key($this->attributes, $changedAttributes);
            $leadLog->logMessage->title = $insert ? 'Create' : 'Update';
            $leadLog->logMessage->model = sprintf('%s (%s)', $this->formName(), $this->uid);
            $leadLog->addLog([
                'lead_id' => $this->lead_id,
            ]);


            if (isset($changedAttributes['status'])) {
                if ($this->lead->called_expert &&
                    $changedAttributes['status'] != $this->status &&
                    !in_array($this->status, [self::STATUS_APPLIED])
                ) {
                    $quote = Quote::findOne(['id' => $this->id]);
                    $data = $quote->getQuoteInformationForExpert(true);
                    BackOffice::sendRequest('lead/update-quote', 'POST', json_encode($data));
                }
            }
        }
    }

    public function getStatusLabel()
    {
        $label = '';
        $date = $this->updated;
        switch ($this->status) {
            case self::STATUS_CREATED:
                $label = '<span id="q-status-' . $this->uid . '" class="sl-quote__status status-label label label-primary" title="At ' . $date . '" data-toggle="tooltip">Created</span>';
                break;
            case self::STATUS_APPLIED:
                $label = '<span id="q-status-' . $this->uid . '" class="sl-quote__status status-label label label-success" title="At ' . $date . '" data-toggle="tooltip">Booked</span>';
                break;
            case self::STATUS_DECLINED:
                $label = '<span id="q-status-' . $this->uid . '" class="sl-quote__status status-label label label-danger" title="At ' . $date . '" data-toggle="tooltip">Declined</span>';
                break;
            case self::STATUS_SEND:
                $label = '<span id="q-status-' . $this->uid . '" class="sl-quote__status status-label label label-warning" title="At ' . $date . '" data-toggle="tooltip">Sent to client</span>';
                break;
            case self::STATUS_OPENED:
                $label = '<span id="q-status-' . $this->uid . '" class="sl-quote__status status-label label label-gold" title="At ' . $date . '" data-toggle="tooltip">Form opened</span>';
                break;
        }
        return $label;
    }

    /**
     * @param $newQuote self
     * @param $lead Lead
     * @return array|QuotePrice[]
     */
    public function cloneQuote(&$newQuote, $lead)
    {

        $prices = [];
        foreach ($lead->getPaxTypes() as $type) {
            $newQPrice = new QuotePrice();
            foreach ($this->quotePrices as $qPrice) {
                if ($qPrice->passenger_type == $type) {
                    $newQPrice->attributes = $qPrice->attributes;
                    break;
                }
            }
            $newQPrice->id = 0;
            $newQPrice->passenger_type = $type;
            $newQPrice->toMoney();
            $prices[] = $newQPrice;
        }
        $newQuote->attributes = $this->attributes;
        $newQuote->id = 0;
        $newQuote->record_locator = null;
        $newQuote->uid = null;
        $newQuote->status = self::STATUS_CREATED;
        return $prices;
    }

    /**
     * @return Airline|null
     */
    public function getMainCarrier()
    {
        return Airline::findIdentity($this->main_airline_code);
    }

    public function getTrips(&$title = null)
    {
        $trips = [];
        $tripIndex = 0;
        $segments = self::parseDump($this->reservation_dump, false);
        foreach ($segments as $key => $segment) {
            $segment['cabin'] = $this->cabin;
            if ($this->trip_type != Lead::TRIP_TYPE_ONE_WAY) {
                if ($key != 0) {
                    $lastSegment = isset($segments[$key - 1])
                        ? $segments[$key - 1] : $segments[$key];
                    $isMoreOneDay = $this->isMoreOneDay($lastSegment['arrivalDateTime'], $segment['departureDateTime']);
                    if ($isMoreOneDay) {
                        $tripIndex = $tripIndex + 1;
                    }
                }
            }
            $segment['departureCountry'] = ($segment['departureCity'] !== null)
                ? $segment['departureCity']->country : '';
            $segment['arrivalCountry'] = ($segment['arrivalCity'] !== null)
                ? $segment['arrivalCity']->country : '';
            $segment['departureCity'] = ($segment['departureCity'] !== null)
                ? $segment['departureCity']->city : '';
            $segment['arrivalCity'] = ($segment['arrivalCity'] !== null)
                ? $segment['arrivalCity']->city : '';
            $trips[$tripIndex]['segments'][] = $segment;
        }
        foreach ($trips as $key => $trip) {
            $routing = [];
            $routing[] = $trip['segments'][0]['departureAirport'];

            $trips[$key]['segments'][0]['layoverDuration'] = 0;

            $firstSegment = $trip['segments'][0];
            $lastSegment = $trip['segments'][count($trip['segments']) - 1];

            $depCity = Airport::findIdentity($firstSegment['departureAirport']);
            $arrCity = Airport::findIdentity($lastSegment['arrivalAirport']);

            if ($depCity !== null && $arrCity !== null && $depCity->dst != $arrCity->dst) {
                $flightDuration = ($lastSegment['arrivalDateTime']->getTimestamp() - $firstSegment['departureDateTime']->getTimestamp()) / 60;
                $trips[$key]['totalDuration'] = intval($flightDuration) + (intval($depCity->dst) * 60) - (intval($arrCity->dst) * 60);
            } else {
                $trips[$key]['totalDuration'] = ($lastSegment['arrivalDateTime']->getTimestamp() - $firstSegment['departureDateTime']->getTimestamp()) / 60;
            }

            foreach ($trip['segments'] as $segment) {
                $routing[] = $segment['arrivalAirport'];
            }
            $src = Airport::findIdentity($routing[min(array_keys($routing))]);
            $dst = Airport::findIdentity($routing[max(array_keys($routing))]);
            $trips[$key]['routing'] = implode('-', $routing);
            $trips[$key]['title'] = sprintf('%s - %s',
                ($src !== null) ? $src->city : $src,
                ($dst !== null) ? $dst->city : $dst
            );
        }
        if ($title !== null) {
            if ($this->trip_type != Lead::TRIP_TYPE_ONE_WAY) {
                if ($this->trip_type == Lead::TRIP_TYPE_ROUND_TRIP) {
                    $exp = explode('-', $trips[0]['title']);
                    if (isset($exp[0])) {
                        $title = $trips[0]['title'] . ' - ' . $exp[0];
                    }
                } else {
                    $title = sprintf('%s, %s', $trips[0]['title'], $trips[1]['title']);
                }
            } else {
                $title = $trips[0]['title'];
            }
        }
        return $trips;
    }

    private function isMoreOneDay(\DateTime $departureDateTime, \DateTime $arrivalDateTime)
    {
        $diff = $departureDateTime->diff($arrivalDateTime);
        return ((int)sprintf('%d%d%d', $diff->y, $diff->m, $diff->d) >= 1)
            ? true : false;
    }

    /**
     * @return string
     */
    public function getStatusName() : string
    {
        $statusName = self::STATUS_LIST[$this->status] ?? '-';
        return $statusName;
    }

    /**
     * @return string
     */
    public function getGdsName2() : string
    {
        $name = self::GDS_LIST[$this->gds] ?? '-';
        return $name;
    }

    public function beforeDelete()
    {
        foreach ($this->quotePrices as $quotePrice) {
            $quotePrice->delete();
        }

        return parent::beforeDelete();
    }

    public function quotePrice()
    {
        $result = [
            'detail' => [],
            'tickets' => count($this->quotePrices),
            'selling' => 0,
            'amountPerPax' => 0,
            'fare' => 0,
            'mark_up' => 0,
            'taxes' => 0,
            'currency' => 'USD'
        ];
        foreach ($this->quotePrices as $price) {
            $price->toFloat();
            $price->roundValue();

            if (!isset($result['detail'][$price->passenger_type]['selling'])) {
                $result['detail'][$price->passenger_type]['selling'] = $price->selling;
                $result['detail'][$price->passenger_type]['fare'] = $price->fare;
                $result['detail'][$price->passenger_type]['taxes'] = $price->taxes + $price->mark_up + $price->extra_mark_up;
                $result['detail'][$price->passenger_type]['tickets'] = 1;
            } else {
                $result['detail'][$price->passenger_type]['selling'] += $price->selling;
                $result['detail'][$price->passenger_type]['fare'] += $price->fare;
                $result['detail'][$price->passenger_type]['taxes'] += $price->taxes + $price->mark_up + $price->extra_mark_up;
                $result['detail'][$price->passenger_type]['tickets'] += 1;
            }

            $result['selling'] += $price->selling;
            $result['fare'] += $price->fare;
            $result['mark_up'] += $price->mark_up + $price->extra_mark_up;
            $result['taxes'] += $price->taxes;
        }

        foreach ($result['detail'] as $type => $item) {
            if (empty($result['amountPerPax']) && $type == QuotePrice::PASSENGER_ADULT) {
                $result['amountPerPax'] = ($item['selling'] / $item['tickets']);
            }
            $result['detail'][$type]['selling'] = ($item['selling'] / $item['tickets']);
            $result['detail'][$type]['fare'] = ($item['fare'] / $item['tickets']);
            $result['detail'][$type]['taxes'] = ($item['taxes'] / $item['tickets']);
        }

        $result['taxes'] = $result['taxes'] + $result['mark_up'];
        $result['selling'] = round($result['selling'], 2);
        $result['fare'] = round($result['fare'], 2);
        $result['taxes'] = round($result['taxes'], 2);
        $result['isCC'] = boolval(!$this->check_payment);
        return $result;
    }

    public function getQuoteInformationForExpert($single = false)
    {
        $qInformation = [
            'record_locator' => $this->record_locator,
            'pcc' => $this->pcc,
            'cabin' => $this->cabin,
            'gds' => $this->gds,
            'trip_type' => $this->trip_type,
            'main_airline_code' => $this->main_airline_code,
            'reservation_dump' => $this->reservation_dump,
            'status' => $this->status,
            'check_payment' => $this->check_payment,
            'fare_type' => $this->fare_type,
            'employee_name' => ($this->created_by_seller)
                ? $this->employee->username : $this->employee_name
        ];

        $pQInformation = [];
        foreach ($this->quotePrices as $quotePrice) {
            $pQInformation[] = [
                'uid' => $quotePrice->uid,
                'information' => [
                    'passenger_type' => $quotePrice->passenger_type,
                    'selling' => $quotePrice->selling,
                    'net' => $quotePrice->net,
                    'fare' => $quotePrice->fare,
                    'taxes' => $quotePrice->taxes,
                    'mark_up' => $quotePrice->mark_up,
                    'extra_mark_up' => $quotePrice->extra_mark_up
                ]
            ];
        }

        if (!$single) {
            return [
                'uid' => $this->uid,
                'created_by_seller' => $this->created_by_seller,
                'information' => $qInformation,
                'LeadQuotePrice' => $pQInformation
            ];
        } else {
            return [
                'LeadRequest' => [
                    'uid' => $this->lead->uid,
                    'market_info_id' => $this->lead->source->id
                ],
                'LeadQuote' => [
                    'uid' => $this->uid,
                    'created_by_seller' => $this->created_by_seller,
                    'information' => $qInformation
                ],
                'LeadQuotePrice' => $pQInformation
            ];
        }
    }
}
