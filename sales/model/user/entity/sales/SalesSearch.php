<?php

namespace sales\model\user\entity\sales;

use common\models\Call;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadFlow;
use common\models\query\LeadQuery;
use common\models\Sms;
use sales\model\callLog\entity\callLog\CallLog;
use sales\model\callLog\entity\callLog\CallLogStatus;
use sales\model\callLog\entity\callLog\CallLogType;
use sales\model\callLog\entity\callLogLead\CallLogLead;
use sales\model\callLog\entity\callLogRecord\CallLogRecord;
use sales\model\clientChatLead\entity\ClientChatLead;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class SalesSearch
 *
 * @property string|null $startDt
 * @property string|null $endDt
 * @property int|null $id
 * @property float|null $final_profit
 * @property string|null $l_status_dt
 * @property string|null $created
 * @property string|null $dateRange
 * @property string|null $defaultDateRange
 * @property string|null $dateFrom
 * @property string|null $dateTo
 * @property string $minDate
 * @property string $maxDate
 * @property string $defaultMinDate
 *
 * @property Employee $currentUser
 */
class SalesSearch extends Model
{
    public $startDt;
    public $endDt;
    public $dateRange;
    public $defaultDateRange;

    public $dateFrom;
    public $dateTo;

    public $id;
    public $final_profit;
    public $l_status_dt;
    public $created;

    private $currentUser;
    private $minDate;
    private $maxDate;
    private $defaultMinDate;

