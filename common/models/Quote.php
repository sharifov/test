<?php

namespace common\models;

use common\components\BackOffice;
use common\models\local\FlightSegment;
use common\models\local\LeadLogMessage;
use Yii;
use yii\base\ErrorException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\db\Query;
use yii\helpers\VarDumper;
use common\components\SearchService;

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
 * @property string $pricing_info
 * @property int $status
 * @property boolean $check_payment
 * @property string $fare_type
 * @property string $created
 * @property string $updated
 * @property boolean $created_by_seller
 * @property string $employee_name
 * @property string $last_ticket_date
 * @property double $service_fee_percent
 *
 * @property QuotePrice[] $quotePrices
 * @property int $quotePricesCount
 * @property Employee $employee
 * @property Lead $lead
 * @property QuoteTrip[] $quoteTrips
 */
class Quote extends \yii\db\ActiveRecord
{
    public const SERVICE_FEE = 0.035;

    public const
        GDS_SABRE = 'S',
        GDS_AMADEUS = 'A',
        GDS_WORLDSPAN = 'W';

    public CONST GDS_LIST = [
        self::GDS_SABRE => 'Sabre',
        self::GDS_AMADEUS => 'Amadeus',
        self::GDS_WORLDSPAN => 'WorldSpan',
    ];

    public const
        FARE_TYPE_PUB = 'PUB',
        FARE_TYPE_SR = 'SR',
        FARE_TYPE_SRU = 'SRU',
        FARE_TYPE_COMM = 'COMM',
        FARE_TYPE_TOUR= 'TOUR',
        FARE_TYPE_PUBC = 'PUBC';

    public const
        STATUS_CREATED = 1,
        STATUS_APPLIED = 2,
        STATUS_DECLINED = 3,
        STATUS_SEND = 4,
        STATUS_OPENED = 5;


    public CONST STATUS_LIST = [
        self::STATUS_CREATED => 'New',
        self::STATUS_APPLIED => 'Applied',
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_SEND => 'Sent',
        self::STATUS_OPENED => 'Opened'
    ];

    public CONST STATUS_CLASS_LIST = [
        self::STATUS_CREATED => 'lq-created',
        self::STATUS_APPLIED => 'lq-applied',
        self::STATUS_DECLINED => 'lq-declined',
        self::STATUS_SEND => 'lq-send',
        self::STATUS_OPENED => 'lq-opened'
    ];

    public CONST STATUS_CLASS_SPAN = [
        self::STATUS_CREATED => 'status-new',
        self::STATUS_APPLIED => 'status-applied',
        self::STATUS_DECLINED => 'status-declined',
        self::STATUS_SEND => 'status-send',
        self::STATUS_OPENED => 'status-opened'
    ];

    public CONST
        PASSENGER_ADULT = 'ADT',
        PASSENGER_CHILD = 'CHD',
        PASSENGER_INFANT = 'INF';

