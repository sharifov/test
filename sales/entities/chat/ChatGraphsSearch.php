<?php

namespace sales\entities\chat;

use common\models\Employee;
use sales\model\clientChat\entity\search\ClientChatSearch;
use sales\model\clientChat\entity\ClientChat;
use yii\data\SqlDataProvider;

class ChatGraphsSearch extends ClientChatSearch
{
    public string $createTimeRange;
    public string $createTimeStart;
    public string $createTimeEnd;
    public int $graphGroupBy;

    public const CREATE_TIME_START_DEFAULT = '-29 days';

    public const DATE_FORMAT_DAYS = 0;
    public const DATE_FORMAT_HOURS = 4;
    public const DATE_FORMAT_WEEKS = 2;
    public const DATE_FORMAT_MONTH = 3;
    public const DATE_FORMAT_HOURS_DAYS = 1;
    public const DATE_FORMAT_WEEKDAYS = 5;

    public const DATE_FORMAT_TEXT = [
        self::DATE_FORMAT_HOURS => 'Hour',
        self::DATE_FORMAT_DAYS => 'Day',
        self::DATE_FORMAT_WEEKS => 'Week',
        self::DATE_FORMAT_MONTH => 'Month',
        self::DATE_FORMAT_HOURS_DAYS => 'Hour of the Day',
        self::DATE_FORMAT_WEEKDAYS => 'Day of the Week',

    ];

    public const DATE_FORMAT_LIST = [
        self::DATE_FORMAT_HOURS_DAYS => '%H:00',
        self::DATE_FORMAT_HOURS => '%Y-%m-%d %H:00',
        self::DATE_FORMAT_DAYS => '%Y-%m-%d',
        self::DATE_FORMAT_WEEKS => '%v',
        self::DATE_FORMAT_MONTH => '%Y-%m',
        self::DATE_FORMAT_WEEKDAYS => '%W',
    ];

    public function rules(): array
    {
        return [
            [['createTimeRange', 'createTimeStart', 'createTimeEnd'], 'string'],
            ['graphGroupBy', 'integer'],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->createTimeRange = date('Y-m-d 00:00:00', strtotime(self::CREATE_TIME_START_DEFAULT)) . ' - ' . date('Y-m-d 23:59:59');
        $range = explode(' - ', $this->createTimeRange);
        $this->createTimeStart = $range[0];
        $this->createTimeEnd = $range[1];

        $this->graphGroupBy = self::DATE_FORMAT_DAYS;
    }

    public function stats()
    {
        if ($this->createTimeRange) {
            $range = explode(' - ', $this->createTimeRange);
            $this->createTimeStart = $range[0];
            $this->createTimeEnd = $range[1];
        }

        $query = static::find();
        $query->select([
            ''. $this->setGroupingParam() .' AS date',
            'SUM(IF(cch_status_id = '. ClientChat::STATUS_NEW .', 1, 0)) AS new',
            'SUM(IF(cch_status_id = '. ClientChat::STATUS_PENDING .', 1, 0)) AS pending',
            'SUM(IF(cch_status_id = '. ClientChat::STATUS_IN_PROGRESS .', 1, 0)) AS progress',
            'SUM(IF(cch_status_id = '. ClientChat::STATUS_TRANSFER .', 1, 0)) AS transfer',
            'SUM(IF(cch_status_id = '. ClientChat::STATUS_HOLD .', 1, 0)) AS hold',
            'SUM(IF(cch_status_id = '. ClientChat::STATUS_CLOSED .', 1, 0)) AS closed'
        ]);

        $query->andWhere([
            'between',
            'cch_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd))
        ]);

        $query->groupBy(['date']);

        return new SqlDataProvider([
            'sql' => $query->createCommand()->rawSql,
            'pagination' => false
        ]);
    }

    /**
     * @return array
     */
    public static function getDateFormatTextList(): array
    {
        return self::DATE_FORMAT_TEXT;
    }

    /**
     * @param $dateFormatId
     * @return string|null
     */
    private function getDateFormat($dateFormatId): ?string
    {
        return self::DATE_FORMAT_LIST[$dateFormatId] ?? null;
    }

    private function setGroupingParam()
    {
        $format = $this->getDateFormat($this->graphGroupBy);
        if($this->graphGroupBy === self::DATE_FORMAT_HOURS){
            return "date_format(cch_created_dt, '$format')";
        } else if ($this->graphGroupBy === self::DATE_FORMAT_DAYS){
            return "date_format(cch_created_dt, '$format')";
        } else if ($this->graphGroupBy === self::DATE_FORMAT_WEEKS){
            return "concat(str_to_date(date_format(cch_created_dt, '%Y %v Monday'), '%x %v %W'), ' - ', str_to_date(date_format(cch_created_dt, '%Y %v Sunday'), '%x %v %W'))";
        } else if ($this->graphGroupBy === self::DATE_FORMAT_MONTH){
            return "date_format(cch_created_dt, '$format')";
        } else if ($this->graphGroupBy === self::DATE_FORMAT_HOURS_DAYS){
            return "date_format(cch_created_dt, '$format')";
        } else if ($this->graphGroupBy === self::DATE_FORMAT_WEEKDAYS){
            return "WEEKDAY(cch_created_dt)";
        }
    }
}