    /**
     * @param Employee $currentUser
     * @param array $config
     */
    public function __construct(Employee $currentUser, $config = [])
    {
        $this->currentUser = $currentUser;
        $this->minDate = date('Y-m-d', strtotime('first day of previous month'));
        $this->maxDate = date("Y-m-d");

        $this->defaultMinDate = date("Y-m-01");
        $this->defaultDateRange = $this->defaultMinDate . ' - ' . $this->maxDate;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['final_profit', 'number'],
            [['l_status_dt', 'created'], 'date', 'format' => 'php:Y-m-d'],

            [['dateRange'], 'safe'],
            [['dateRange'], 'default', 'value' => $this->defaultDateRange],

            [['dateFrom', 'dateTo'], 'date', 'format' => 'php:Y-m-d'],
            ['dateFrom', 'compare', 'compareAttribute' => 'dateTo', 'operator' => '<='],
            //['dateFrom', 'compare', 'compareValue' => $this->minDate, 'operator' => '>=', 'type' => 'date'],

            ['dateFrom', 'default', 'value' => $this->defaultMinDate],
            ['dateTo', 'default', 'value' => $this->maxDate],
        ];
    }

    public function attributeLabels(): array
    {
        return [];
    }

    public function searchByUser(array $params, int $cacheDuration = -1)
    {
        $query = (new Query());
        $query->from(Lead::tableName());
        $query->select(Lead::tableName() . '.*');
        $query->addSelect(['gross_profit' => new Expression('ROUND(final_profit - agents_processing_fee, 2)')]);

        $query->where(['employee_id' => $this->currentUser->getId()]);
        $query->andWhere(['status' => Lead::STATUS_SOLD]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['l_status_dt' => SORT_DESC],
                'attributes' => [
                    'l_status_dt' => [
                        'asc' => [Lead::tableName() . '.l_status_dt' => SORT_ASC],
                        'desc' => [Lead::tableName() . '.l_status_dt' => SORT_DESC],
                        'label' => 'Sold DT',
                    ],
                    'id' => [
                        'asc' => [Lead::tableName() . '.id' => SORT_ASC],
                        'desc' => [Lead::tableName() . '.id' => SORT_DESC],
                        'label' => 'Lead',
                    ],
                    'final_profit' => [
                        'asc' => ['gross_profit' => SORT_ASC],
                        'desc' => ['gross_profit' => SORT_DESC],
                        'label' => 'Lead',
                    ],
                    'created',
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            Lead::tableName() . '.id' => $this->id,
            'DATE(' . Lead::tableName() . '.l_status_dt)' => $this->l_status_dt,
            'DATE(' . Lead::tableName() . '.created)' => $this->created,
        ]);

        if ($this->dateFrom && $this->dateTo) {
            $query->andWhere(['>=', 'DATE(' . Lead::tableName() . '.l_status_dt)', $this->dateFrom]);
            $query->andWhere(['<=', 'DATE(' . Lead::tableName() . '.l_status_dt)', $this->dateTo]);
        }

        if ($this->final_profit) {
            $query->andHaving(['=', 'gross_profit', $this->final_profit]);
        }

        $query->cache($cacheDuration);

        return $dataProvider;
    }

    public function qualifiedLeadsTakenQuery(array $params, int $cacheDuration = -1): LeadQuery
    {
        $query = Lead::find();
        $query->select(Lead::tableName() . '.*');

        $query->where([Lead::tableName() . '.employee_id' => $this->currentUser->getId()]);
        $query->andWhere(['!=', Lead::tableName() . '.status', Lead::STATUS_TRASH]);
        $query->andWhere(['l_is_test' => 0]);
        $query->andWhere(['l_duplicate_lead_id' => null]);

        if ($this->dateFrom && $this->dateTo) {
            $query->andWhere(['>=', 'DATE(' . Lead::tableName() . '.l_status_dt)', $this->dateFrom]);
            $query->andWhere(['<=', 'DATE(' . Lead::tableName() . '.l_status_dt)', $this->dateTo]);
        }

        $query->innerJoin([
            'segments' => LeadFlightSegment::find()
                ->select(['lead_id'])
                ->groupBy(['lead_id'])
                ->cache($cacheDuration)
        ], Lead::tableName() . '.id = segments.lead_id');

        $query->innerJoin([
            'lead_flow' => LeadFlow::find()
                ->select(['lead_id'])
                ->where(['AND',
                    ['lf_owner_id' => $this->currentUser->getId()],
                    ['lf_description' => LeadFlow::DESCRIPTION_MANUAL_CREATE],
                ])
                ->orWhere(['AND',
                    ['lf_owner_id' => $this->currentUser->getId()],
                    ['lf_description' => LeadFlow::DESCRIPTION_CLIENT_CHAT_CREATE],
                ])
                ->orWhere(['AND',
                    ['lf_owner_id' => $this->currentUser->getId()],
                    ['status' => Lead::STATUS_PROCESSING],
                    ['lf_from_status_id' => Lead::STATUS_PENDING],
                ])
                ->groupBy(['lead_id'])
                ->cache($cacheDuration)
        ], Lead::tableName() . '.id = lead_flow.lead_id');

        $query->leftJoin([
            'my_leads' => Lead::find()
                ->select([Lead::tableName() . '.id'])
                ->where([Lead::tableName() . '.employee_id' => $this->currentUser->getId()])
                ->cache($cacheDuration)
        ], Lead::tableName() . '.clone_id = my_leads.id')
        ->andWhere(['my_leads.id' => null]);

        $query->leftJoin([
            'emails' => Email::find()
                ->select(['e_lead_id'])
                ->groupBy(['e_lead_id'])
                ->cache($cacheDuration)
        ], Lead::tableName() . '.id = emails.e_lead_id');

        $query->leftJoin([
            'sms' => Sms::find()
                ->select(['s_lead_id'])
                ->groupBy(['s_lead_id'])
                ->cache($cacheDuration)
        ], Lead::tableName() . '.id = sms.s_lead_id');

        $fromMonth = date("m", strtotime($this->dateFrom));
        $fromYear = date("Y", strtotime($this->dateFrom));
        $toMonth = date("m", strtotime($this->dateTo));
        $toYear = date("Y", strtotime($this->dateTo));

        $query->leftJoin([
            'calls' => CallLog::find()
                ->select(['cll_lead_id'])
                ->innerJoin(CallLogLead::tableName(), 'cl_id = cll_cl_id')
                ->innerJoin(CallLogRecord::tableName(), 'cl_id = clr_cl_id')
                ->where([
                    'AND',
                        ['cl_type_id' => CallLogType::OUT],
                        ['cl_status_id' => CallLogStatus::COMPLETE],
                        ['>=', 'clr_duration', 30]
                ])
                ->orWhere([
                    'AND',
                        ['cl_type_id' => CallLogType::IN],
                        ['cl_status_id' => CallLogStatus::COMPLETE],
                        ['>=', 'clr_duration', 30]
                ])
                ->andWhere(
                    '(cl_year = :fromYear AND cl_month = :fromMonth) OR (cl_year = :toYear AND cl_month = :toMonth)',
                    [':fromYear' => $fromYear, ':fromMonth' => $fromMonth, ':toYear' => $toYear, ':toMonth' => $toMonth]
                )
                ->groupBy(['cll_lead_id'])
                ->cache($cacheDuration)
        ], Lead::tableName() . '.id = calls.cll_lead_id');

        $query->leftJoin([
            'chat' => ClientChatLead::find()
                ->select(['ccl_lead_id'])
                ->groupBy(['ccl_lead_id'])
                ->cache($cacheDuration)
        ], Lead::tableName() . '.id = chat.ccl_lead_id');

        $query->andWhere([
            'OR',
                ['IS NOT', 'emails.e_lead_id', null],
                ['IS NOT', 'sms.s_lead_id', null],
                ['IS NOT', 'calls.cll_lead_id', null],
                ['IS NOT', 'chat.ccl_lead_id', null],
        ]);

        $query->cache($cacheDuration);

        return $query;
    }

    public function formName(): string
    {
        return '';
    }

    public function getCurrentUser(): Employee
    {
        return $this->currentUser;
    }

    public function getMinDate(): string
    {
        return $this->minDate;
    }

    public function getMaxDate(): string
    {
        return $this->maxDate;
    }
}
