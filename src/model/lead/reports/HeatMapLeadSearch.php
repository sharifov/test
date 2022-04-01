<?php

namespace src\model\lead\reports;

use common\components\validators\IsArrayValidator;
use common\models\Project;
use yii\base\Model;
use yii\validators\DateValidator;

/**
 * Class HeatMapLeadSearch
 */
class HeatMapLeadSearch extends Model
{
    public $dateRange;

    private $dateFrom;
    private $dateTo;

    private string $endDefaultDT;
    private string $startDefaultDT;
    private int $intervalDaysDefault = 30;

    public function __construct(
        array $config = []
    ) {
        $this->setDataRangeDefault();
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['dateRange', 'required'],
            ['dateRange', 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            ['dateRange', 'dateRangeProcessing', 'params' => ['minStartFrom' => '2018-01-01 00:00', 'maxEndTo' => date("Y-m-d 23:59")]],

            /* TODO:: minStartFrom */

            ['project', IsArrayValidator::class],
            ['project', 'each', 'rule' => ['in', 'range' => array_keys(Project::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    /**
     * @throws \Exception
     */
    private function setDataRangeDefault(): void
    {
        $currentDT = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->startDefaultDT = $currentDT->modify('-' . $this->getIntervalDaysDefault() . ' day')->format('Y-m-d 00:00:00');
        $this->endDefaultDT = $currentDT->modify('-1 day')->format('Y-m-d 23:59:59');
    }

    public function dateRangeProcessing($attribute, $params): void
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
        if ($from > $to) {
            $this->addError($attribute, 'Range From date more than To date');
            return;
        }
        if ($from == $to) {
            $this->addError($attribute, 'Range From date and To date is equal');
            return;
        }

        $paramsFrom = new \DateTimeImmutable($params['minStartFrom']);
        if ($from < $paramsFrom) {
            $this->addError($attribute, 'Range From date must be more or equal than ' . $paramsFrom->format('Y-m-d H:i'));
            return;
        }

        $paramsTo = new \DateTimeImmutable($params['maxEndTo']);
        if ($to > $paramsTo) {
            $this->addError($attribute, 'Range To date must be less or equal than ' . $paramsTo->format('Y-m-d H:i'));
            return;
        }

        $this->dateFrom = $from->format('Y-m-d H:i');
        $this->dateTo = $to->format('Y-m-d H:i');
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
}
