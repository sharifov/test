<?php

namespace src\model\lead\reports;

use common\components\validators\IsArrayValidator;
use common\models\Call;
use common\models\Lead;
use common\models\Project;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\validators\DateValidator;

/**
 * Class HeatMapLeadSearch
 */
class HeatMapLeadSearch extends Model
{
    public $dateRange;
    public $project;

    private ?string $toDT = null;
    private ?string $fromDT = null;
    private string $toDefaultDT;
    private string $fromDefaultDT;
    private int $intervalDaysDefault = 30;

    /**
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        $this->setDataRangeDefault();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['dateRange'], 'required'],
            [['dateRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['dateRange'], 'dateRangeProcessing'],

            [['project'], 'required'],
            [['project'], IsArrayValidator::class],
            [['project'], 'each', 'rule' => ['in', 'range' => array_keys(Project::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    private function queryCondition(ActiveQuery $query): ActiveQuery
    {
        $query->andWhere(['BETWEEN', 'created', $this->getFromDT(), $this->getToDT()]);
        return $query;
    }

    public function leadChtHeatMap(array $params, int $cacheDuration = -1): array
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
        $query->cache($cacheDuration);

        return $query->asArray()->all();
    }

    public function leadChtByHour(array $params, int $cacheDuration = -1): array
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
        $query->cache($cacheDuration);

        return $query->asArray()->all();
    }

    public function leadChtByMonthDay(array $params, int $cacheDuration = -1): array
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
        $query->cache($cacheDuration);

        return $query->asArray()->all();
    }

    /**
     * @throws \Exception
     */
    private function setDataRangeDefault(): void
    {
        $currentDT = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
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
}