    public $itinerary = [];
    public $hasFreeBaggage = false;
    public $freeBaggageInfo;
    public $freeBaggageInfo2;
    public $hasAirportChange = false;
    public $hasOvernight = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotes';
    }

    public static function getGDSName($gds = null)
    {
        $mapping = self::GDS_LIST;

        if ($gds === null) {
            return $mapping;
        }

        return isset($mapping[$gds]) ? $mapping[$gds] : $gds;
    }

    public static function getFareType($fareType = null)
    {
        $mapping = [
            self::FARE_TYPE_PUB => 'Published - no commission',
            self::FARE_TYPE_PUBC => 'Published - with commission',
            self::FARE_TYPE_SR => 'Private - Limited markup',
            self::FARE_TYPE_TOUR => 'Tour Fare',
            self::FARE_TYPE_COMM => 'SPLIT MCO'
        ];

        if ($fareType === null) {
            return $mapping;
        }

        return isset($mapping[$fareType]) ? $mapping[$fareType] : $fareType;
    }

    /**
     * @return string
     */
    public function getStatusLabelClass(): string
    {
        return self::STATUS_CLASS_LIST[$this->status] ?? 'label-default';
    }

    public static function getProfit($markUp, $sellingPrice, $fare_type, $check_payment = true, $processing_fee = 0)
    {
        $fare_type = empty($fare_type)
            ? self::FARE_TYPE_PUB : $fare_type;
        $profit = $markUp;
        $profit -= $processing_fee;
        return $profit;
    }

    /**
     * @return float
     */
    public function getEstimationProfit()
    {
        $priceData = $this->getPricesData();
        $profit = 0;
        $markUp = $priceData['total']['mark_up'] + $priceData['total']['extra_mark_up'];
        $sellingPrice = $priceData['total']['selling'];
        $checkPayment = $this->check_payment;
        $processingFee = $priceData['processing_fee'];
        /* $serviceFee = $this->getServiceFeePercent();
        if($serviceFee > 0){
            $serviceFee = $serviceFee/100;
        } */

        $profit += $markUp;
        $profit -= $processingFee;

        return round($profit,2);
    }

    public function getFinalProfit()
    {
        $final = $this->lead->final_profit;
        if($this->lead->agents_processing_fee){
            $final -= $this->lead->agents_processing_fee;
        }else{
            $final -= ($this->lead->adults + $this->lead->children)*Lead::AGENT_PROCESSING_FEE_PER_PAX;
        }
        return $final;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteTrips()
    {
        return $this->hasMany(QuoteTrip::className(), ['qt_quote_id' => 'id']);
    }

    public function getDataForProfit($quoteId)
    {
        $query = new Query();

        $query
        ->select(['selling' => 'SUM(qp.selling)',
            'mark_up' => 'SUM(qp.mark_up + qp.extra_mark_up)',
            'fare_type' => 'q.fare_type',
            'check_payment' => 'q.check_payment'
        ])
        ->from(Quote::tableName().' q')
        ->leftJoin(QuotePrice::tableName().' qp','q.id = qp.quote_id')
        ->where(['q.id' => $quoteId]);

        return $query->one();
    }

    public static function countProfit($id)
    {
        $data = self::getDataForProfit($id);
        return self::getProfit($data['mark_up'], $data['selling'],$data['fare_type'],$data['check_payment']);
    }

    public function getTotalProfit()
    {
        if ($this->lead->final_profit !== null) {
            return $this->lead->final_profit;
        }

        $priceData = $this->getPricesData();
        return self::getProfit($priceData['total']['mark_up'] + $priceData['total']['extra_mark_up'], $priceData['total']['selling'],$this->fare_type,$this->check_payment);
        //return self::countProfit($this->id);
    }

    /**
     * @return int
     */
    public function getProcessingFee()
    {
        if($this->lead->agents_processing_fee)
            return $this->lead->agents_processing_fee;

        if(!$this->employee){
            $employee = $this->lead->employee;
            if(!$employee) return 0;

            $groups = $employee->ugsGroups;
            return ($groups)?$groups[0]->ug_processing_fee:0;
        }

        $groups = $this->employee->ugsGroups;
        return ($groups)?$groups[0]->ug_processing_fee:0;
    }

    public function isEditable()
    {
        if($this->status == self::STATUS_CREATED)
            return true;

        return false;
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

            if ($flightSegment->operationAirlineCode) {
                $segment .= " OPERATED BY " . $flightSegment->operationAirlineCode;
            }

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
            [['uid', 'reservation_dump', 'main_airline_code'], 'required'],
            [['lead_id', 'status' ], 'integer'],
            [[ 'check_payment'], 'boolean'],
            [['created', 'updated', 'reservation_dump', 'created_by_seller', 'employee_name', 'employee_id', 'pcc', 'gds', 'last_ticket_date', 'service_fee_percent'], 'safe'],
            [['uid', 'record_locator', 'pcc', 'cabin', 'gds', 'trip_type', 'main_airline_code', 'fare_type'], 'string', 'max' => 255],

            [['reservation_dump'], 'checkReservationDump'],
            [['pricing_info'], 'string'],
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
            'last_ticket_date' => 'Last Ticket Date',
            'service_fee_percent' => 'Service Fee Percent',
            'reservation_dump' => 'Reservation Dump',
            'pricing_info' => 'Pricing info',
        ];
    }

    public function behaviors(): array
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
     * @return int
     */
    public function getQuotePricesCount(): int
    {
        return $this->hasMany(QuotePrice::class, ['quote_id' => 'id'])->count();
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


    public function beforeSave($insert): bool
    {
        if ($this->isNewRecord) {
            $this->uid = empty($this->uid) ? uniqid() : $this->uid;
            /*if (!Yii::$app->user->isGuest && Yii::$app->user->identityClass != 'webapi\models\ApiUser' && empty($this->employee_id)) {
                $this->employee_id = Yii::$app->user->id;
            }*/
        }

        if (parent::beforeSave($insert)) {


            if ($insert) {
                if (!$this->status) {
                    $this->status = self::STATUS_CREATED;
                }

                if (!$this->uid) {
                    $this->uid = uniqid();
                }

                /*                if(!$this->employee_id && Yii::$app->user->id) {
                                    $this->employee_id = Yii::$app->user->id;
                                }*/

            }

            return true;
        }
        return false;
    }

    public static function parseDump($string, $validation = true, &$itinerary = [], $onView = false)
    {

        if (!empty($itinerary) && $validation) {
            $itinerary = [];
        }

        $depCity = $arrCity = null;
        $data = [];
        $segmentCount = 0;
        $operatedCnt = 0;
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

                if (stripos($rowArr[0], "OPERATED") !== false) {
                    $idx = count($itinerary);
                    if($idx > 0){
                        $idx--;
                    }
                    if (isset($data[$idx]) && isset($itinerary[$idx])) {
                        $operatedCnt++;
                        $position = stripos($row, "OPERATED BY");
                        $operatedBy = trim(substr($row, $position));
                        $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                        $data[$idx]['operatingAirline'] = $operatedBy;
                        $itinerary[$idx]->operationAirlineCode = $operatedBy;
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
                $arrDateInRow = false;
                $operationAirlineCode = '';

                if (stripos($row, "OPERATED BY") !== false) {
                    $position = stripos($row, "OPERATED BY");
                    $operatedBy = trim(substr($row, $position));
                    $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                    $operationAirlineCode = $operatedBy;
                }

                $posCarr = stripos($row, $carrier);
                $rowFl = substr($row, $posCarr+strlen($carrier));
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
                    if (isset($matches[0][1])) {
                        $arrDateInRow = true;
                    }
                    $arrDate = (isset($matches[0][1])) ? $matches[0][1] : $depDate;
                }

                $rowExpl = explode($depAirport . $arrAirport, $row);
                $rowTime = $rowExpl[1];
                preg_match_all('/([0-9]{3,4})(N|A|P)?(\+([0-9])?)?/', $rowTime, $matches);
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

                    $arrDepDiff = $depDateTime->diff($arrDateTime);
                    if ($arrDepDiff->d == 0 && !$arrDateInRow) {
                        if ($matches[3][1] == "+") {
                            $arrDateTime->add(\DateInterval::createFromDateString('+1 day'));
                        } elseif (strpos($matches[3][1], "+")) {
                            $arrDateTime->add(\DateInterval::createFromDateString($matches[3][1] . ' day'));
                        }
                    }

                    if ($matches[4][1] != '') {
                        $arrDateTime->add(new \DateInterval('P' . $matches[4][1] . 'D'));
                    }

                    if ($now->format('m') > $arrDateTime->format('m')) {
                        $arrDateTime->add(\DateInterval::createFromDateString('+1 year'));
                    }

                    if ($depDateTime > $arrDateTime) {
                        $arrDepDiff = $depDateTime->diff($arrDateTime);
                        if($arrDepDiff->invert == 1 && $arrDepDiff->d > 0){
                            $arrDateTime->add(\DateInterval::createFromDateString('+1 year'));
                        }
                    }

                    $depCity = $arrCity = null;
                    if (!$onView) {
                        $depCity = Airport::findIdentity($depAirport);
                        $arrCity = Airport::findIdentity($arrAirport);
                    }
                }

                $rowExpl = explode($depDate, $rowFl);
                $cabin = trim(str_replace($flightNumber, '', trim($rowExpl[0])));
                if ($depCity !== null && $arrCity !== null && $depCity->dst != $arrCity->dst) {
                    $flightDuration = ($arrDateTime->getTimestamp() - $depDateTime->getTimestamp()) / 60;
                    $flightDuration = intval($flightDuration) + (intval($depCity->dst) * 60) - (intval($arrCity->dst) * 60);
                } else {
                    $flightDuration = ($arrDateTime->getTimestamp() - $depDateTime->getTimestamp()) / 60;
                }

                $airline = null;
                if (!$onView) {
                    $airline = Airline::findIdentity($carrier);
                }

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
                if(!empty($airline)){
                    $segment['cabin'] = $airline->getCabinByClass($cabin);
                }
                if (!empty($operationAirlineCode)) {
                    $segment['operatingAirline'] = $operationAirlineCode;
                }
                if (count($data) != 0 && isset($data[count($data) - 1])) {
                    $previewSegment = $data[count($data) - 1];
                    $segment['layoverDuration'] = ($segment['departureDateTime']->getTimestamp() - $previewSegment['arrivalDateTime']->getTimestamp()) / 60;
                }
                $data[] = $segment;
                $fSegment = new FlightSegment();
                $fSegment->airlineCode = $segment['carrier'];
                $fSegment->bookingClass = $segment['bookingClass'];
                if(isset($segment['cabin']) && !empty($segment['cabin'])){
                    $fSegment->cabin = $segment['cabin'];
                }
                $fSegment->flightNumber = $segment['flightNumber'];
                $fSegment->departureAirportCode = $segment['departureAirport'];
                $fSegment->destinationAirportCode = $segment['arrivalAirport'];
                $fSegment->departureTime = $segment['departureDateTime']->format('Y-m-d H:i:s');
                $fSegment->arrivalTime = $segment['arrivalDateTime']->format('Y-m-d H:i:s');
                if (!empty($operationAirlineCode)) {
                    $fSegment->operationAirlineCode = $operationAirlineCode;
                }
                $itinerary[] = $fSegment;
            }
            if ($validation) {
                //echo sprintf('Check %d - %d - %d', $segmentCount, count($data), $operatedCnt);
                if ($segmentCount !== count($data) + $operatedCnt) {
                    $data = [];
                }
            }
        } catch (ErrorException $ex) {
            $data = [];
        }

        return $data;
    }

    public function getTripsSegmentsData()
    {
        $trips = [];
        $segments = [];
        $segmentCount = 0;
        $operatedCnt = 0;

        $rows = explode("\n", $this->reservation_dump);
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

            if (stripos($rowArr[0], "OPERATED") !== false) {
                $idx = count($segments);
                if($idx > 0){
                    $idx--;
                }
                if (isset($segments[$idx])) {
                    $operatedCnt++;
                    $position = stripos($row, "OPERATED BY");
                    $operatedBy = trim(substr($row, $position));
                    $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                    preg_match('/\((.*?)\)/', $operatedBy, $matches);
                    if (!empty($matches)) {
                        $operatedBy = trim($matches[1]);
                    }
                    if(mb_strlen($operatedBy) > 2){
                        $airline = Airline::find()->andWhere(['like' ,'name', $operatedBy ])->one();
                        if(!empty($airline)){
                            $operatedBy = $airline->iata;
                        }
                    }
                    $segments[$idx]['operatingAirline'] = str_replace('/', '', $operatedBy);
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
            $arrDateInRow = false;
            $operationAirlineCode = '';

            if (stripos($row, "OPERATED BY") !== false) {
                $position = stripos($row, "OPERATED BY");
                $operatedBy = trim(substr($row, $position));
                $operatedBy = trim(str_ireplace("OPERATED BY", "", $operatedBy));
                preg_match('/\((.*?)\)/', $operatedBy, $matches);
                if (!empty($matches)) {
                    $operatedBy = trim($matches[1]);
                }
                if(mb_strlen($operatedBy) > 2){
                    $airline = Airline::find()->andWhere(['like' ,'name', $operatedBy ])->one();
                    if(!empty($airline)){
                        $operatedBy = $airline->iata;
                    }
                }
                $operationAirlineCode = str_replace('/', '', $operatedBy);
            }

            $posCarr = stripos($row, $carrier);
            $rowFl = substr($row, $posCarr+strlen($carrier));

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
                if (isset($matches[0][1])) {
                    $arrDateInRow = true;
                }
                $arrDate = (isset($matches[0][1])) ? $matches[0][1] : $depDate;
            }

            $rowExpl = explode($depAirport . $arrAirport, $row);
            $rowTime = $rowExpl[1];
            preg_match_all('/([0-9]{3,4})(N|A|P)?(\+([0-9])?)?/', $rowTime, $matches);
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

                $arrDepDiff = $depDateTime->diff($arrDateTime);
                if ($arrDepDiff->d == 0 && !$arrDateInRow) {
                    if ($matches[3][1] == "+") {
                        $arrDateTime->add(\DateInterval::createFromDateString('+1 day'));
                    } elseif (strpos($matches[3][1], "+")) {
                        $arrDateTime->add(\DateInterval::createFromDateString($matches[3][1] . ' day'));
                    }
                }

                if ($matches[4][1] != '') {
                    $arrDateTime->add(new \DateInterval('P' . $matches[4][1] . 'D'));
                }

                if ($now->format('m') > $arrDateTime->format('m')) {
                    $arrDateTime->add(\DateInterval::createFromDateString('+1 year'));
                }

                if ($depDateTime > $arrDateTime) {
                    $arrDepDiff = $depDateTime->diff($arrDateTime);
                    if($arrDepDiff->invert == 1 && $arrDepDiff->d > 0){
                        $arrDateTime->add(\DateInterval::createFromDateString('+1 year'));
                    }
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
                'qs_departure_airport_code' => $depAirport,
                'qs_arrival_airport_code' => $arrAirport,
                'qs_departure_time' => $depDateTime,
                'qs_arrival_time' => $arrDateTime,
                'qs_flight_number' => $flightNumber,
                'qs_booking_class' => trim($cabin),
                'qs_duration' => $flightDuration,
                'qs_marketing_airline' => $carrier,
                'qs_operating_airline' => (!empty($operationAirlineCode))?$operationAirlineCode:$carrier,
            ];
            $segment['qs_key'] = '#'.$segment['qs_flight_number'].$segment['qs_departure_airport_code'].'-'.$segment['qs_arrival_airport_code'].' '.$segment['qs_departure_time']->format('Y-m-d H:i');

            if(!empty($airline)){
                $segment['qs_cabin'] = QuoteSegment::getCabinReal($airline->getCabinByClass($cabin));
            }
            if(!isset($segment['qs_cabin'])){
                $segment['qs_cabin'] = QuoteSegment::getCabinReal($this->cabin);
            }
            $segments[] = $segment;
        }

        $tripIndex = 0;
        foreach ($segments as $key => $segment) {
            if ($this->trip_type != Lead::TRIP_TYPE_ONE_WAY) {
                if ($key != 0) {
                    $lastSegment = isset($segments[$key - 1])
                    ? $segments[$key - 1] : $segments[$key];
                    $isMoreOneDay = $this->isMoreOneDay($lastSegment['qs_arrival_time'], $segment['qs_departure_time']);
                    if ($isMoreOneDay) {
                        $tripIndex = $tripIndex + 1;
                    }
                }
            }
            $segment['qs_departure_time'] = $segment['qs_departure_time']->format('Y-m-d H:i');
            $segment['qs_arrival_time'] = $segment['qs_arrival_time']->format('Y-m-d H:i');
            $trips[$tripIndex]['segments'][] = $segment;
        }

        foreach ($trips as $key => $trip) {
            $firstSegment = $trip['segments'][0];
            $lastSegment = $trip['segments'][count($trip['segments']) - 1];

            $depCity = Airport::findIdentity($firstSegment['qs_departure_airport_code']);
            $arrCity = Airport::findIdentity($lastSegment['qs_arrival_airport_code']);
            $arrivalTime = new \DateTime($lastSegment['qs_arrival_time']);
            $departureTime = new \DateTime($firstSegment['qs_departure_time']);

            if ($depCity !== null && $arrCity !== null && $depCity->dst != $arrCity->dst) {
                $flightDuration = ($arrivalTime->getTimestamp() - $departureTime->getTimestamp()) / 60;
                $trips[$key]['qt_duration'] = intval($flightDuration) + (intval($depCity->dst) * 60) - (intval($arrCity->dst) * 60);
            } else {
                $trips[$key]['qt_duration'] = ($arrivalTime->getTimestamp() - $departureTime->getTimestamp()) / 60;
            }

            $keySegment = [];
            foreach ($trip['segments'] as $segment){
                $keySegment[] = $segment['qs_key'];
            }
            $trips[$key]['qt_key'] = implode('|', $keySegment);
        }

        return $trips;
    }

    public function createQuoteTrips()
    {
        if($this->getQuoteTrips()->count() == 0){
            $data = $this->getTripsSegmentsData();
            if(!empty($data)){
                $transaction = self::getDb()->beginTransaction();

                foreach ($data as $tripEntry){
                    $trip = new QuoteTrip();
                    $trip->qt_duration = $tripEntry['qt_duration'];
                    $trip->qt_key = $tripEntry['qt_key'];

                    if(!$trip->validate()){
                        Yii::error('QuoteUid: '.$this->uid.'<br/>'.VarDumper::dumpAsString($trip->getErrors()).'<br/>dump: '.$this->reservation_dump, 'QuoteModel:createQuoteTrips:QuoteTrip:save');
                        $transaction->rollBack();
                        return false;
                    }
                    $this->link('quoteTrips', $trip);

                    if(isset($tripEntry['segments']) && is_array($tripEntry['segments'])) {
                        foreach ($tripEntry['segments'] as $segmentEntry){
                            $segment = new QuoteSegment();
                            $segment->attributes = $segmentEntry;
                            if(!$segment->validate()){
                                Yii::error('QuoteUid: '.$this->uid.'<br/>'.VarDumper::dumpAsString($segmentEntry).VarDumper::dumpAsString($segment->getErrors()).'<br/>dump: '.$this->reservation_dump, 'QuoteModel:createQuoteTrips:QuoteSegment:save');
                                $transaction->rollBack();
                                return false;
                            }
                            $trip->link('quoteSegments', $segment);
                        }
                    }
                }
                $transaction->commit();
                return true;
            }
        }

        return false;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->getBaggageInfo();
        $this->getHasAirportChange();
        //$this->getHasOvernight();
    }

    public function getHasOvernight()
    {
        if(!empty($this->quoteTrips)){
            foreach ($this->quoteTrips as $trip){
                if(!empty($trip->quoteSegments) && count($trip->quoteSegments) > 1){
                    foreach ($trip->quoteSegments as $segment){
                        if($segment->getIsOVernight()){
                            $this->hasOvernight = true;
                        }
                    }
                }
            }
        }
        return $this->hasOvernight;
    }

    public function getHasAirportChange()
    {
        if(!empty($this->quoteTrips)){
            foreach ($this->quoteTrips as $trip){
                if(!empty($trip->quoteSegments) && count($trip->quoteSegments) > 1){
                    $previousSegment = null;
                    foreach ($trip->quoteSegments as $segment){
                        if($previousSegment !== null && $segment->qs_departure_airport_code != $previousSegment->qs_arrival_airport_code){
                            $this->hasAirportChange = true;
                            break;
                        }
                        $previousSegment = $segment;
                    }
                }
            }
        }
        return $this->hasAirportChange;
    }

    public function getBaggageInfo()
    {
        //if one segment has baggage -> quote has baggage
        if(!empty($this->quoteTrips)){
            foreach ($this->quoteTrips as $trip){
                if(!empty($trip->quoteSegments)){
                    foreach ($trip->quoteSegments as $segment){
                        if(!empty($segment->quoteSegmentBaggages)){
                            foreach ($segment->quoteSegmentBaggages as $baggage){
                                if(($baggage->qsb_allow_pieces && $baggage->qsb_allow_pieces > 0)){
                                    $this->freeBaggageInfo = $baggage->qsb_allow_pieces.' pcs';
                                }elseif ($baggage->qsb_allow_weight){
                                    $this->freeBaggageInfo = $baggage->qsb_allow_weight.$baggage->qsb_allow_unit;
                                }

                                if($this->freeBaggageInfo) {
                                    $this->hasFreeBaggage = true;
                                    return ['hasFreeBaggage' => $this->hasFreeBaggage, 'freeBaggageInfo' => $this->freeBaggageInfo];
                                }
                            }
                        }
                    }
                }
            }
        }
        return ['hasFreeBaggage' => $this->hasFreeBaggage, 'freeBaggageInfo' => $this->freeBaggageInfo];
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function getBaggageInfo2() : array
    {

        //$caryOn = 1;
        //$checked = random_int(0, 1);

        //if one segment has baggage -> quote has baggage
        if($this->quoteTrips){
            foreach ($this->quoteTrips as $trip){
                if(!empty($trip->quoteSegments)){
                    foreach ($trip->quoteSegments as $segment){
                        if(!empty($segment->quoteSegmentBaggages)){
                            foreach ($segment->quoteSegmentBaggages as $baggage){
                                if($baggage->qsb_allow_pieces && $baggage->qsb_allow_pieces > 0){
                                    $this->freeBaggageInfo2 = $baggage->qsb_allow_pieces;
                                } elseif ($baggage->qsb_allow_weight) {
                                    $this->freeBaggageInfo2 = $baggage->qsb_allow_weight.$baggage->qsb_allow_unit;
                                }

                                if($this->freeBaggageInfo2){
                                    $this->hasFreeBaggage = true;
                                    //return ['hasFreeBaggage' => $this->hasFreeBaggage, 'freeBaggageInfo' => $this->freeBaggageInfo2];
                                }
                            }
                        }
                    }
                }
            }
        }

        return ['carryOn' => $caryOn, 'checked' => $checked];
    }



    public function getBaggageInfoByTrip(QuoteTrip $trip) : array
    {

        $caryOn = 1;
        $checked = 0;

        if($trip->quoteSegments){
            foreach ($trip->quoteSegments as $segment){
                if(!empty($segment->quoteSegmentBaggages)){
                    foreach ($segment->quoteSegmentBaggages as $baggage){
                        if($baggage->qsb_allow_pieces > 0) {
                            $checked = $baggage->qsb_allow_pieces;
                        }
                        /*elseif ($baggage->qsb_allow_weight) {
                            $this->freeBaggageInfo2 = $baggage->qsb_allow_weight.$baggage->qsb_allow_unit;
                        }*/
                    }
                }
            }
        }


        return ['carryOn' => $caryOn, 'checked' => $checked];
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
                QuoteStatusLog::createNewFromQuote($this);
            }
        } else {
            QuoteStatusLog::createNewFromQuote($this);

            $quote = Quote::findOne(['id' => $this->id]);
            $data = $quote->getQuoteInformationForExpert(true);
            BackOffice::sendRequest('lead/update-quote', 'POST', json_encode($data));
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

    public function getStatusSpan()
    {
        $class = self::STATUS_CLASS_SPAN[$this->status] ??  '';
        $label = self::STATUS_LIST[$this->status] ?? '-';

        return '<span id="q-status-' . $this->uid . '" class="quote__status '.$class.'" title="' . Yii::$app->formatter->asDatetime($this->updated) . '" data-toggle="tooltip"><i class="fa fa-circle"></i> <span>'.$label.'</span></span>';
    }


    public function getLabelByStatus(int $status)
    {
        $class = self::STATUS_CLASS_LIST[$status];

        $statusName = self::STATUS_LIST[$status] ?? '-';

        return '<span class="label ' . $class . '" style="font-size: 13px">' . Html::encode($statusName) . '</span>';
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
            $newQPrice->uid = null;
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

    public function getQuoteTripsData()
    {
        $trips = [];

        if(empty($this->quoteTrips)){
            return $this->getTripsFromDumpLikeSearch();
        }

        foreach ($this->quoteTrips as $tripKey => $trip){
            $segments = [];
            foreach ($trip->quoteSegments as $keySegm => $segment) {

                $airline = Airline::findIdentity($segment->qs_marketing_airline);
                $departureDateTime = new \DateTime($segment->qs_departure_time);
                $arrivalDateTime = new \DateTime($segment->qs_arrival_time);
                $baggages = $segment->quoteSegmentBaggages;
                $baggageCharge = $segment->quoteSegmentBaggageCharges;

                $baggageInfo = [];
                if(count($baggages)){
                    foreach ($baggages as $baggageEntry){
                        $baggageInfo[$baggageEntry->qsb_pax_code] = $baggageEntry->getInfo();
                    }
                }
                if(count($baggageCharge)){
                    foreach ($baggageCharge as $baggageChEntry){
                        $baggageInfo[$baggageChEntry->qsbc_pax_code]['charge'] = $baggageChEntry->getInfo();
                    }
                }
                $stops = [];
                if(count($segment->quoteSegmentStops)){
                    foreach ($segment->quoteSegmentStops as $stopEntry){
                        $stops[] = $stopEntry->getInfo();
                    }
                }
                $segments[] = [
                    'segmentId' => $keySegm+1,
                    'departureTime' => $departureDateTime->format('Y-m-d H:i'),
                    'arrivalTime' => $arrivalDateTime->format('Y-m-d H:i'),
                    'stop' => ($segment->qs_stop)?$segment->qs_stop:0,
                    'stops' => (count($stops))?$stops:null,
                    'flightNumber' => $segment->qs_flight_number,
                    'bookingClass' => $segment->qs_booking_class,
                    'duration' => $segment->qs_duration,
                    'departureAirportCode' => $segment->qs_departure_airport_code,
                    'departureAirportTerminal' => $segment->qs_departure_airport_terminal."",
                    'arrivalAirportCode' => $segment->qs_arrival_airport_code,
                    'arrivalAirportTerminal' => $segment->qs_arrival_airport_terminal."",
                    'operatingAirline' => $segment->qs_operating_airline,
                    'airEquipType' => $segment->qs_air_equip_type,
                    'marketingAirline' => $segment->qs_marketing_airline,
                    'cabin' => $segment->qs_cabin,
                    'baggage' => $baggageInfo,
                ];
            }
            $trips[] = [
                'tripId' => $tripKey+1,
                'segments' => $segments,
                'duration' => $trip->qt_duration,
            ];
        }

        return $trips;
    }

    public function getTrips(&$title = null)
    {
        $trips = [];

        if(empty($this->quoteTrips)){
            return $this->getTripsFromDump($title);
        }

        foreach ($this->quoteTrips as $trip){
            $segments = [];
            $routing = [];
            foreach ($trip->quoteSegments as $keySegm => $segment) {
                if($keySegm == 0){
                    $routing[] = $segment->qs_departure_airport_code;
                }
                $routing[] = $segment->qs_arrival_airport_code;
                $airline = Airline::findIdentity($segment->qs_marketing_airline);
                $departureDateTime = new \DateTime($segment->qs_departure_time);
                $arrivalDateTime = new \DateTime($segment->qs_arrival_time);
                $operatingAirline = Airline::findIdentity($segment->qs_operating_airline);
                $baggages = $segment->quoteSegmentBaggages;
                $baggageCharge = $segment->quoteSegmentBaggageCharges;

                $baggageInfo = [];
                if(count($baggages)){
                    foreach ($baggages as $baggageEntry){
                        $baggageInfo[$baggageEntry->qsb_pax_code] = $baggageEntry->getInfo();
                    }
                }
                if(count($baggageCharge)){
                    foreach ($baggageCharge as $baggageChEntry){
                        $baggageInfo[$baggageChEntry->qsbc_pax_code]['charge'] = $baggageChEntry->getInfo();
                    }
                }
                $segments[] = [
                    'cabin' => $segment->qs_cabin,
                    'carrier' => $segment->qs_marketing_airline,
                    'airlineName' => ($airline)?$airline->name:'',
                    'departureAirport' => $segment->qs_departure_airport_code,
                    'departureCity' => ($segment->departureAirport)?$segment->departureAirport->city:'',
                    'departureCountry' => ($segment->departureAirport)?$segment->departureAirport->country:'',
                    'arrivalAirport' => $segment->qs_arrival_airport_code,
                    'arrivalCity' => ($segment->arrivalAirport)?$segment->arrivalAirport->city:'',
                    'arrivalCountry' => ($segment->arrivalAirport)?$segment->arrivalAirport->country:'',
                    'departureDateTime' => $departureDateTime,
                    'arrivalDateTime' => $arrivalDateTime,
                    'flightNumber' => $segment->qs_flight_number,
                    'bookingClass' => $segment->qs_booking_class,
                    'flightDuration' => $segment->qs_duration,
                    'operatingAirline' => ($segment->qs_operating_airline != $segment->qs_marketing_airline)?(($operatingAirline)?$operatingAirline->name:$segment->qs_operating_airline):null,
                    'layoverDuration' => ($keySegm > 0)?(($departureDateTime->getTimestamp() - $segments[$keySegm-1]['arrivalDateTime']->getTimestamp())/60):0,
                    'stop' => $segment->qs_stop,
                    'baggage' => $baggageInfo,
                ];
            }
            $trips[] = [
                'segments' => $segments,
                'stops' => $trip->stops,
                'totalDuration' => $trip->qt_duration,
                'routing' => implode('-', $routing),
                'title' => $segments[0]['departureCity'].' - '.$segments[count($segments)-1]['arrivalCity'],
                ];
        }

        return $trips;
    }

    public function getInfoForEmail()
    {
        $trips = [];

        foreach ($this->quoteTrips as $trip){
            $firstSegment = null;
            $lastSegment = null;

            $segments = $trip->quoteSegments;
            if( $segments ) {
                $segmentsCnt = count($segments);
                $stopCnt = $segmentsCnt - 1;
                $firstSegment = $segments[0];
                $lastSegment = end($segments);
                $marketingAirlines = [];
                foreach ($segments as $segment){
                    if(isset($segment->qs_stop) && $segment->qs_stop > 0){
                        $stopCnt += $segment->qs_stop;
                    }
                    if(!in_array($segment->qs_marketing_airline, $marketingAirlines)){
                        $marketingAirlines[] = $segment->qs_marketing_airline;
                    }
                }
            } else {
                continue;
            }
            $datediff = strtotime($lastSegment->qs_arrival_time) - strtotime($firstSegment->qs_departure_time);

            $trips[] = [
                'airline' => (count($marketingAirlines) == 1)?$marketingAirlines[0]:'',
                'departureDate' => Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time),'MMM d'),
                'departureTime' => Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time),'h:mm a'),
                'departureAirport' => $firstSegment->qs_departure_airport_code,
                'departureCity' => ($firstSegment->departureAirport)?$firstSegment->departureAirport->city:$firstSegment->qs_departure_airport_code,
                'arrivalTime' => Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->qs_arrival_time),'h:mm a'),
                'arrivalDatePlus' => round($datediff / (60 * 60 * 24)),
                'arrivalAirport' => $lastSegment->qs_arrival_airport_code,
                'arrivalCity' => ($lastSegment->arrivalAirport)?$lastSegment->arrivalAirport->city:$lastSegment->qs_arrival_airport_code,
                'duration' => SearchService::durationInMinutes($trip->qt_duration),
                'stops' => Yii::t('search', '{n, plural, =0{Nonstop} one{# stop} other{# stops}}', ['n' => $stopCnt]),
            ];
        }

        return [
            'price' => $this->getPricePerPax(),
            'trips' => $trips,
            'baggage' => $this->freeBaggageInfo,
        ];
    }





    public function getInfoForEmail2() : array
    {
        $trips = [];
        $quoteCabinClasses = [];
        $quoteMarketingAirlines = [];
        $quoteOperatingAirlines = [];

        $maxStopsQuantity = 0;

        foreach ($this->quoteTrips as $trip){

            $tripCabinClasses = [];
            $tripMarketingAirlines = [];
            $tripOperatingAirlines = [];

            $firstSegment = null;
            $lastSegment = null;
            $segments = $trip->quoteSegments;

            if( $segments ) {

                $segmentsCnt = count($segments);
                $stopCnt = $segmentsCnt - 1;
                $firstSegment = $segments[0];
                $lastSegment = end($segments);

                foreach ($segments as $segment) {

                    $cabinClass = SearchService::getCabin($segment->qs_cabin);

                    $quoteCabinClasses[$segment->qs_cabin] = $cabinClass;
                    $tripCabinClasses[$segment->qs_cabin] = $cabinClass;

                    $airlineCodeM = $airlineNameM = $segment->qs_marketing_airline;

                    if($segment->qs_marketing_airline) {
                        $airline = Airline::findIdentity($segment->qs_marketing_airline);
                        if($airline) {
                            $airlineNameM = $airline->name;
                        }
                    }

                    $tripMarketingAirlines[$airlineCodeM] = $airlineNameM; /*[
                        'airlineName' => $airlineName,
                        'airlineCode' => $airlineCode,
                    ];*/

                    $quoteMarketingAirlines[$airlineCodeM] = $airlineNameM;


                    $airlineCodeO = $airlineNameO = $segment->qs_operating_airline;

                    if($segment->qs_operating_airline) {
                        $airline = Airline::findIdentity($segment->qs_operating_airline);
                        if($airline) {
                            $airlineNameO = $airline->name;
                        }
                    }

                    $tripOperatingAirlines[$airlineCodeO] = $airlineNameO;
                    $quoteOperatingAirlines[$airlineCodeO] = $airlineNameO;

                    if($segment->qs_stop > 0){
                        $stopCnt += $segment->qs_stop;
                    }
                }

                if($stopCnt > $maxStopsQuantity) {
                    $maxStopsQuantity = $stopCnt;
                }

                $dateDiff = strtotime($lastSegment->qs_arrival_time) - strtotime($firstSegment->qs_departure_time);

                $trips[] = [
                    'cabinClasses' => $tripCabinClasses,
                    'marketingAirlines' => $tripMarketingAirlines,
                    'operatingAirlines' => $tripOperatingAirlines,


                    //'airlineCode' => isset($marketingAirlines[0]) ? $marketingAirlines[0]['airlineName'] : '',
                    //'airlineName' => isset($marketingAirlines[0]) ? $marketingAirlines[0]['airlineCode'] : '',

                    //'departureDate' => Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time),'MMM d'),
                    //'departureTime' => Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time),'h:mm a'),
                    'departDateTime' => date('Y-m-d H:i:s', strtotime($firstSegment->qs_departure_time)),
                    'departAirportIATA' => $firstSegment->qs_departure_airport_code,
                    //'departAirportName' => $firstSegment->qs_departure_airport_,
                    'departCity' => $firstSegment->departureAirport ? $firstSegment->departureAirport->city : $firstSegment->qs_departure_airport_code,
                    'arriveDateTime' => date('Y-m-d H:i:s', strtotime($lastSegment->qs_arrival_time)), //Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->qs_arrival_time),'h:mm a'),
                    'arriveDatePlus' => round($dateDiff / (60 * 60 * 24)),
                    'arriveAirportIATA' => $lastSegment->qs_arrival_airport_code,
                    'arriveCity' => $lastSegment->arrivalAirport ? $lastSegment->arrivalAirport->city : $lastSegment->qs_arrival_airport_code,
                    'flightDuration' => SearchService::durationInMinutes($trip->qt_duration),
                    'flightDurationMinutes' => $trip->qt_duration,
                    'stopsQuantity' => $stopCnt,
                    'baggage' => $this->getBaggageInfoByTrip($trip),
                ];

            }


        }



        return [
            'cabinClasses' => $quoteCabinClasses,
            'marketingAirlines' => $quoteMarketingAirlines,
            'operatingAirlines' => $quoteOperatingAirlines,
            'pricePerPax' => $this->getPricePerPax(),
            'priceTotal' => 0,
            'currencySymbol' => '$',
            'currencyCode' => 'USD',
            'trips' => $trips,
            'maxStopsQuantity' => $maxStopsQuantity
            //'baggage' => $this->getBaggageInfo2(),
        ];
    }

    public function getTripsFromDumpLikeSearch()
    {
        $trips = [];
        $tripIndex = 0;
        $segments = self::parseDump($this->reservation_dump, false);
        foreach ($segments as $key => $segment) {
            if(!isset($segment['cabin']) || empty($segment['cabin'])){
                $segment['cabin'] = $this->cabin;
            }
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
            $trips[$tripIndex]['tripId'] = $tripIndex+1;
            $trips[$tripIndex]['segments'][] = [
                'segmentId' => $key+1,
                'departureTime' => $segment['departureDateTime']->format('Y-m-d H:i'),
                'arrivalTime' => $segment['arrivalDateTime']->format('Y-m-d H:i'),
                'stop' => 0,
                'stops' => null,
                'flightNumber' => $segment['flightNumber'],
                'bookingClass' => $segment['bookingClass'],
                'duration' => $segment['flightDuration'],
                'departureAirportCode' => $segment['departureAirport'],
                'arrivalAirportCode' => $segment['arrivalAirport'],
                'marketingAirline' => $segment['carrier'],
                'cabin' => QuoteSegment::getCabinReal($segment['cabin']),
            ];
        }
        foreach ($trips as $key => $trip) {
            $firstSegment = $trip['segments'][0];
            $lastSegment = $trip['segments'][count($trip['segments']) - 1];

            $depCity = Airport::findIdentity($firstSegment['departureAirportCode']);
            $arrCity = Airport::findIdentity($lastSegment['arrivalAirportCode']);

            $arrDt = new \DateTime($lastSegment['arrivalTime']);
            $depDt = new \DateTime($firstSegment['departureTime']);
            if ($depCity !== null && $arrCity !== null && $depCity->dst != $arrCity->dst) {
                $flightDuration = ($arrDt->getTimestamp() - $depDt->getTimestamp()) / 60;
                $trips[$key]['duration'] = intval($flightDuration) + (intval($depCity->dst) * 60) - (intval($arrCity->dst) * 60);
            } else {
                $trips[$key]['duration'] = ($arrDt->getTimestamp() - $depDt->getTimestamp()) / 60;
            }

        }
        return $trips;
    }

    public function getTripsFromDump(&$title = null)
    {
        $trips = [];
        $tripIndex = 0;
        $segments = self::parseDump($this->reservation_dump, false);
        foreach ($segments as $key => $segment) {
            if(!isset($segment['cabin']) || empty($segment['cabin'])){
                $segment['cabin'] = $this->cabin;
            }
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
     * @param bool $label
     * @return string
     */
    public function getStatusName(bool $label = false): string
    {
        $statusName = self::STATUS_LIST[$this->status] ?? '-';

        if ($label) {
            $class = $this->getStatusLabelClass();
            $statusName = '<span class="label ' . $class . '" style="font-size: 13px">' . Html::encode($statusName) . '</span>';
        }

        return $statusName;
    }

    /**
     * @return string
     */
    public function getGdsName2(): string
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
            $price->roundValue();

            if (!isset($result['detail'][$price->passenger_type]['selling'])) {
                $result['detail'][$price->passenger_type]['selling'] = $price->selling;
                $result['detail'][$price->passenger_type]['fare'] = $price->fare;
                $result['detail'][$price->passenger_type]['taxes'] = $price->taxes + $price->mark_up + $price->extra_mark_up + $price->service_fee;
                $result['detail'][$price->passenger_type]['tickets'] = 1;
            } else {
                $result['detail'][$price->passenger_type]['selling'] += $price->selling;
                $result['detail'][$price->passenger_type]['fare'] += $price->fare;
                $result['detail'][$price->passenger_type]['taxes'] += $price->taxes + $price->mark_up + $price->extra_mark_up + $price->service_fee;
                $result['detail'][$price->passenger_type]['tickets'] += 1;
            }

            $result['selling'] += $price->selling;
            $result['fare'] += $price->fare;
            $result['mark_up'] += $price->mark_up + $price->extra_mark_up + $price->service_fee;
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
        $result['fare_type'] = empty($this->fare_type)
            ? self::FARE_TYPE_PUB : $this->fare_type;
        return $result;
    }

    public function getQuotePriceData()
    {
        $priceData = $this->getPricesData();
        $details = [];
        foreach ($priceData['prices'] as $paxCode => $price){
            $details[$paxCode]['selling'] = round($price['selling']/$price['tickets'], 2);
            $details[$paxCode]['fare'] = round($price['fare']/$price['tickets'], 2);
            $details[$paxCode]['taxes'] = round(($price['taxes'] + $price['mark_up'] + $price['extra_mark_up'] + $price['service_fee']) / $price['tickets'], 2);
            $details[$paxCode]['tickets'] = $price['tickets'];
        }
        $result = [
            'detail' => $details,
            'tickets' => count($this->quotePrices),
            'selling' => $priceData['total']['selling'],
            'amountPerPax' => $this->getPricePerPax(),
            'fare' => $priceData['total']['fare'],
            'mark_up' => $priceData['total']['mark_up'],
            'taxes' => $priceData['total']['taxes'],
            'currency' => 'USD',
            'isCC' => boolval(!$this->check_payment),
            'fare_type' => empty($this->fare_type) ? self::FARE_TYPE_PUB : $this->fare_type,
        ];
        return $result;
    }

    public function getQuotePricePassengersData()
    {
        $priceData = $this->getPricesData();
        $result = [
            'prices' => [
                'totalPrice' => round($priceData['total']['selling'],2),
                'totalTax' => 0,
                'isCk' => boolval($this->check_payment),
            ],
            'passengers' => [],
            'currency' => 'USD',
            'currencyRate' => 1,
            'fareType' => empty($this->fare_type) ? self::FARE_TYPE_PUB : $this->fare_type,
        ];

        $priceData = $this->getPricesData();
        foreach ($priceData['prices'] as $paxCode => $price){
            $result['passengers'][$paxCode]['cnt'] = $price['tickets'];
            $result['passengers'][$paxCode]['price'] = round($price['selling']/$price['tickets'], 2);
            $result['passengers'][$paxCode]['tax'] = round(($price['taxes'] + $price['mark_up'] + $price['extra_mark_up'] + $price['service_fee']) / $price['tickets'], 2);
            $result['passengers'][$paxCode]['baseFare'] = round($price['fare']/$price['tickets'], 2);
            $result['prices']['totalTax'] += $result['passengers'][$paxCode]['tax'] * $price['tickets'];
        }
        $result['prices']['totalTax'] = round($result['prices']['totalTax'], 2);


        return $result;
    }

    public function getServiceFeePercent()
    {
        return ($this->check_payment)?($this->service_fee_percent?$this->service_fee_percent:self::SERVICE_FEE*100):0;
    }

    public function getEstimationProfitText()
    {
        $priceData = $this->getPricesData();
        $data = [];
        /* if(isset($priceData['service_fee']) && $priceData['service_fee'] > 0){
            $data[] = '<span class="text-danger">Merchant fee: -'.round($priceData['service_fee'],2).'$</span>';
        } */
        if(isset($priceData['processing_fee']) && $priceData['processing_fee'] > 0){
            $data[] = '<span class="text-danger">Processing fee: -'.round($priceData['processing_fee'],2).'$</span>';
        }

        return (empty($data))?'-':implode('<br/>', $data);
    }

    public function getPricePerPax()
    {
        $priceData = $this->getPricesData();
        if(isset($priceData['prices'])){
            foreach ($priceData['prices'] as $paxCode => $priceEntry) {
                if($paxCode == QuotePrice::PASSENGER_ADULT){
                    return round($priceEntry['selling'] / $priceEntry['tickets'],2);
                }
            }
        }

        return 0;
    }

    public function getPricesData()
    {
        $prices = [];
        $service_fee_percent = $this->getServiceFeePercent();
        $defData = [
            'fare' => 0,
            'taxes' => 0,
            'net' => 0, // fare + taxes
            'tickets' => 0,
            'mark_up' => 0,
            'extra_mark_up' => 0,
            'service_fee' => 0,
            'selling' => 0, //net + mark_up + extra_mark_up + service_fee
        ];
        $total = $defData;

        $paxCode = null;
        foreach ($this->quotePrices as $price){
            if($paxCode !== $price->passenger_type){
                $prices[$price->passenger_type] = $defData;
                $paxCode = $price->passenger_type;
            }
            $prices[$price->passenger_type]['fare'] += $price->fare;
            $prices[$price->passenger_type]['taxes'] += $price->taxes;
            $prices[$price->passenger_type]['net'] = $prices[$price->passenger_type]['fare'] + $prices[$price->passenger_type]['taxes'];
            $prices[$price->passenger_type]['tickets'] += 1;
            $prices[$price->passenger_type]['mark_up'] += $price->mark_up;
            $prices[$price->passenger_type]['extra_mark_up'] += $price->extra_mark_up;
            $prices[$price->passenger_type]['selling'] = ($prices[$price->passenger_type]['net'] + $prices[$price->passenger_type]['mark_up'] + $prices[$price->passenger_type]['extra_mark_up']);
            if($service_fee_percent > 0){
                $prices[$price->passenger_type]['service_fee'] = $prices[$price->passenger_type]['selling'] * $service_fee_percent / 100;
                $prices[$price->passenger_type]['selling'] += $prices[$price->passenger_type]['service_fee'];
            }
            $prices[$price->passenger_type]['selling'] = round($prices[$price->passenger_type]['selling'], 2);
        }

        foreach ($prices as $key => $price){
            $total['tickets'] += $price['tickets'];
            $total['net'] += $price['net'];
            $total['mark_up'] += $price['mark_up'];
            $total['extra_mark_up'] += $price['extra_mark_up'];
            $total['selling'] += $price['selling'];

            $prices[$key]['selling'] = round($price['selling'], 2);
            $prices[$key]['net'] = round($price['net'], 2);
        }

        return [
                'prices' => $prices,
                'total' => $total,
                'service_fee_percent' => $service_fee_percent,
                'service_fee' => ($service_fee_percent > 0)?$total['selling'] * $service_fee_percent / 100:0,
                'processing_fee' => $this->getProcessingFee()
                ];
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
            'employee_name' => $this->employee_name
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
                    'extra_mark_up' => $quotePrice->extra_mark_up,
                    'service_fee' => $quotePrice->service_fee
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
                    'gid' => $this->lead->gid,
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

    public function getStatusLog()
    {
        return QuoteStatusLog::findAll(['quote_id' => $this->id]);
    }

    /**
     * @param string $ip
     * @return bool
     */
    public function isExcludedIP($ip)
    {
        $ipInGlobal = GlobalAcl::findOne(['mask' => $ip]);
        if($ipInGlobal !== null){
            return true;
        }

        $ipInEmployee = EmployeeAcl::findOne(['mask' => $ip]);
        if($ipInEmployee !== null){
            return true;
        }
        return false;
    }

    public function parsePriceDump($priceDump)
    {
        $explodeDump = explode("\n", $priceDump);
        $priceRows = [];
        $bagRows = [];
        $validatingCarrierRow = '';
        foreach ($explodeDump as $key => $row) {
            $row = trim($row);
            if (stripos($row, "«") !== false) {
                continue;
            }

            if ((stripos($row, "JCB") !== false ||
                stripos($row, "ADT") !== false ||
                stripos($row, "PFA") !== false ||
                stripos($row, "JNN") !== false ||
                stripos($row, "CNN") !== false ||
                stripos($row, "CBC") !== false ||
                stripos($row, "JNF") !== false ||
                stripos($row, "INF") !== false ||
                stripos($row, "CBI") !== false) &&
                stripos($row, "XT") !== false
                ) {
                    $priceRows[] = $row;
                }

                if (stripos($row, "VALIDATING CARRIER") !== false && empty($validatingCarrierRow)) {
                    $row = str_replace("VALIDATING CARRIER - ", "", $row);
                    $validating = explode(' ', $row);
                    $validatingCarrierRow = $validating[0];
                }

                if (stripos($row, "BAG ALLOWANCE") !== false) {
                    $bagRows[] = $this->getBagString($explodeDump, $key);
                }
        }

        $prices = [];
        foreach ($priceRows as $row) {
            if (stripos($row, "JCB") !== false ||
                stripos($row, "ADT") !== false ||
                stripos($row, "PFA") !== false
                ) {
                    if (empty($prices[self::PASSENGER_ADULT])) {
                        $prices[self::PASSENGER_ADULT] = $this->getPrice($row);
                    }
                } elseif (stripos($row, "JNN") !== false ||
                    stripos($row, "CNN") !== false ||
                    stripos($row, "CBC") !== false
                    ) {
                        if (empty($prices[self::PASSENGER_CHILD])) {
                            $prices[self::PASSENGER_CHILD] = $this->getPrice($row);
                        }
                } elseif (stripos($row, "JNF") !== false ||
                    stripos($row, "INF") !== false ||
                    stripos($row, "CBI") !== false
                    ) {
                        if (empty($prices[self::PASSENGER_INFANT])) {
                            $prices[self::PASSENGER_INFANT] = $this->getPrice($row);
                        }
                }
        }


        return [
            'validating_carrier' => $validatingCarrierRow,
            'prices' => $prices,
            'baggage' => $bagRows
        ];
    }

    private function getBagString($array, $index)
    {
        $bags = [];
        foreach ($array as $key => $val) {
            $val = trim($val);
            if ($key < $index) {
                continue;
            }
            if (stripos($val, "BAG ALLOWANCE") !== false && $key > $index){
                break;
            }
            $bags[] = $val;
            if (stripos($val, "**") !== false) {
                if (!isset($array[($key + 1)]) || stripos($array[($key + 1)], "2NDCHECKED") === false) {
                    break;
                }
            }
        }

        $bagsString = explode('2NDCHECKED', trim(implode(' ', $bags)));
        $bags = [
            'segment' => '',
            'free_baggage' => [],
            'paid_baggage' => []
        ];

        foreach ($bagsString as $key => $val) {
            $val = str_replace('*', '', $val);
            $detail = explode('-', $val);

            if (stripos($val, "BAG ALLOWANCE") !== false) {
                $bags['segment'] = $detail[1];
                if (stripos($val, "NIL/") !== false ||
                    stripos($val, "*/") !== false
                    ) {
                        if (stripos($val, "1STCHECKED") !== false) {
                            $bagsString = explode('1STCHECKED', $val);
                            $detailBag = explode('/', $bagsString[1]);
                            if (stripos($detailBag[0], "USD") !== false) {
                                $bagItem = [
                                    'ordinal' => '1st',
                                    'piece' => 1,
                                    'weight' => 'N/A',
                                    'height' => 'N/A',
                                    'price' => explode('-', $detailBag[0])[2],
                                ];
                                $detailVolume = explode('UP TO', $bagsString[1]);
                                if (isset($detailVolume[1])) {
                                    $bagItem['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                                }
                                if (isset($detailVolume[2])) {
                                    $bagItem['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                                }
                                $bags['paid_baggage'][] = $bagItem;
                            }
                        }
                    } else {

                        $detailBag = explode('/', $detail[2]);
                        $bags['free_baggage'] = [
                            'piece' => (int)str_replace('P', '', $detailBag[0]),
                            'weight' => 'N/A',
                            'height' => 'N/A',
                            'price' => 'USD0'
                        ];
                        $detailVolume = explode('UP TO', $detail[2]);
                        if (isset($detailVolume[1])) {
                            $bags['free_baggage']['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                        }
                        if (isset($detailVolume[2])) {
                            $bags['free_baggage']['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                        }
                    }
            } else {
                $detailBag = explode('/', $detail[2]);
                if (stripos($detailBag[0], "USD") !== false) {
                    $bagItem = [
                        'ordinal' => '2nd',
                        'piece' => 1,
                        'weight' => 'N/A',
                        'height' => 'N/A',
                        'price' => $detailBag[0],
                    ];

                    $detailVolume = explode('UP TO', $detail[2]);
                    if (isset($detailVolume[1])) {
                        $bagItem['weight'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[1])));
                    }
                    if (isset($detailVolume[2])) {
                        $bagItem['height'] = trim(sprintf('UP TO%s', str_replace('AND', '', $detailVolume[2])));
                    }
                    $bags['paid_baggage'][] = $bagItem;
                }

            }
        }
        return $bags;
    }

    private function getPrice($string)
    {
        $arr = [
            'fare' => 0,
            'taxes' => 0,
        ];
        $rows = explode(' ', $string);
        foreach ($rows as $row) {
            if (stripos($row, "XT") !== false) {
                $arr['taxes'] = (float)str_replace('XT', '', $row);
            }
        }
        $lastRow = end($rows);
        if (stripos($lastRow, "USD") !== false) {
            $lastRow = str_replace('USD', '', $lastRow);
            $arr['fare'] = (float)substr($lastRow, 0, -2) - $arr['taxes'];
        }
        return $arr;
    }
}
