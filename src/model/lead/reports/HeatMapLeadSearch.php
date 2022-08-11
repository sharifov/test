<?php

namespace src\model\lead\reports;

use common\components\validators\IsArrayValidator;
use common\models\Call;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use common\models\Sources;
use common\models\UserGroup;
use common\models\UserGroupAssign;
use src\helpers\query\QueryHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\validators\DateValidator;

/**
 * Class HeatMapLeadSearch
 */
class HeatMapLeadSearch extends Model
{
    public $dateRange;
    public $project;
    public $employee;
    public $department;
    public $userGroup;
    public $source;
    public $cache;
    public $typeCreate;
    public $isAnswered;
    public $timeZone;

    private ?string $toDT = null;
    private ?string $fromDT = null;
    private string $toDefaultDT;
    private string $fromDefaultDT;
    private int $intervalDaysDefault = 30;

    /**
     * @throws \Exception
     */
    public function __construct(string $defaultTimeZone, array $config = [])
    {
        $this->timeZone = $defaultTimeZone;
        $this->setDataRangeDefault();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['dateRange'], 'required'],
            [['dateRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['dateRange'], 'dateRangeProcessing'],

            [['project'], IsArrayValidator::class],
            [['project'], 'each', 'rule' => ['in', 'range' => array_keys(Project::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['department'], IsArrayValidator::class],
            [['department'], 'each', 'rule' => ['in', 'range' => array_keys(Department::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['userGroup'], IsArrayValidator::class],
            [['userGroup'], 'each', 'rule' => ['in', 'range' => array_keys(UserGroup::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['source'], IsArrayValidator::class],

            [['employee'], IsArrayValidator::class],
            [['employee'], 'each', 'rule' => ['in', 'range' => array_keys(Employee::getActiveUsersList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['cache'], 'integer'],
            [['cache'], 'default', 'value' => 300],

            ['timeZone', 'string'],
            ['timeZone', 'in', 'range' => array_keys(Employee::timezoneList(true))],

            [['typeCreate'], IsArrayValidator::class],
            [['typeCreate'], 'each', 'rule' => ['in', 'range' => array_keys(Lead::TYPE_CREATE_LIST)], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['isAnswered'], 'in', 'range' => [0, 1]],
        ];
    }

    private function queryCondition(ActiveQuery $query): ActiveQuery
    {
        $from = QueryHelper::getDateFromUserTZToUtc($this->getFromDT(), $this->timeZone)->format('Y-m-d H:i');
        $to = QueryHelper::getDateFromUserTZToUtc($this->getToDT(), $this->timeZone)->format('Y-m-d H:i');
        $query->andWhere(['BETWEEN', 'created', $from, $to]);

        if ($this->project) {
            $query->andWhere(['IN', 'project_id', $this->project]);
        }
        if ($this->department) {
            $query->andWhere(['IN', 'l_dep_id', $this->department]);
        }
        if ($this->source) {
            $query->andWhere(['IN', 'source_id', $this->source]);
        }
        if ($this->userGroup) {
            $query->innerJoin([
                'userGroupAssign' => UserGroupAssign::find()
                    ->select(['ugs_user_id'])
                    ->andWhere(['IN', 'ugs_group_id', $this->userGroup])
                    ->groupBy(['ugs_user_id'])
            ], 'userGroupAssign.ugs_user_id = employee_id');
        }

        if ($this->employee) {
            $query->andWhere(['IN', 'employee_id', $this->employee]);
        }

        if ($this->typeCreate) {
            $query->andWhere(['IN', 'l_type_create', $this->typeCreate]);
        }

        if (ArrayHelper::isIn($this->isAnswered, ['1', '0'], false)) {
            $query->andWhere(['l_answered' => (int)$this->isAnswered]);
        }

        return $query;
    }

    public function leadCountHeatMap(array $params): array
    {
        $query = Lead::find();
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
        }

        $query->addSelect(['MONTH(created) AS monthHeatMap']);
        $query->addSelect(['DAY(created) AS dayHeatMap']);
        $query->addSelect(['HOUR(created) AS hourHeatMap']);
        $query->addSelect(['COUNT(*) AS cnt']);
        $query = $this->queryCondition($query);
        $query->groupBy(['monthHeatMap', 'dayHeatMap', 'hourHeatMap']);
        $query->orderBy(['monthHeatMap' => SORT_ASC, 'dayHeatMap' => SORT_ASC, 'hourHeatMap' => SORT_ASC]);
        $query->indexBy(function ($row) {
            return $row['monthHeatMap'] . '-' . $row['dayHeatMap'] . '-' . $row['hourHeatMap'];
        });
        $query->cache($this->cache);

        return $query->asArray()->all();
    }

    public function leadChtByHour(array $params): array
    {
        $query = Lead::find();
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
        }

        $query->addSelect(['HOUR(created) AS hourHeatMap']);
        $query->addSelect(['COUNT(*) AS cnt']);
        $query = $this->queryCondition($query);
        $query->groupBy(['hourHeatMap']);
        $query->indexBy(function ($row) {
            return $row['hourHeatMap'];
        });
        $query->cache($this->cache);

        return $query->asArray()->all();
    }

    public function leadChtByMonthDay(array $params): array
    {
        $query = Lead::find();
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
        }

        $query->addSelect(['MONTH(created) AS monthHeatMap']);
        $query->addSelect(['DAY(created) AS dayHeatMap']);
        $query->addSelect(['COUNT(*) AS cnt']);
        $query = $this->queryCondition($query);
        $query->groupBy(['monthHeatMap', 'dayHeatMap']);
        $query->indexBy(function ($row) {
            return $row['monthHeatMap'] . '-' . $row['dayHeatMap'];
        });
        $query->cache($this->cache);

        return $query->asArray()->all();
    }

    /**
     * @throws \Exception
     */
    private function setDataRangeDefault(): void
    {
        $currentDT = new \DateTimeImmutable('now', new \DateTimeZone($this->timeZone));
        $this->fromDefaultDT = $currentDT->modify('-' . $this->getIntervalDaysDefault() . ' day')->format('Y-m-d 00:00');
        $this->toDefaultDT = $currentDT->modify('-1 day')->format('Y-m-d 23:59');

        $this->dateRange = $this->fromDefaultDT . ' - ' . $this->toDefaultDT;
    }

    /**
     * @param $attribute
     * @return void
     * @throws \Exception
     */
    public function dateRangeProcessing($attribute): void
    {
        $range = explode(' - ', $this->$attribute);
        if (count($range) !== 2) {
            $this->addError($attribute, 'Range From date or To date is incorrect');
            return;
        }

        $dateTimeValidator = new DateValidator([
            'type' => DateValidator::TYPE_DATETIME,
            'format' => 'php:Y-m-d H:i'
        ]);
        $dateTimeValidator->validate($range[0], $errors);
        if ($errors) {
            $this->addError($attribute, 'Range From date is incorrect');
            return;
        }
        $dateTimeValidator->validate($range[1], $errors);
        if ($errors) {
            $this->addError($attribute, 'Range To date is incorrect');
            return;
        }

        $from = new \DateTimeImmutable($range[0]);
        $to = new \DateTimeImmutable($range[1]);
        if ($from >= $to) {
            $this->addError($attribute, 'Range From date more than To date');
            return;
        }

        $this->fromDT = $from->format('Y-m-d H:i');
        $this->toDT = $to->format('Y-m-d H:i');
    }

    public function getIntervalDaysDefault(): int
    {
        return $this->intervalDaysDefault;
    }

    public function setIntervalDaysDefault(int $intervalDaysDefault): HeatMapLeadSearch
    {
        $this->intervalDaysDefault = $intervalDaysDefault;
        return $this;
    }

    public function getFromDT(): string
    {
        return $this->fromDT ?: $this->fromDefaultDT;
    }

    public function getToDT(): string
    {
        return $this->toDT ?: $this->toDefaultDT;
    }

    public function formName(): string
    {
        return '';
    }

    public function getToDefaultDT(): string
    {
        return $this->toDefaultDT;
    }

    public function getFromDefaultDT(): string
    {
        return $this->fromDefaultDT;
    }
}
