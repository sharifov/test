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
use sales\model\leadUserConversion\entity\LeadUserConversion;
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
 * @property $luc_created_dt
 * @property $luc_description
 * @property string|null $dateRange
 * @property string|null $defaultDateRange
 * @property string|null $dateFrom
 * @property string|null $dateTo
 * @property string $minDate
 * @property string $maxDate
 * @property string $defaultMinDate
 * @property int|null $status_id
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
    public $status_id;
    public $l_status_dt;
    public $created;
    public $luc_created_dt;
    public $luc_description;

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

            [['luc_created_dt'], 'date', 'format' => 'php:Y-m-d'],
            [['luc_description'], 'string', 'max' => 100],
            ['status_id', 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'luc_created_dt' => 'Qualified date',
            'luc_description' => 'Description',
            'status_id' => 'Status',
        ];
    }

// TODO: To remove in future
    /*public function searchByUserOld(array $params, int $cacheDuration = -1)
    {
        $query = (new Query());
        $query->from(Lead::tableName());
        $query->select(Lead::tableName() . '.*');
        $query->addSelect(['gross_profit' => new Expression('ROUND(final_profit - agents_processing_fee, 2)')]);
        $query->innerJoin(LeadUserConversion::tableName(), Lead::tableName() . '.id = luc_lead_id');

        $query->where(['luc_user_id' => $this->currentUser->getId()]);
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
            $query->andWhere(['>=', 'DATE(luc_created_dt)', $this->dateFrom]);
            $query->andWhere(['<=', 'DATE(luc_created_dt)', $this->dateTo]);
        }

        if ($this->final_profit) {
            $query->andHaving(['=', 'gross_profit', $this->final_profit]);
        }

        $query->cache($cacheDuration);

        return $dataProvider;
    }*/

    public function searchByUser(array $params, int $cacheDuration = -1)
    {
        $this->load($params);

        if ($this->dateFrom && $this->dateTo) {
            $from = $this->dateFrom;
            $to = $this->dateTo;
        } else {
            $from = $this->minDate;
            $to = $this->maxDate;
        }

        $query = new Query();
        $query->select([
            Lead::tableName() . '.id',
            Lead::tableName() . '.gid',
            '(ROUND(if(sp.`owner_share` is null, 1, sp.`owner_share`) * (final_profit - agents_processing_fee), 2)) as gross_profit',
            'if(sp.`owner_share` is null, 1, ROUND(sp.owner_share, 2)) as share',
            'l_status_dt',
            'created'
        ]);
        $query->from(Lead::tableName());
        $query->leftJoin([
            'sp' => (new Query())->select(['id', '(100 - sum(ps_percent)) / 100 as owner_share'])
                ->from(Lead::tableName())
                ->innerJoin('profit_split', 'ps_lead_id = id')
                ->where(['status' => Lead::STATUS_SOLD])
                ->andWhere(['BETWEEN', 'l_status_dt', $from, $to])
                ->andWhere(['employee_id' => $this->currentUser->getId()])
                ->groupBy(['id'])
        ], 'sp.id = leads.id');
        $query->where(['status' => Lead::STATUS_SOLD]);
        $query->andWhere(['BETWEEN', 'l_status_dt', $from, $to]);
        $query->andWhere(['employee_id' => $this->currentUser->getId()]);

        if ($this->id) {
            $query->andWhere(['=', Lead::tableName() . '.id', $this->id]);
        }

        if ($this->final_profit) {
            $query->andWhere(['=', 'gross_profit', $this->final_profit]);
        }

        if ($this->l_status_dt) {
            $query->andWhere(['=', 'DATE(l_status_dt)', $this->l_status_dt]);
        }

        if ($this->created) {
            $query->andWhere(['=', 'DATE(created)', $this->created]);
        }

        $complementaryQuery = new Query();
        $complementaryQuery->select([
            'id',
            'gid',
            '(ROUND((final_profit - agents_processing_fee) * ps_percent/100, 2)) as gross_profit',
            'ROUND((ps_percent / 100), 2) as share',
            'l_status_dt',
            'created'
        ]);
        $complementaryQuery->from(Lead::tableName());
        $complementaryQuery->innerJoin('profit_split', 'ps_lead_id = id and ps_user_id = ' . $this->currentUser->getId());
        $complementaryQuery->where(['status' => Lead::STATUS_SOLD]);
        $complementaryQuery->andWhere(['BETWEEN', 'l_status_dt', $from, $to]);

        if ($this->id) {
            $complementaryQuery->andWhere(['=', 'id', $this->id]);
        }

        if ($this->final_profit) {
            $complementaryQuery->andWhere(['=', 'gross_profit', $this->final_profit]);
        }

        if ($this->l_status_dt) {
            $complementaryQuery->andWhere(['=', 'DATE(l_status_dt)', $this->l_status_dt]);
        }

        if ($this->created) {
            $complementaryQuery->andWhere(['=', 'DATE(created)', $this->created]);
        }

        $query->union($complementaryQuery, true);

        //$query->cache($cacheDuration);

        return $query->createCommand()->queryAll();
    }

    public function searchQualifiedLeads(array $params, int $cacheDuration = -1)
    {
        $query = (new Query());
        $query->from(Lead::tableName());
        $query->select([
            Lead::tableName() . '.*',
            LeadUserConversion::tableName() . '.luc_description',
            LeadUserConversion::tableName() . '.luc_created_dt',
        ]);
        $query->innerJoin(LeadUserConversion::tableName(), Lead::tableName() . '.id = luc_lead_id');
        $query->where(['luc_user_id' => $this->currentUser->getId()]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['luc_created_dt' => SORT_DESC],
                'attributes' => [
                    'luc_created_dt' => [
                        'asc' => ['luc_created_dt' => SORT_ASC],
                        'desc' => ['luc_created_dt' => SORT_DESC],
                        'label' => 'Created',
                    ],
                    'luc_description'  => [
                        'asc' => ['luc_description' => SORT_ASC],
                        'desc' => ['luc_description' => SORT_DESC],
                        'label' => 'Description',
                    ],
                    'status_id'  => [
                        'asc' => ['status' => SORT_ASC],
                        'desc' => ['status' => SORT_DESC],
                        'label' => 'Status',
                    ],
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

        if ($this->dateFrom && $this->dateTo) {
            $query->andWhere(['>=', 'DATE(luc_created_dt)', $this->dateFrom]);
            $query->andWhere(['<=', 'DATE(luc_created_dt)', $this->dateTo]);
        }

        $query->andFilterWhere([
            Lead::tableName() . '.id' => $this->id,
            'DATE(luc_created_dt)' => $this->luc_created_dt,
        ]);

        $query->andFilterWhere(['like', 'luc_description', $this->luc_description]);

        $query->andFilterWhere(['=', 'status', $this->status_id]);

        $query->cache($cacheDuration);

        return $dataProvider;
    }

    public function qualifiedLeadsTakenQuery(array $params, int $cacheDuration = -1): LeadQuery
    {
        $query = Lead::find();
        $query->select(Lead::tableName() . '.*');
        $query->innerJoin(LeadUserConversion::tableName(), Lead::tableName() . '.id = luc_lead_id');
        $query->where(['luc_user_id' => $this->currentUser->getId()]);

        if ($this->dateFrom && $this->dateTo) {
            $query->andWhere(['>=', 'DATE(luc_created_dt)', $this->dateFrom]);
            $query->andWhere(['<=', 'DATE(luc_created_dt)', $this->dateTo]);
        }

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
