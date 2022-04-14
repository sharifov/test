<?php

namespace src\entities\chat;

use common\models\Employee;
use common\models\UserGroupAssign;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\entity\search\ClientChatSearch;
use src\model\clientChatMessage\entity\ClientChatMessage;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\data\ArrayDataProvider;
use yii\db\Query;

class ChatExtendedGraphsSearch extends ClientChatSearch
{
    public string $createTimeRange;
    public string $createTimeStart;
    public string $createTimeEnd;
    public int $graphGroupBy;
    public $userGroupIds = [];
    public string $defaultUserTz = '';
    public string $timeZone = '';

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

    public const GROUP_FORMAT_HOURS = 'H:00:00';
    public const GROUP_FORMAT_DAYS_HOURS = 'Y-m-d H:00';

    public function rules(): array
    {
        return [
            ['timeZone', 'required'],
            [['createTimeRange', 'createTimeStart', 'createTimeEnd', 'timeZone', 'defaultUserTz'], 'string'],
            [['graphGroupBy', 'cch_owner_user_id', 'cch_channel_id', 'cch_project_id'], 'integer'],
            [['userGroupIds'], 'each', 'rule' => ['integer']],
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
        $this->defaultUserTz = \Yii::$app->user->identity->timezone;
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
            '' . $this->setGroupingParam() . ' AS date',
            'SUM(IF(cch_source_type_id = ' . ClientChat::SOURCE_TYPE_CLIENT . ', 1, 0)) AS newIncomingClientChats',
            'SUM(IF(cch_source_type_id = ' . ClientChat::SOURCE_TYPE_AGENT . ', 1, 0)) AS newOutgoingAgentChats',
            'SUM(IF(cch_source_type_id = ' . ClientChat::SOURCE_TYPE_CLIENT . ' AND (cch_status_id = ' . ClientChat::STATUS_CLOSED . ' OR cch_status_id = ' . ClientChat::STATUS_ARCHIVE . '), 1, 0)) AS initByClientClosedArchive',
            'SUM(IF(cch_source_type_id = ' . ClientChat::SOURCE_TYPE_AGENT . ' AND (cch_status_id = ' . ClientChat::STATUS_CLOSED . ' OR cch_status_id = ' . ClientChat::STATUS_ARCHIVE . '), 1, 0)) AS initByAgentClosedArchive',
            'SUM(IF(cch_missed = ' . ClientChat::MISSED . ', 1, 0)) AS missedChats',
            'GROUP_CONCAT(DISTINCT cch_owner_user_id) AS agentsInGroup'
        ]);

        if ($this->cch_project_id) {
            $query->andWhere(['cch_project_id' => $this->cch_project_id]);
        }

        $ccTblSubQuery = new Query();
        $ccTblSubQuery->select('*')->from(ClientChat::tableName())->where('' . $this->setGroupingParam() . ' = date');
        $ccTblSubQuery->andWhere(['cch_source_type_id' => [ClientChat::SOURCE_TYPE_CLIENT, ClientChat::SOURCE_TYPE_AGENT]]);

        $ccuaTblSubQuery = new Query();
        $ccuaTblSubQuery->select('*')->from(ClientChatUserAccess::tableName())->where(['ccua_status_id' => ClientChatUserAccess::STATUS_ACCEPT]);

        if ($this->cch_owner_user_id) {
            $query->andWhere(['cch_owner_user_id' => $this->cch_owner_user_id]);
            $ccuaTblSubQuery->andWhere(['ccua_user_id' => $this->cch_owner_user_id]);
        }

