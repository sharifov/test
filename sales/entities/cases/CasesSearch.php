<?php

namespace sales\entities\cases;

use common\models\Airport;
use common\models\Call;
use common\models\CaseSale;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\Sms;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use frontend\helpers\JsonHelper;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\helpers\setting\SettingHelper;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\CallLogType;
use sales\model\callLog\entity\callLogCase\CallLogCase;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatCase\entity\ClientChatCase;
use sales\model\saleTicket\entity\SaleTicket;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * Class CasesSearch
 *
 * @property $cssSaleId
 * @property $cssBookId
 * @property $salePNR
 * @property $clientPhone
 * @property $clientEmail
 * @property $ticketNumber
 * @property $airlineConfirmationNumber
 * @property $paxFirstName
 * @property $paxLastName
 * @property $cssChargedFrom
 * @property $cssChargedTo
 * @property $cssProfitFrom
 * @property $cssProfitTo
 * @property $cssOutDate
 * @property $cssInDate
 * @property $cssInOutDate
 * @property $cssChargeType
 * @property $departureAirport
 * @property $arrivalAirport
 * @property $departureCountries
 * @property $arrivalCountries
 * @property $css_profit
 * @property $saleTicketSendEmailDate
 * @property $sentEmailBy
 * @property $userGroup
 *
 * @property array $cacheSaleData
 * @property array $csStatuses
 * @property int|null $airlinePenalty
 * @property string|null $validatingCarrier
 *
 * @property int|null $emailsQtyFrom
 * @property int|null $emailsQtyTo
 * @property int|null $smsQtyFrom
 * @property int|null $smsQtyTo
 * @property int|null $callsQtyFrom
 * @property int|null $callsQtyTo
 * @property int|null $chatsQtyFrom
 * @property int|null $chatsQtyTo
 * @property int|null $caseUserGroup
 */
class CasesSearch extends Cases
{
    public $cssSaleId;
    public $cssBookId;
    public $salePNR;
    public $clientPhone;
    public $clientEmail;
    public $ticketNumber;
    public $airlineConfirmationNumber;
    public $paxFirstName;
    public $paxLastName;

    public $cssChargedFrom;
    public $cssChargedTo;
    public $cssProfitFrom;
    public $cssProfitTo;
    public $cssOutDate;
    public $cssInDate;
    public $cssInOutDate;
    public $cssChargeType;
    public $departureAirport;
    public $arrivalAirport;
    public $departureCountries;
    public $arrivalCountries;
    public $clientId;
    public $saleTicketSendEmailDate;

    public $sentEmailBy;
    public $userGroup;
    public $csStatuses;
    public $airlinePenalty;
    public $validatingCarrier;

    public $emailsQtyFrom;
    public $emailsQtyTo;
    public $smsQtyFrom;
    public $smsQtyTo;
    public $callsQtyFrom;
    public $callsQtyTo;
    public $chatsQtyFrom;
    public $chatsQtyTo;
    public $caseUserGroup;

    private $cacheSaleData = [];

    public int $cacheDuration = 60 * 1;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['cs_gid', 'string'],
            ['cs_id', 'integer'],
            ['cs_subject', 'string'],
            ['cs_category_id', 'integer'],
            ['cs_status', 'integer'],
            ['csStatuses', 'in', 'range' => array_keys(CasesStatus::STATUS_LIST), 'allowArray' => true],
            ['cs_user_id', 'integer'],
            ['cs_lead_id', 'string'],
            ['cs_dep_id', 'integer'],
            ['cs_created_dt', 'string'],
            [['sentEmailBy', 'userGroup'], 'string'],
            [['cs_client_id', 'clientId'], 'integer'],
            ['cs_project_id', 'integer'],
            ['cs_source_type_id', 'integer'],
            ['cs_last_action_dt', 'safe'],
            ['cssSaleId', 'integer'],
            ['cssBookId', 'string'],
            ['salePNR', 'string'],
            ['clientPhone', 'string'],
            ['clientEmail', 'string'],
            ['ticketNumber', 'string', 'min' => 4],
            ['airlineConfirmationNumber', 'string', 'min' => 4],
            ['paxFirstName', 'string', 'min' => 2],
            ['paxLastName', 'string', 'min' => 2],
            ['cs_need_action', 'boolean'],

