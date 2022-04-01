<?php

namespace src\model\lead\reports;

use common\components\validators\IsArrayValidator;
use common\models\Call;
use common\models\Lead;
use common\models\Project;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\validators\DateValidator;

/**
 * Class HeatMapLeadSearch
 */
class HeatMapLeadSearch extends Model
{
    public $dateRange;
    public $project;

    private string $toDT;
    private string $fromDT;
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
            ['dateRange', 'required'],
            ['dateRange', 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            ['dateRange', 'dateRangeProcessing'],

            /* TODO:: minStartFrom */

            ['project', IsArrayValidator::class],
            ['project', 'each', 'rule' => ['in', 'range' => array_keys(Project::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function search($params)
    {
        $query = Lead::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->addSelect(['MONTH(created) AS month']);
        $query->addSelect(['DAY(created) AS day']);
        $query->addSelect(['HOUR(created) AS hour']);
        $query->addSelect(['COUNT(*) AS cnt']);

        /*$query->innerJoin([
            'lead_created' => Lead::find()
                ->select(['lead_id'])
                ->andWhere(['BETWEEN', 'created', $this->fromDT, $this->toDT])
                ->groupBy(['lead_id'])
        ],'leads.id = lead_created.id');*/

        $query->andWhere(['BETWEEN', 'created', $this->fromDT, $this->toDT]);

        $query->groupBy(['MONTH(created)', 'DAY(created)', 'HOUR(created)']);

        $query->orderBy(['month' => SORT_ASC, 'day' => SORT_ASC, 'hour' => SORT_ASC]);

        // \yii\helpers\VarDumper::dump($query->createCommand()->getRawSql(), 10, true); exit();  /* FOR DEBUG:: must by remove */
        // \yii\helpers\VarDumper::dump($query->asArray()->all(), 20, true); exit();
        /* FOR DEBUG:: must by remove */

        return $dataProvider;/* TODO::  */
    }

    /**
     * @throws \Exception
     */
    private function setDataRangeDefault(): void
    {
        $currentDT = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->fromDefaultDT = $currentDT->modify('-' . $this->getIntervalDaysDefault() . ' day')->format('Y-m-d 00:00');
        $this->toDefaultDT = $currentDT->modify('-1 day')->format('Y-m-d 23:59');
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
}