        if ($this->userGroupIds) {
            $userIdsByGroup = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id) as cch_owner_user_id'])->where(['ugs_group_id' => $this->userGroupIds])->asArray()->all();
            if ($userIdsByGroup) {
                $query->andWhere(['in', ['cch_owner_user_id'], $userIdsByGroup]);
            }
        }


        if ($this->cch_channel_id) {
            $query->andWhere(['cch_channel_id' => $this->cch_channel_id]);
            $ccTblSubQuery->andWhere(['cch_channel_id' => $this->cch_channel_id]);
        }

        $query->addSelect([
            'acceptedByAgentSourceAgent' => (new Query())
                ->select('COUNT(*)')
                ->from([
                    'ccTbl' => $ccTblSubQuery,
                    'ccuaTbl' => $ccuaTblSubQuery
                ])
                ->where('ccTbl.cch_id = ccuaTbl.ccua_cch_id')
                ->andWhere('ccTbl.cch_owner_user_id = ccuaTbl.ccua_user_id')
                ->andWhere('ccTbl.cch_source_type_id = ' . ClientChat::SOURCE_TYPE_AGENT),

            'acceptedByAgentSourceClient' => (new Query())
                ->select('COUNT(*)')
                ->from([
                    'ccTbl' => $ccTblSubQuery,
                    'ccuaTbl' => $ccuaTblSubQuery
                ])
                ->where('ccTbl.cch_id = ccuaTbl.ccua_cch_id')
                ->andWhere('ccTbl.cch_owner_user_id = ccuaTbl.ccua_user_id')
                ->andWhere('ccTbl.cch_source_type_id = ' . ClientChat::SOURCE_TYPE_CLIENT)
        ]);

        $query->andWhere([
            'between',
            'cch_created_dt',
            Employee::convertToUTC(strtotime($this->createTimeStart), $this->timeZone),
            Employee::convertToUTC(strtotime($this->createTimeEnd), $this->timeZone),
        ]);


        $query->groupBy(['date']);
        $allData = $query->createCommand()->queryAll();

        $queryChats = static::find()->select([
            '' . $this->setGroupingParam() . ' AS date',
            'cch_id',
            'cch_owner_user_id',
            'cch_status_id',
            'cch_source_type_id',
            'cch_created_dt',
            'cch_updated_dt'
        ]);

        if ($this->cch_owner_user_id) {
            $queryChats->andWhere(['cch_owner_user_id' => $this->cch_owner_user_id]);
        }

        if ($this->cch_channel_id) {
            $queryChats->andWhere(['cch_channel_id' => $this->cch_channel_id]);
        }

        $queryChats->andWhere('cch_owner_user_id IS NOT NULL');
        $queryChats->andWhere([
            'between',
            'cch_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd))
        ]);
        $chatsData = $queryChats->createCommand()->queryAll();

        $queryFirstAgentMsg = ClientChatMessage::find();
        $queryFirstAgentMsg->select([
            'each_item.ccm_cch_id',
            'first_msg_date',
            'ccm_user_id',
        ]);
        $queryFirstAgentMsg->innerJoin('(SELECT ccm_cch_id, MIN(ccm_sent_dt) AS first_msg_date FROM client_chat_message 
                       WHERE ccm_client_id IS NOT NULL AND ccm_user_id IS NOT NULL GROUP BY ccm_cch_id) AS each_item', 'each_item.first_msg_date = client_chat_message.ccm_sent_dt AND each_item.ccm_cch_id = client_chat_message.ccm_cch_id');

        $firstChatAgentMessage = $queryFirstAgentMsg->createCommand()->queryAll();

        $firstChatAgentMessageResults = [];
        foreach ($firstChatAgentMessage as $message) {
            $firstChatAgentMessageResults[$message['ccm_cch_id']] = $message;
        }
        unset($firstChatAgentMessage);

        $queryFirstMessagesOfChats = ClientChatMessage::find();
        $queryFirstMessagesOfChats->select([
            'each_item.ccm_cch_id',
            'first_msg_date',
            'ccm_user_id',
        ]);
        $queryFirstMessagesOfChats->innerJoin('(SELECT ccm_cch_id, MIN(ccm_sent_dt) AS first_msg_date FROM client_chat_message 
                       GROUP BY ccm_cch_id) AS each_item', 'each_item.first_msg_date = client_chat_message.ccm_sent_dt AND each_item.ccm_cch_id = client_chat_message.ccm_cch_id');

        $firstMessagesOfChats = $queryFirstMessagesOfChats->createCommand()->queryAll();

        $firstMessagesOfChatsResults = [];
        foreach ($firstMessagesOfChats as $messagesOfChat) {
            $firstMessagesOfChatsResults[$messagesOfChat['ccm_cch_id']] = $messagesOfChat;
        }

        unset($firstMessagesOfChats);


        foreach ($chatsData as $key => $chat) {
            $chatsData[$key]['agent_frt'] = 0;
            $chatsData[$key]['chat_duration'] = 0;

            if (isset($firstChatAgentMessageResults[$chat['cch_id']])) {
                $chatsData[$key]['agent_frt'] = strtotime($firstChatAgentMessageResults[$chat['cch_id']]['first_msg_date']) - strtotime($chat['cch_created_dt']);
            }


            if (isset($firstMessagesOfChatsResults[$chat['cch_id']]) && ($chat['cch_status_id'] == ClientChat::STATUS_CLOSED || $chat['cch_status_id'] == ClientChat::STATUS_ARCHIVE)) {
                $chatsData[$key]['chat_duration'] = strtotime($chat['cch_updated_dt']) - strtotime($firstMessagesOfChatsResults[$chat['cch_id']]['first_msg_date']);
            }
        }


        foreach ($allData as $key => $finalData) {
            $allData[$key]['sumFrtOfChatsInGroup'] = 0;
            $allData[$key]['sumClientChatDurationInGroup'] = 0;
            $allData[$key]['sumAgentChatDurationInGroup'] = 0;
            $agentsInGroup = explode(',', $finalData['agentsInGroup']);
            for ($i = 0; $i < count($agentsInGroup); $i++) {
                foreach ($chatsData as $chat) {
                    if (!strcmp($finalData['date'], $chat['date'])  && $agentsInGroup[$i] == $chat['cch_owner_user_id'] && $chat['cch_source_type_id'] == ClientChat::SOURCE_TYPE_CLIENT) {
                        $allData[$key]['sumFrtOfChatsInGroup'] += $chat['agent_frt'];
                    }
                    if (!strcmp($finalData['date'], $chat['date'])  && $agentsInGroup[$i] == $chat['cch_owner_user_id'] && $chat['cch_source_type_id'] == ClientChat::SOURCE_TYPE_CLIENT) {
                        $allData[$key]['sumClientChatDurationInGroup'] += $chat['chat_duration'];
                    }
                    if (!strcmp($finalData['date'], $chat['date'])  && $agentsInGroup[$i] == $chat['cch_owner_user_id'] && $chat['cch_source_type_id'] == ClientChat::SOURCE_TYPE_AGENT) {
                        $allData[$key]['sumAgentChatDurationInGroup'] += $chat['chat_duration'];
                    }
                }
            }
        }


        return new ArrayDataProvider([
            'allModels' => $allData,
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
        $offset = $this->getTimeZoneOffset();
        if ($this->graphGroupBy === self::DATE_FORMAT_HOURS) {
            return "date_format(CONVERT_TZ(cch_created_dt, '+00:00', '$offset'), '$format')";
        } elseif ($this->graphGroupBy === self::DATE_FORMAT_DAYS) {
            return "date_format(CONVERT_TZ(cch_created_dt, '+00:00', '$offset'), '$format')";
        } elseif ($this->graphGroupBy === self::DATE_FORMAT_WEEKS) {
            return "concat(str_to_date(date_format(CONVERT_TZ(cch_created_dt, '+00:00', '$offset'), '%Y %v Monday'), '%x %v %W'), '/', str_to_date(date_format(CONVERT_TZ(cch_created_dt, '+00:00', '$offset'), '%Y %v Sunday'), '%x %v %W'))";
        } elseif ($this->graphGroupBy === self::DATE_FORMAT_MONTH) {
            return "date_format(CONVERT_TZ(cch_created_dt, '+00:00', '$offset'), '$format')";
        } elseif ($this->graphGroupBy === self::DATE_FORMAT_HOURS_DAYS) {
            return "date_format(CONVERT_TZ(cch_created_dt, '+00:00', '$offset'), '$format')";
        } elseif ($this->graphGroupBy === self::DATE_FORMAT_WEEKDAYS) {
            return "WEEKDAY(CONVERT_TZ(cch_created_dt, '+00:00', '$offset'))";
        }
    }

    private function getTimeZoneOffset()
    {
        $timezone = new \DateTimeZone($this->timeZone);
        $seconds = $timezone->getOffset(new \DateTime());
        $sign = ($seconds > 0) ? '+' : '-';
        $hours = floor(abs($seconds) / 3600);
        $minutes = floor((abs($seconds) / 60) % 60);
        return $sign . sprintf("%02d:%02d", $hours, $minutes);
    }
}
