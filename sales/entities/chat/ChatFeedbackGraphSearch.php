<?php


namespace sales\entities\chat;


use common\models\Employee;
use sales\model\clientChatFeedback\entity\ClientChatFeedbackSearch;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use Yii;

class ChatFeedbackGraphSearch extends ClientChatFeedbackSearch
{
    public string $timeRange;
    public string $startTimeRange = '';
    public string $endTimeRange = '';
    public string $projectID = '';
    public string $channelID = '';
    public string $ccfUserID = '';
    public int $groupBy;
    public string $timeZone;

    public const DEFAULT_PERIOD = '-6 days';

    public const GROUP_BY_HOURS = 1;
    public const GROUP_BY_DAYS = 2;
    public const GROUP_BY_WEEKS = 3;
    public const GROUP_BY_MONTH = 4;
    /*public const DATE_FORMAT_HOURS_DAYS = 1;
    public const DATE_FORMAT_WEEKDAYS = 5;*/

    public const GROUP_FORMAT_HOURS = 'H:00:00';
    public const GROUP_FORMAT_DAYS = 'Y-m-d';
    public const GROUP_FORMAT_MONTH = 'Y-m';

    public const GROUP_TEXT_LABELS = [
        self::GROUP_BY_HOURS => 'Hour',
        self::GROUP_BY_DAYS => 'Day',
        self::GROUP_BY_WEEKS => 'Week',
        self::GROUP_BY_MONTH => 'Month',
        /*self::DATE_FORMAT_HOURS_DAYS => 'Hour of the Day',
        self::DATE_FORMAT_WEEKDAYS => 'Day of the Week',*/
    ];

    public function rules(): array
    {
        return [
            [['timeRange', 'startTimeRange', 'endTimeRange', 'projectID', 'channelID', 'ccfUserID', 'ccf_created_dt', 'ccf_message'], 'string'],
            [['ccf_id', 'ccf_rating', 'ccf_client_id', 'ccf_user_id', 'groupBy'], 'integer'],
            ['timeRange', 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            ['timeRange', 'required'],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->timeRange = date('Y-m-d 00:00:00', strtotime(self::DEFAULT_PERIOD)) . ' - ' . date('Y-m-d 23:59:59');
        $this->groupBy = self::GROUP_BY_DAYS;
        $this->timeZone = $this->timeZone ?? Yii::$app->user->identity->timezone;
    }

    public function stats()
    {
        if ($this->timeRange) {
            $range = explode(' - ', $this->timeRange);
            $this->startTimeRange = $range[0];
            $this->endTimeRange = $range[1];
        }

        $query = static::find()->joinWith(['clientChat', 'client', 'employee']);
        $query->select('*');

        $query->where([
            'between',
            'ccf_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->startTimeRange)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->endTimeRange))
        ]);

        if ($this->projectID) {
            $query->andWhere(['cch_project_id' => $this->projectID]);
        }

        if ($this->channelID) {
            $query->andWhere(['cch_channel_id' => $this->channelID]);
        }
        if ($this->ccfUserID) {
            $query->andWhere(['cch_owner_user_id' => $this->ccfUserID]);
        }

        if ($this->ccf_message === '0') {
            $query->andWhere(['>', 'LENGTH(ccf_message)', 0]);
        } elseif ($this->ccf_message === '1') {
            $query->andWhere(['=', 'LENGTH(ccf_message)', 0]);
            $query->orWhere(['ccf_message' => null]);
        }

        $query->andFilterWhere([
            'ccf_id' => $this->ccf_id,
            'ccf_client_id' => $this->ccf_client_id,
            'ccf_user_id' => $this->ccf_user_id,
            'ccf_rating' => $this->ccf_rating,
            'DATE(ccf_created_dt)' => $this->ccf_created_dt
        ]);


        return new SqlDataProvider(['sql' => $query->createCommand()->rawSql, 'pagination' => false]);
    }

    public static function getGroupsList(): array
    {
        return self::GROUP_TEXT_LABELS;
    }
}