            ['cs_order_uid', 'string'],

            [['cssChargedFrom', 'cssChargedTo', 'cssProfitFrom', 'cssProfitTo'], 'number'],
            [['cssOutDate', 'cssInDate'], 'date'],
            [['cssChargeType'], 'string', 'max' => 100],
            [['departureAirport', 'arrivalAirport', 'departureCountries', 'arrivalCountries', 'cssInOutDate', 'saleTicketSendEmailDate'], 'safe'],

            [['airlinePenalty', 'caseUserGroup'], 'integer'],
            ['validatingCarrier', 'string', 'length' => 2],
            [
                [
                    'emailsQtyFrom', 'emailsQtyTo', 'smsQtyFrom', 'smsQtyTo',
                    'callsQtyFrom', 'callsQtyTo', 'chatsQtyFrom', 'chatsQtyTo',
                ],
                'integer', 'min' => 0, 'max' => 1000
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cs_id' => 'ID',
            'cs_gid' => 'GID',
            'cs_project_id' => 'Project',
            'cs_subject' => 'Subject',
            'cs_category_id' => 'Category',
            'cs_status' => 'Status',
            'cs_user_id' => 'User',
            'cs_lead_id' => 'Lead ID',
            'cs_dep_id' => 'Department',
            'cs_created_dt' => 'Created',
            'cssSaleId' => 'Sale ID',
            'cssBookId' => 'Booking ID',
            'salePNR' => 'Quote PNR',
            'cs_source_type_id' => 'Source type',
            'cs_need_action' => 'Need action',
            'cssChargedFrom' => 'Charged from',
            'cssChargedTo' => 'Charged to',
            'cssProfitFrom' => 'Profit from',
            'cssProfitTo' => 'Profit to',
            'cssOutDate' => 'Out Date',
            'cssInDate' => 'In Date',
            'cssChargeType' => 'Charge Type',
            'departureAirport' => 'Departure Airport',
            'arrivalAirport' => 'Arrival Airport',
            'departureCountries' => 'Depart. Countries',
            'arrivalCountries' => 'Arrival Countries',
            'clientId' => 'Client ID',
			'saleTicketSendEmailDate' => 'Send Email Date',
			'sentEmailBy' => 'Sent Email By User',
			'userGroup' => 'User Group',
			'csStatuses' => 'Status',
			'airlinePenalty' => 'Airline Penalty',
			'cs_order_uid' => 'Order uid',
			'validatingCarrier' => 'Validating Carrier',
			'emailsQtyFrom' => 'Emails From', 'emailsQtyTo' => 'Emails To',
			'smsQtyFrom' => 'Sms From', 'smsQtyTo' => 'Sms To',
			'callsQtyFrom' => 'Calls From', 'callsQtyTo' => 'Calls To',
			'chatsQtyFrom' => 'Chats From', 'chatsQtyTo' => 'Chats To',
            'caseUserGroup' => 'Case User Group',
        ];
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function search($params, $user): ActiveDataProvider
    {
        if ($user->isAdmin()) {
            return $this->searchByAdmin($params);
        }
        return $this->searchByAgent($params, $user);
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     * @throws \JsonException
     */
    public function searchByAgent($params, $user): ActiveDataProvider
    {
        $query = self::find()->with(['project', 'department', 'category']);

//        $query->andWhere(['cs_dep_id' => array_keys(EmployeeDepartmentAccess::getDepartments())]);
        $query->andWhere(['cs_project_id' => array_keys(EmployeeProjectAccess::getProjects())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cs_id' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        unset($dataProvider->sort->attributes['cs_lead_id']);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cs_id' => $this->cs_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_dep_id' => $this->cs_dep_id,
            'cs_status' => $this->cs_status,
            'cs_source_type_id' => $this->cs_source_type_id,
            'cs_need_action' => $this->cs_need_action,
            'cs_client_id' => $this->cs_client_id,
        ]);

        $query->andFilterWhere(['IN', 'cs_status', $this->csStatuses]);
        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);

        if ($user->isExSuper() || $user->isSupSuper()) {
            if ($this->cs_user_id) {
                $query->andWhere(['cs_user_id' => Employee::find()->select(Employee::tableName() . '.id')->andWhere([Employee::tableName() . '.id' => $this->cs_user_id])]);
            }
        }
        if ($this->cssSaleId) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $this->cssSaleId])]);
        }
        if ($this->clientId){
            $query->andWhere(['cs_client_id' => $this->clientId]);
        }

        if ($this->validatingCarrier) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->andWhere(['=',
                        new Expression("JSON_EXTRACT(css_sale_data,'$.validatingCarrier')"),
                        $this->validatingCarrier
                ])
            ]);
        }
        if ($this->ticketNumber) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(
                    new Expression("JSON_CONTAINS(css_sale_data->'$.passengers[*].ticket_number', JSON_ARRAY(:ticket_number))"),
                    [':ticket_number' => $this->ticketNumber]
                )
            ]);
        }
        if ($this->paxFirstName) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(
                    new Expression("JSON_CONTAINS(css_sale_data->'$.passengers[*].first_name', JSON_ARRAY(:first_name))"),
                    [':first_name' => $this->paxFirstName]
                )
            ]);
        }
        if ($this->paxLastName) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(
                    new Expression("JSON_CONTAINS(css_sale_data->'$.passengers[*].last_name', JSON_ARRAY(:last_name))"),
                    [':last_name' => $this->paxLastName]
                )
            ]);
        }
        if ($this->airlineConfirmationNumber) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(
                    new Expression("JSON_CONTAINS(css_sale_data->'$.itinerary[*].segments[*].airlineRecordLocator', JSON_ARRAY(:airlineRecordLocator))"),
                    [':airlineRecordLocator' => $this->airlineConfirmationNumber]
                )
            ]);
        }
        if ($this->cssBookId) {
            $query->andWhere(['OR',
                ['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_book_id' => $this->cssBookId])],
                ['cs_order_uid' => $this->cssBookId],
            ]);
        }
        if ($this->salePNR) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_pnr' => $this->salePNR])]);
        }
        if ($this->clientPhone) {
            $query->andWhere(['cs_client_id' => ClientPhone::find()->select('client_id')->andWhere(['phone' => $this->clientPhone])]);
        }
        if ($this->clientEmail) {
            $query->andWhere(['cs_client_id' => ClientEmail::find()->select('client_id')->andWhere(['email' => $this->clientEmail])]);
        }
        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }
        if ($this->cssChargedFrom) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['>=', 'css_charged', $this->cssChargedFrom])]);
        }
        if ($this->cssChargedTo) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['<=', 'css_charged', $this->cssChargedTo])]);
        }
        if ($this->cssProfitFrom) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['>=', 'css_profit', $this->cssProfitFrom])]);
        }
        if ($this->cssProfitTo) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['<=', 'css_profit', $this->cssProfitTo])]);
        }
        if ($this->cssInOutDate) {
			$departRange = explode(' - ', $this->cssInOutDate);
			if ($departRange[0] && $departRange[1]) {
				$fromDate = date('Y-m-d', strtotime($departRange[0]));
				$toDate = date('Y-m-d', strtotime($departRange[1]));
				$query->andWhere(['cs_id' => CaseSale::find()
													->select('css_cs_id')
													->where(['between', 'DATE(css_out_date)', $fromDate, $toDate])
													->orWhere(['between', 'DATE(css_in_date)', $fromDate, $toDate])
				]);
			}
		}
        if ($this->cssChargeType) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_charge_type' => $this->cssChargeType])]);
        }
        if ($this->departureAirport) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(['IN', 'css_out_departure_airport', $this->departureAirport])
                ->orWhere(['IN', 'css_in_departure_airport', $this->departureAirport])
            ]);
        }
        if ($this->arrivalAirport) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(['IN', 'css_out_arrival_airport', $this->arrivalAirport])
                ->orWhere(['IN', 'css_in_arrival_airport', $this->arrivalAirport])
            ]);
        }
        if ($this->departureCountries) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('case_sale.css_cs_id')
                ->innerJoin(Airport::tableName() . 'AS airports',
                    'case_sale.css_out_departure_airport = airports.iata OR case_sale.css_in_departure_airport = airports.iata')
                ->where(['IN', 'airports.country', $this->departureCountries])
            ]);
        }
        if ($this->arrivalCountries) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('case_sale.css_cs_id')
                ->innerJoin(Airport::tableName() . 'AS airports',
                    'case_sale.css_out_departure_airport = airports.iata OR case_sale.css_in_departure_airport = airports.iata')
                ->where(['IN', 'airports.country', $this->arrivalCountries])
            ]);
        }

		if ($this->saleTicketSendEmailDate) {
			$emails = SettingHelper::getCaseSaleTicketMainEmailList();
			$this->saleTicketSendEmailDate = date('Y-m-d', strtotime($this->saleTicketSendEmailDate));
			$query->innerJoin(Email::tableName(), new Expression('cs_id = e_case_id'))
				->andWhere(['e_email_to' => $emails, 'date_format(e_created_dt, "%Y-%m-%d")' => $this->saleTicketSendEmailDate])
				->groupBy('cs_id');

			if ($params['export_type']) {
				$query->addSelect(['cases.*', 'css_sale_id as cssSaleId', 'css_sale_book_id as cssBookId', 'css_sale_pnr as salePNR', 'css_send_email_dt as saleTicketSendEmailDate', 'username as sentEmailBy', 'ug_name as userGroup'])
					->innerJoin(CaseSale::tableName(), new Expression('css_cs_id = cs_id and css_send_email_dt is not null'))
					->leftJoin(Employee::tableName(), new Expression('id = e_created_user_id'))
					->leftJoin(UserGroupAssign::tableName(), new Expression('ugs_user_id = e_created_user_id'))
					->leftJoin(UserGroup::tableName(), new Expression('ug_id = ugs_group_id'))
					->andWhere(['date_format(css_send_email_dt, "%Y-%m-%d")' => $this->saleTicketSendEmailDate]);
			}
		}
		if ($this->airlinePenalty) {
            $query->andWhere([
                    'cs_id' => SaleTicket::find()->select('st_case_id')
                        ->andWhere(['st_penalty_type' => $this->airlinePenalty])
                ]
            );
        }

        //$query = $this->prepareCommunicationQuery($query);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \JsonException
     */
    private function searchByAdmin($params): ActiveDataProvider
    {
        $query = self::find()->with(['project', 'department', 'category']);

//        $query->andWhere(['cs_dep_id' => array_keys(EmployeeDepartmentAccess::getDepartments())]);
        $query->andWhere(['cs_project_id' => array_keys(EmployeeProjectAccess::getProjects())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cs_id' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        unset($dataProvider->sort->attributes['cs_lead_id']);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cs_id' => $this->cs_id,
            'cs_gid' => $this->cs_gid,
            'cs_project_id' => $this->cs_project_id,
            'cs_dep_id' => $this->cs_dep_id,
            'cs_category_id' => $this->cs_category_id,
            'cs_client_id' => $this->cs_client_id,
            'cs_source_type_id' => $this->cs_source_type_id,
            'cs_need_action' => $this->cs_need_action,
            'cs_status' => $this->cs_status,
        ]);

        $query->andFilterWhere(['IN', 'cs_status', $this->csStatuses]);
        $query->andFilterWhere(['like', 'cs_subject', $this->cs_subject]);
        $query->andFilterWhere(['like', 'cs_order_uid', $this->cs_order_uid]);

        if ($this->cs_user_id) {
            $query->andWhere(['cs_user_id' => Employee::find()->select(Employee::tableName() . '.id')->andWhere([Employee::tableName() . '.id' => $this->cs_user_id])]);
        }
        if ($this->cs_lead_id) {
            $query->andWhere(['cs_lead_id' => Lead::find()->select(Lead::tableName() . '.id')->andWhere([Lead::tableName() . '.id' => $this->cs_lead_id])]);
        }

        if ($this->cssSaleId) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_id' => $this->cssSaleId])]);
        }
        if ($this->clientId){
            $query->andWhere(['cs_client_id' => $this->clientId]);
        }

        if ($this->validatingCarrier) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->andWhere(['=',
                        new Expression("JSON_EXTRACT(css_sale_data,'$.validatingCarrier')"),
                        $this->validatingCarrier
                ])
            ]);
        }
        if ($this->ticketNumber) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(
                    new Expression("JSON_CONTAINS(css_sale_data->'$.passengers[*].ticket_number', JSON_ARRAY(:ticket_number))"),
                    [':ticket_number' => $this->ticketNumber]
                )
            ]);
        }
        if ($this->paxFirstName) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(
                    new Expression("JSON_CONTAINS(css_sale_data->'$.passengers[*].first_name', JSON_ARRAY(:first_name))"),
                    [':first_name' => $this->paxFirstName]
                )
            ]);
        }
        if ($this->paxLastName) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(
                    new Expression("JSON_CONTAINS(css_sale_data->'$.passengers[*].last_name', JSON_ARRAY(:last_name))"),
                    [':last_name' => $this->paxLastName]
                )
            ]);
        }
        if ($this->airlineConfirmationNumber) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(
                    new Expression("JSON_CONTAINS(css_sale_data->'$.itinerary[*].segments[*].airlineRecordLocator', JSON_ARRAY(:airlineRecordLocator))"),
                    [':airlineRecordLocator' => $this->airlineConfirmationNumber]
                )
            ]);
        }
        if ($this->cssBookId) {
            $query->andWhere(['OR',
                ['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_book_id' => $this->cssBookId])],
                ['cs_order_uid' => $this->cssBookId],
            ]);
        }
        if ($this->salePNR) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_sale_pnr' => $this->salePNR])]);
        }
        if ($this->clientPhone) {
            $query->andWhere(['cs_client_id' => ClientPhone::find()->select('client_id')->andWhere(['phone' => $this->clientPhone])]);
        }
        if ($this->clientEmail) {
            $query->andWhere(['cs_client_id' => ClientEmail::find()->select('client_id')->andWhere(['email' => $this->clientEmail])]);
        }
        if ($this->cs_created_dt) {
            $query->andFilterWhere(['DATE(cs_created_dt)' => date('Y-m-d', strtotime($this->cs_created_dt))]);
        }
        if ($this->cs_last_action_dt) {
            $query->andFilterWhere(['DATE(cs_last_action_dt)' => date('Y-m-d', strtotime($this->cs_last_action_dt))]);
        }
        if ($this->cssChargedFrom) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['>=', 'css_charged', $this->cssChargedFrom])]);
        }
        if ($this->cssChargedTo) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['<=', 'css_charged', $this->cssChargedTo])]);
        }
        if ($this->cssProfitFrom) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['>=', 'css_profit', $this->cssProfitFrom])]);
        }
        if ($this->cssProfitTo) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['<=', 'css_profit', $this->cssProfitTo])]);
        }
		if ($this->cssInOutDate) {
			$departRange = explode(' - ', $this->cssInOutDate);
			if ($departRange[0] && $departRange[1]) {
				$fromDate = date('Y-m-d', strtotime($departRange[0]));
				$toDate = date('Y-m-d', strtotime($departRange[1]));
				$query->andWhere(['cs_id' => CaseSale::find()
													->select('css_cs_id')
													->where(['between', 'DATE(css_out_date)', $fromDate, $toDate])
													->orWhere(['between', 'DATE(css_in_date)', $fromDate, $toDate])
				]);
			}
		}

		if ($this->saleTicketSendEmailDate) {
			$emails = SettingHelper::getCaseSaleTicketMainEmailList();
			$this->saleTicketSendEmailDate = date('Y-m-d', strtotime($this->saleTicketSendEmailDate));
			$query->innerJoin(Email::tableName(), new Expression('cs_id = e_case_id'))
				->andWhere(['e_email_to' => $emails, 'date_format(e_created_dt, "%Y-%m-%d")' => $this->saleTicketSendEmailDate])
				->groupBy('cs_id');

			if ($params['export_type']) {
				$query->addSelect(['cases.*', 'css_sale_id as cssSaleId', 'css_sale_book_id as cssBookId', 'css_sale_pnr as salePNR', 'css_send_email_dt as saleTicketSendEmailDate', 'username as sentEmailBy', 'ug_name as userGroup'])
					->innerJoin(CaseSale::tableName(), new Expression('css_cs_id = cs_id and css_send_email_dt is not null'))
					->leftJoin(Employee::tableName(), new Expression('id = e_created_user_id'))
					->leftJoin(UserGroupAssign::tableName(), new Expression('ugs_user_id = e_created_user_id'))
					->leftJoin(UserGroup::tableName(), new Expression('ug_id = ugs_group_id'))
					->andWhere(['date_format(css_send_email_dt, "%Y-%m-%d")' => $this->saleTicketSendEmailDate]);
			}
		}

        if ($this->cssChargeType) {
            $query->andWhere(['cs_id' => CaseSale::find()->select('css_cs_id')->andWhere(['css_charge_type' => $this->cssChargeType])]);
        }
        if ($this->departureAirport) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(['IN', 'css_out_departure_airport', $this->departureAirport])
                ->orWhere(['IN', 'css_in_departure_airport', $this->departureAirport])
            ]);
        }
        if ($this->arrivalAirport) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('css_cs_id')
                ->where(['IN', 'css_out_arrival_airport', $this->arrivalAirport])
                ->orWhere(['IN', 'css_in_arrival_airport', $this->arrivalAirport])
            ]);
        }
        if ($this->departureCountries) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('case_sale.css_cs_id')
                ->innerJoin(Airport::tableName() . ' AS airports',
                    'case_sale.css_out_departure_airport = airports.iata OR case_sale.css_in_departure_airport = airports.iata')
                ->where(['IN', 'airports.country', $this->departureCountries])
            ]);
        }
        if ($this->arrivalCountries) {
            $query->andWhere(['cs_id' =>
                CaseSale::find()->select('case_sale.css_cs_id')
                ->innerJoin(Airport::tableName() . ' AS airports',
                    'case_sale.css_out_departure_airport = airports.iata OR case_sale.css_in_departure_airport = airports.iata')
                ->where(['IN', 'airports.country', $this->arrivalCountries])
            ]);
        }
        if ($this->airlinePenalty) {
            $query->andWhere([
                    'cs_id' => SaleTicket::find()->select('st_case_id')
                        ->andWhere(['st_penalty_type' => $this->airlinePenalty])
                ]
            );
        }
        if ($this->caseUserGroup) {
            $query->andWhere([
                    'cs_user_id' => Employee::find()->select('id')
                        ->innerJoin(UserGroupAssign::tableName() . ' AS user_group_assign',
                            new Expression('user_group_assign.ugs_user_id = employees.id'))
                        ->andWhere(['user_group_assign.ugs_group_id' => $this->caseUserGroup])
                        ->groupBy('employees.id')
                ]
            );
        }

        //$query = $this->prepareCommunicationQuery($query);

        return $dataProvider;
    }

    /**
     * @param $query
     * @return mixed
     */
    private function prepareCommunicationQuery($query)
    {
        if ($this->emailsQtyFrom !== '' || $this->emailsQtyTo !== '') {
            $query->leftJoin([
                'emails' => Email::find()
                    ->select([
                        'e_case_id',
                        new Expression('COUNT(e_case_id) AS cnt')
                    ])
                    ->groupBy(['e_case_id'])
            ], 'cases.cs_id = emails.e_case_id');

            if ('' !== $this->emailsQtyFrom) {
                if ((int) $this->emailsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['>=', 'emails.cnt', $this->emailsQtyFrom],
                            ['IS', 'emails.e_case_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['>=', 'emails.cnt', $this->emailsQtyFrom]);
                }
            }
            if ('' !== $this->emailsQtyTo) {
                if ((int) $this->emailsQtyTo === 0 || (int) $this->emailsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['<=', 'emails.cnt', $this->emailsQtyTo],
                            ['IS', 'emails.e_case_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['<=', 'emails.cnt', $this->emailsQtyTo]);
                }
            }
        }

        if ($this->smsQtyFrom !== '' || $this->smsQtyTo !== '') {
            $query->leftJoin([
                'sms' => Sms::find()
                    ->select([
                        's_case_id',
                        new Expression('COUNT(s_case_id) AS cnt')
                    ])
                    ->groupBy(['s_case_id'])
            ], 'cases.cs_id = sms.s_case_id');

            if ('' !== $this->smsQtyFrom) {
                if ((int) $this->smsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['>=', 'sms.cnt', $this->smsQtyFrom],
                            ['IS', 'sms.s_case_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['>=', 'sms.cnt', $this->smsQtyFrom]);
                }
            }
            if ('' !== $this->smsQtyTo) {
                if ((int) $this->smsQtyTo === 0 || (int) $this->smsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['<=', 'sms.cnt', $this->smsQtyTo],
                            ['IS', 'sms.s_case_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['<=', 'sms.cnt', $this->smsQtyTo]);
                }
            }
        }

        if ($this->callsQtyFrom !== '' || $this->callsQtyTo !== '') {

            if ((bool) Yii::$app->params['settings']['new_communication_block_lead']) {
                $query->leftJoin([
                    'calls' => CallLogCase::find()
                        ->select([
                            'clc_case_id AS c_case_id',
                            new Expression('COUNT(clc_case_id) AS cnt')
                        ])
                        ->innerJoin(CallLog::tableName(), 'call_log.cl_id = call_log_case.clc_cl_id')
                        ->where(['IN', 'cl_type_id', [CallLogType::IN, CallLogType::OUT]])
                        ->groupBy(['clc_case_id'])
                ], 'cases.cs_id = calls.c_case_id');

            } else {
                $query->leftJoin([
                    'calls' => Call::find()
                        ->select([
                            'c_case_id',
                            new Expression('COUNT(c_case_id) AS cnt')
                        ])
                        ->where(['c_parent_id' => null])
                        ->andWhere(['IN', 'c_call_type_id', [Call::CALL_TYPE_IN, Call::CALL_TYPE_OUT]])
                        ->groupBy(['c_case_id'])
                ], 'cases.cs_id = calls.c_case_id');
            }

            if ('' !== $this->callsQtyFrom) {
                if ((int) $this->callsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['>=', 'calls.cnt', $this->callsQtyFrom],
                            ['IS', 'calls.c_case_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['>=', 'calls.cnt', $this->callsQtyFrom]);
                }
            }
            if ('' !== $this->callsQtyTo) {
                if ((int) $this->callsQtyTo === 0 || (int) $this->callsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['<=', 'calls.cnt', $this->callsQtyTo],
                            ['IS', 'calls.c_case_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['<=', 'calls.cnt', $this->callsQtyTo]);
                }
            }
        }

        if ($this->chatsQtyFrom !== '' || $this->chatsQtyTo !== '') {
            $query->leftJoin([
                'chats' => ClientChatCase::find()
                    ->select([
                        'cccs_case_id',
                        new Expression('COUNT(cccs_case_id) AS cnt')
                    ])
                    ->groupBy(['cccs_case_id'])
            ], 'cases.cs_id = chats.cccs_case_id');

            if ('' !== $this->chatsQtyFrom) {
                if ((int) $this->chatsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['>=', 'chats.cnt', $this->chatsQtyFrom],
                            ['IS', 'chats.cccs_case_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['>=', 'chats.cnt', $this->chatsQtyFrom]);
                }
            }
            if ('' !== $this->chatsQtyTo) {
                if ((int) $this->chatsQtyTo === 0 || (int) $this->chatsQtyFrom === 0) {
                    $query->andWhere(
                        [
                            'OR',
                            ['<=', 'chats.cnt', $this->chatsQtyTo],
                            ['IS', 'chats.cccs_case_id', null]
                        ]
                    );
                } else {
                    $query->andWhere(['<=', 'chats.cnt', $this->chatsQtyTo]);
                }
            }
        }

        return $query;
    }

    /**
     * @return array
     */
    private function getCaseSaleData($search): array
    {
//        if ($this->cacheSaleData) {
//            return $this->cacheSaleData;
//        }
        $this->cacheSaleData = CaseSale::find()
            ->select(['css_sale_data'])
            ->andWhere(['LIKE', 'css_sale_data', $search])
            ->all();
        return $this->cacheSaleData;
    }

    /**
     * @param $tickerNum
     * @return int|null
     */
    private function getSaleIdByTicket($tickerNum): ?int
    {
        foreach ($this->getCaseSaleData($tickerNum) as $sale) {
            $decodeSale = JsonHelper::decode($sale['css_sale_data'], false);
            foreach ($decodeSale->passengers as $passenger) {
                if (strcasecmp($passenger->ticket_number, $tickerNum) === 0) {
                    return $decodeSale->saleId;
                }
            }
        }
        return null;
    }


    /**
     * @param string $validatingCarrier
     * @return int|null
     * @throws \JsonException
     */
    private function getSaleIdByValidatingCarrier(string $validatingCarrier): ?int
    {
        $validatingCarrierParam = 'validatingCarrier\":\"' . $validatingCarrier;

        foreach ($this->getCaseSaleData($validatingCarrierParam) as $sale) {
            $decodeSale = JsonHelper::decode($sale['css_sale_data'], false);
            if (
                isset($decodeSale->validatingCarrier) &&
                strcasecmp($decodeSale->validatingCarrier, $validatingCarrier) === 0
            ) {
                return $decodeSale->saleId;
            }
        }
        return null;
    }

    /**
     * @param $firstName
     * @return int|null
     */
    private function getSaleIdByPaxFirstName($firstName): ?int
    {
        foreach ($this->getCaseSaleData($firstName) as $sale) {
            $decodeSale = JsonHelper::decode($sale['css_sale_data'], false);
            foreach ($decodeSale->passengers as $passenger) {
                if (strcasecmp($passenger->first_name, $firstName) === 0) {
                    return $decodeSale->saleId;
                }
            }
        }
        return null;
    }

    /**
     * @param $lastName
     * @return int|null
     */
    private function getSaleIdByPaxLastName($lastName): ?int
    {
        foreach ($this->getCaseSaleData($lastName) as $sale) {
            $decodeSale = JsonHelper::decode($sale['css_sale_data'], false);
            foreach ($decodeSale->passengers as $passenger) {
                if (strcasecmp($passenger->last_name, $lastName) === 0) {
                    return $decodeSale->saleId;
                }
            }
        }
        return null;
    }

    /**
     * @param $acn
     * @return int|null
     */
    private function getSaleIdByAcn($acn): ?int
    {
        foreach ($this->getCaseSaleData($acn) as $sale) {
            $decodeSale = JsonHelper::decode($sale['css_sale_data'], false);
            foreach ($decodeSale->itinerary as $itinerary) {
                foreach ($itinerary->segments as $segment) {
                    if (strcasecmp($segment->airlineRecordLocator, $acn) === 0) {
                        return $decodeSale->saleId;
                    }
                }
            }
        }
        return null;
    }
}
