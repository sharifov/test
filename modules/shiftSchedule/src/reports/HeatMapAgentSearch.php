<?php

namespace modules\shiftSchedule\src\reports;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\UserGroup;
use yii\base\Model;

class HeatMapAgentSearch extends Model
{
    public $dateRange;
    public $userGroup;
    public $shifts;
    public $cache;
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
           // [['dateRange'], 'dateRangeProcessing'],

            [['userGroup'], IsArrayValidator::class],
            [['userGroup'], 'each', 'rule' => ['in', 'range' => array_keys(UserGroup::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            [['shifts'], IsArrayValidator::class],
            [['shifts'], 'each', 'rule' => ['in', 'range' => array_keys(UserGroup::getList())], 'skipOnError' => true, 'skipOnEmpty' => true],

            ['timeZone', 'string'],
            ['timeZone', 'in', 'range' => array_keys(Employee::timezoneList(true))],
        ];
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
}
