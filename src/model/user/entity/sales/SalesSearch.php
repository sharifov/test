<?php

namespace src\model\user\entity\sales;

use common\models\Call;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\LeadFlow;
use common\models\query\LeadQuery;
use common\models\Sms;
use src\helpers\query\QueryHelper;
use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogStatus;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\callLog\entity\callLogLead\CallLogLead;
use src\model\callLog\entity\callLogRecord\CallLogRecord;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\leadUserConversion\entity\LeadUserConversion;
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
 *
 * @Annotation:
 * Qualified Leads Taken - кол-во уникальных лидов из таблицы Lead User Conversion, где дата создания записи попадает в указанный диапазон
 * Split Share - это доля участия пользователя в продажах лидов, равно сумме ps_percent / 100 из таблицы Profit Split для лидов со статусом Sold где дата продажи (lead -> l_status_dt) попадает в указанный диапазон
 * Sold Leads - это кол-во уникальных лидов пользователя в рамках которых у него есть запись в таблице Profit Split для лидов со статусом Sold где дата продажи (lead -> l_status_dt)попадает в указанный диапазон
 * Gross Profit  - это сумма доходов пользователя по лидам в которых он имеют долю в продаже, равно сумме ((Lead Final Profit - Lead Agent Processing Fee)* Profit Split Percent / 100) окргуленной до двух знаков после запятой, для записей в таблице Profit Split для лидов со статусом Sold где дата продажи (lead -> l_status_dt) попадает в указанный диапазон
 * Sale Conversion - это отношение Split Share к Qualified Leads Taken выражженое в процентах, с округлением до 2 знаков после запятой
 */
class SalesSearch extends Model
{
    public const DEFAULT_TIMEZONE = 'America/New_York';

    public $timeZone;
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
        $this->timeZone = self::DEFAULT_TIMEZONE;

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

            ['timeZone', 'string'],
            ['timeZone', 'in', 'range' => array_keys(Employee::timezoneList(true))],
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

    public function searchByUser(array $params, int $cacheDuration = -1)
    {
        $this->load($params);

        if ($this->dateFrom && $this->dateTo) {
            $from = $this->dateFrom;
            $to = $this->dateTo;
        } else {
            $from = $this->defaultMinDate;
            $to = $this->maxDate;
        }
        /** @fflag FFlag::FF_KEY_CONVERSION_BY_TIMEZONE, Conversion Filter by Timezone */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CONVERSION_BY_TIMEZONE)) {
            $from = QueryHelper::getDateFromUserTZToUtc($from . ' 00:00', $this->timeZone)->format('Y-m-d H:i');
            $to = QueryHelper::getDateFromUserTZToUtc($to . ' 23:59', $this->timeZone)->format('Y-m-d H:i');
        }

        $query = new Query();
        $query->select([
            'id',
            'gid',
            '(ROUND((final_profit - agents_processing_fee) * ps_percent/100, 2)) as gross_profit',
            'ROUND((ps_percent / 100), 2) as share',
            'l_status_dt',
            'created'
        ]);
        $query->from(Lead::tableName());
        $query->innerJoin('profit_split', 'ps_lead_id = id and ps_user_id = ' . $this->currentUser->getId());
        $query->where(['status' => Lead::STATUS_SOLD]);
        $query->andWhere(['BETWEEN', 'l_status_dt', $from, $to]);

        if ($this->id) {
            $query->andWhere(['=', 'id', $this->id]);
        }

        if ($this->l_status_dt) {
            /** @fflag FFlag::FF_KEY_CONVERSION_BY_TIMEZONE, Conversion Filter by Timezone */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CONVERSION_BY_TIMEZONE)) {
                $l_status_dt_from = QueryHelper::getDateFromUserTZToUtc($this->l_status_dt, $this->timeZone)->format('Y-m-d H:i:s');
                $l_status_dt_to = QueryHelper::getDateFromUserTZToUtc($this->l_status_dt . ' 23:59:59', $this->timeZone)->format('Y-m-d H:i:s');
                $query->andWhere(['BETWEEN', 'l_status_dt', $l_status_dt_from, $l_status_dt_to]);
            } else {
                $query->andWhere(['=', 'DATE(l_status_dt)', $this->l_status_dt]);
            }
        }

        if ($this->created) {
            /** @fflag FFlag::FF_KEY_CONVERSION_BY_TIMEZONE, Conversion Filter by Timezone */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CONVERSION_BY_TIMEZONE)) {
                $created_from = QueryHelper::getDateFromUserTZToUtc($this->created, $this->timeZone)->format('Y-m-d H:i:s');
                $created_to = QueryHelper::getDateFromUserTZToUtc($this->created . ' 23:59:59', $this->timeZone)->format('Y-m-d H:i:s');
                $query->andWhere(['BETWEEN', 'created', $created_from, $created_to]);
            } else {
                $query->andWhere(['=', 'DATE(created)', $this->created]);
            }
        }

        if ($this->final_profit) {
            $query->andHaving(['=', 'gross_profit', $this->final_profit]);
        }

        $query->cache($cacheDuration);

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
            /** @fflag FFlag::FF_KEY_CONVERSION_BY_TIMEZONE, Conversion Filter by Timezone */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CONVERSION_BY_TIMEZONE)) {
                $from = QueryHelper::getDateFromUserTZToUtc($this->dateFrom . ' 00:00', $this->timeZone)->format('Y-m-d H:i');
                $to = QueryHelper::getDateFromUserTZToUtc($this->dateTo . ' 23:59', $this->timeZone)->format('Y-m-d H:i');
                $query->andWhere(['>=', 'luc_created_dt', $from]);
                $query->andWhere(['<=', 'luc_created_dt', $to]);
            } else {
                $query->andWhere(['>=', 'luc_created_dt', $this->dateFrom]);
                $query->andWhere(['<=', 'luc_created_dt', $this->dateTo]);
            }
        }

        $query->andFilterWhere([
            Lead::tableName() . '.id' => $this->id
        ]);

        if ($this->luc_created_dt) {
            /** @fflag FFlag::FF_KEY_CONVERSION_BY_TIMEZONE, Conversion Filter by Timezone */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_CONVERSION_BY_TIMEZONE)) {
                $luc_created_dt_from = QueryHelper::getDateFromUserTZToUtc($this->luc_created_dt, $this->timeZone)->format('Y-m-d H:i:s');
                $luc_created_dt_to = QueryHelper::getDateFromUserTZToUtc($this->luc_created_dt . ' 23:59:59', $this->timeZone)->format('Y-m-d H:i:s');
                $query->andWhere(['BETWEEN', 'luc_created_dt', $luc_created_dt_from, $luc_created_dt_to]);
            } else {
                $query->andWhere(['=', 'DATE(luc_created_dt)', $this->luc_created_dt]);
            }
        }

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

    public function getTimezone(): string
    {
        if (!$this->timeZone) {
            return "UTC";
        }
        return $this->timeZone;
    }
}
