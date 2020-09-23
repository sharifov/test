<?php

namespace sales\entities\chat;

use common\models\Employee;
use sales\model\clientChat\entity\search\ClientChatSearch;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;

class ChatExtendedGraphsSearch extends ClientChatSearch
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
            [['graphGroupBy', 'cch_owner_user_id', 'cch_channel_id'], 'integer'],
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
            '' . $this->setGroupingParam() . ' AS date',
            'SUM(IF(cch_source_type_id = ' . ClientChat::SOURCE_TYPE_CLIENT . ' AND cch_status_id = '. ClientChat::STATUS_GENERATED . ', 1, 0)) AS initiatedByClient',
            'SUM(IF(cch_source_type_id = ' . ClientChat::SOURCE_TYPE_AGENT . ' AND cch_status_id = '. ClientChat::STATUS_GENERATED . ', 1, 0)) AS initiatedByAgent',
            'SUM(IF(cch_source_type_id = ' . ClientChat::SOURCE_TYPE_CLIENT . ' AND cch_status_id = '. ClientChat::STATUS_CLOSED. ', 1, 0)) AS initiatedByClientClosed',
            'SUM(IF(cch_source_type_id = ' . ClientChat::SOURCE_TYPE_AGENT . ' AND cch_status_id = '. ClientChat::STATUS_CLOSED . ', 1, 0)) AS initiatedByAgentClosed',
            'SUM(IF(cch_missed = ' . ClientChat::MISSED . ', 1, 0)) AS missedChats',
            'GROUP_CONCAT(DISTINCT cch_owner_user_id) AS agentsInGroup'
        ]);

        $query->where(['cch_status_id' => [ClientChat::STATUS_GENERATED, ClientChat::STATUS_CLOSED]]);

        if($this->cch_owner_user_id){
            $query->andWhere(['cch_owner_user_id' => $this->cch_owner_user_id]);
        }

        $ccTblSubQuery = new Query();
        $ccTblSubQuery->select('*')->from(ClientChat::tableName())->where('date_format(cch_created_dt, "%Y-%m-%d") = date');

        $ccuaTblSubQuery = new Query();
        $ccuaTblSubQuery->select('*')->from(ClientChatUserAccess::tableName())->where(['ccua_status_id' => ClientChatUserAccess::STATUS_ACCEPT]);

        if($this->cch_channel_id){
            $query->andWhere(['cch_channel_id' => $this->cch_channel_id]);
            $ccTblSubQuery->andWhere(['cch_channel_id' => $this->cch_channel_id]);
        }

        $query->addSelect([
            'acceptedByAgent' => (new Query())
                ->select('COUNT(*)')
                ->from([
                    //'ccTbl' => ((new Query())->select('*')->from(ClientChat::tableName())->where('date_format(cch_created_dt, "%Y-%m-%d") = date')),
                    //'ccuaTbl' => (new Query())->select('*')->from(ClientChatUserAccess::tableName())->where(['ccua_status_id' => ClientChatUserAccess::STATUS_ACCEPT])
                    'ccTbl' => $ccTblSubQuery,
                    'ccuaTbl' => $ccuaTblSubQuery
                ])
                ->where('ccTbl.cch_id = ccuaTbl.ccua_cch_id')
                ->andWhere('ccTbl.cch_owner_user_id = ccuaTbl.ccua_user_id')
        ]);

        $query->andWhere('cch_owner_user_id IS NOT NULL');
        $query->andWhere([
            'between',
            'cch_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd))
        ]);


        $query->groupBy(['date']);
        $allData = $query->createCommand()->queryAll();
        //var_dump($allData); die();

        $queryChats = static::find()->select([
            '' . $this->setGroupingParam() . ' AS date',
            'cch_id',
            'cch_owner_user_id',
            'cch_status_id',
            'cch_source_type_id',
            'cch_created_dt',
            'cch_updated_dt'
        ]);
        $queryChats->where(['cch_status_id' => [ClientChat::STATUS_GENERATED, ClientChat::STATUS_CLOSED]]);

        if($this->cch_owner_user_id){
            $queryChats->andWhere(['cch_owner_user_id' => $this->cch_owner_user_id]);
        }

        if($this->cch_channel_id){
            $queryChats->andWhere(['cch_channel_id' => $this->cch_channel_id]);
        }

        $queryChats->andWhere('cch_owner_user_id IS NOT NULL');
        //$queryChats->andWhere(['cch_source_type_id' => ClientChat::SOURCE_TYPE_CLIENT]);
        $queryChats->andWhere([
            'between',
            'cch_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->createTimeEnd))
        ]);
        $chatsData = $queryChats->createCommand()->queryAll();
        //var_dump($chatsData); die();

        $queryFirstAgentMsg = ClientChatMessage::find();
        $queryFirstAgentMsg->select([
            'each_item.ccm_cch_id',
            'first_msg_date',
            //'ccm_client_id',
            'ccm_user_id',
            //new Expression("ccm_body->>'msg' as msg")
        ]);
        $queryFirstAgentMsg->innerJoin('(SELECT ccm_cch_id, MIN(ccm_sent_dt) AS first_msg_date FROM client_chat_message 
                       WHERE ccm_client_id IS NOT NULL AND ccm_user_id IS NOT NULL GROUP BY ccm_cch_id) AS each_item', 'each_item.first_msg_date = client_chat_message.ccm_sent_dt AND each_item.ccm_cch_id = client_chat_message.ccm_cch_id');

        $firstChatAgentMessage = $queryFirstAgentMsg->createCommand()->queryAll();
        //var_dump($firstChatAgentMessage); die();

        $queryFirstMessagesOfChats = ClientChatMessage::find();
        $queryFirstMessagesOfChats->select([
            'each_item.ccm_cch_id',
            'first_msg_date',
            //'ccm_client_id',
            'ccm_user_id',
            //new Expression("ccm_body->>'msg' as msg")
        ]);
        $queryFirstMessagesOfChats->innerJoin('(SELECT ccm_cch_id, MIN(ccm_sent_dt) AS first_msg_date FROM client_chat_message 
                       GROUP BY ccm_cch_id) AS each_item', 'each_item.first_msg_date = client_chat_message.ccm_sent_dt AND each_item.ccm_cch_id = client_chat_message.ccm_cch_id');

        $firstMessagesOfChats = $queryFirstMessagesOfChats->createCommand()->queryAll();
        //var_dump( $firstMessagesOfChats); die();


        foreach ($chatsData as $key => $chat) {
            $chatsData[$key]['agent_frt'] = 0;
            $chatsData[$key]['chat_duration'] = 0;

            foreach ($firstChatAgentMessage as $message) {
                if ($chat['cch_id'] == $message['ccm_cch_id']){
                    $chatsData[$key]['agent_frt'] = strtotime($message['first_msg_date']) - strtotime($chat['cch_created_dt']);
                }
            }

            foreach ($firstMessagesOfChats as $messageOfChat) {
                if ($chat['cch_id'] == $messageOfChat['ccm_cch_id'] && $chat['cch_status_id'] == ClientChat::STATUS_CLOSED ){
                    $chatsData[$key]['chat_duration'] = strtotime($chat['cch_updated_dt']) - strtotime($messageOfChat['first_msg_date']);
                }
            }
        }

        //var_dump($chatsData); die();

        foreach ($allData as $key => $finalData){
            $allData[$key]['sumFrtOfChatsInGroup'] = 0;
            $allData[$key]['sumClientChatDurationInGroup'] = 0;
            $allData[$key]['sumAgentChatDurationInGroup'] = 0;
            $agentsInGroup = explode(',', $finalData['agentsInGroup']);
            for ($i = 0; $i < count($agentsInGroup); $i++){
                foreach ($chatsData as $chat){
                    if (!strcmp($finalData['date'], $chat['date'])  && $agentsInGroup[$i] == $chat['cch_owner_user_id'] && $chat['cch_source_type_id'] == ClientChat::SOURCE_TYPE_CLIENT ){
                        $allData[$key]['sumFrtOfChatsInGroup'] += $chat['agent_frt'];
                    }
                    if (!strcmp($finalData['date'], $chat['date'])  && $agentsInGroup[$i] == $chat['cch_owner_user_id'] && $chat['cch_source_type_id'] == ClientChat::SOURCE_TYPE_CLIENT ){
                        $allData[$key]['sumClientChatDurationInGroup'] += $chat['chat_duration'];
                    }
                    if (!strcmp($finalData['date'], $chat['date'])  && $agentsInGroup[$i] == $chat['cch_owner_user_id'] && $chat['cch_source_type_id'] == ClientChat::SOURCE_TYPE_AGENT ){
                        $allData[$key]['sumAgentChatDurationInGroup'] += $chat['chat_duration'];
                    }
                }
            }
        }

        //var_dump($allData); die();



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