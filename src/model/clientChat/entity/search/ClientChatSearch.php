<?php

namespace src\model\clientChat\entity\search;

use common\models\Client;
use common\models\ClientEmail;
use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\UserGroupAssign;
use src\access\EmployeeGroupAccess;
use src\helpers\query\QueryHelper;
use src\model\clientChat\dashboard\FilterForm;
use src\model\clientChat\dashboard\GroupFilter;
use src\model\clientChat\dashboard\ReadUnreadFilter;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatLastMessage\entity\ClientChatLastMessage;
use src\model\clientChatMessage\entity\ClientChatMessage;
use src\model\clientChatUnread\entity\ClientChatUnread;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class ClientChatSearch
 *
 * @property int|null $lead_id
 * @property int|null $case_id
 * @property int      $pageSize
 */
class ClientChatSearch extends ClientChat
{
    public $lead_id;
    public $case_id;

    public string $timeRange;
    public string $timeStart;
    public string $timeEnd;

    public const DEFAULT_INTERVAL_BETWEEN_DAYS = '-6 days';

    private int $pageSize;
    private const DEFAULT_PAGE_SIZE = 10;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->timeRange = date('Y-m-d 00:00:00', strtotime('-29 days')) . ' - ' . date('Y-m-d 23:59:59');
        $range = explode(' - ', $this->timeRange);
        $this->timeStart = $range[0];
        $this->timeEnd = $range[1];
        $this->pageSize = \Yii::$app->params['settings']['client_chat_page_size'] ?? self::DEFAULT_PAGE_SIZE;
    }

    public function rules(): array
    {
        return [
            [['timeRange', 'timeStart', 'timeEnd'], 'string'],

            ['cch_ccr_id', 'integer'],

            ['cch_channel_id', 'integer'],

            ['cch_client_id', 'integer'],

            ['cch_created_dt', 'safe'],

            ['cch_created_user_id', 'integer'],

            ['cch_dep_id', 'integer'],

            ['cch_description', 'safe'],

            ['cch_id', 'integer'],

            ['cch_ip', 'safe'],

            ['cch_language_id', 'safe'],

            ['cch_note', 'safe'],

            ['cch_owner_user_id', 'integer'],

            ['cch_project_id', 'integer'],

            ['cch_rid', 'safe'],

            ['cch_status_id', 'integer'],
            ['cch_source_type_id', 'integer'],

            ['cch_title', 'safe'],

            ['cch_ua', 'integer'],

            ['cch_updated_dt', 'safe'],

            ['cch_updated_user_id', 'integer'],

            ['lead_id', 'integer'],

            ['case_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $query->joinWith(['leads', 'cases']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cch_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->timeStart && $this->timeEnd) {
            $query->andFilterWhere(['>=', 'cch_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->timeStart))])
                ->andFilterWhere(['<=', 'cch_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->timeEnd))]);
        }

        if (!empty($this->cch_created_dt)) {
            $query->andFilterWhere(['DATE(cch_created_dt)' => date('Y-m-d', strtotime($this->cch_created_dt))]);
        }

        $query->andFilterWhere([
            'cch_id' => $this->cch_id,
            'cch_ccr_id' => $this->cch_ccr_id,
            'cch_project_id' => $this->cch_project_id,
            'cch_dep_id' => $this->cch_dep_id,
            'cch_channel_id' => $this->cch_channel_id,
            'cch_client_id' => $this->cch_client_id,
            'cch_owner_user_id' => $this->cch_owner_user_id,
            'cch_status_id' => $this->cch_status_id,
            'cch_source_type_id' => $this->cch_source_type_id,
            'cch_ua' => $this->cch_ua,
            //'date_format(cch_created_dt, "%Y-%m-%d")' => $this->cch_created_dt,
            'date_format(cch_updated_dt, "%Y-%m-%d")' => $this->cch_updated_dt,
            'cch_created_user_id' => $this->cch_created_user_id,
            'cch_updated_user_id' => $this->cch_updated_user_id,
            'ccl_lead_id' => $this->lead_id,
            'cccs_case_id' => $this->case_id,
        ]);

        $query->andFilterWhere(['like', 'cch_rid', $this->cch_rid])
            ->andFilterWhere(['like', 'cch_title', $this->cch_title])
            ->andFilterWhere(['like', 'cch_description', $this->cch_description])
            ->andFilterWhere(['like', 'cch_note', $this->cch_note])
            ->andFilterWhere(['like', 'cch_ip', $this->cch_ip])
            ->andFilterWhere(['like', 'cch_language_id', $this->cch_language_id]);

        return $dataProvider;
    }

    public function report($params)
    {
        $this->load($params);
        if ($this->timeRange) {
            $range = explode(' - ', $this->timeRange);
            $this->timeStart = $range[0];
            $this->timeEnd = $range[1];
        }

        $query = static::find()->joinWith('cchOwnerUser');
        $query->select([
            'username',
            'cch_owner_user_id AS owner',
            'SUM(IF(cch_status_id = ' . ClientChat::STATUS_PENDING . ', 1, 0)) AS generated',
            'SUM(IF(cch_status_id = ' . ClientChat::STATUS_CLOSED . ', 1, 0)) AS closed',
        ]);

        $query->where('cch_owner_user_id IS NOT NULL');
        $query->andWhere([
            'between',
            'cch_created_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->timeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->timeEnd)),
        ]);

        $query->groupBy(['owner']);

        $queryMessages = ClientChatMessage::find();
        $queryMessages->select([
            'ccm_user_id AS user',
            'SUM(CASE WHEN ccm_user_id IS NOT NULL THEN 1 ELSE 0 END) AS messages',
        ]);

        $queryMessages->andWhere([
            'between',
            'ccm_sent_dt',
            Employee::convertTimeFromUserDtToUTC(strtotime($this->timeStart)),
            Employee::convertTimeFromUserDtToUTC(strtotime($this->timeEnd)),
        ]);

        $queryMessages->groupBy(['user']);

        $clientChat = $query->createCommand()->queryAll();
        $clientChatMsg = $queryMessages->createCommand()->queryAll();

        foreach ($clientChat as $key => $item) {
            $clientChat[$key]['msg'] = '';
            foreach ($clientChatMsg as $msg) {
                if ($item['owner'] == $msg['user']) {
                    $clientChat[$key]['msg'] = (string) $msg['messages'];
                }
            }
        }

        $paramsData = [
            'allModels' => $clientChat,
            'sort' => [
                //'defaultOrder' => ['username' => SORT_ASC],
                'attributes' => [
                    'username',
                    'generated',
                    'closed',
                    'msg',
                ],
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ];

        return new ArrayDataProvider($paramsData);
    }

    public function searchRealtimeClientChatActivity($params)
    {
        $queryChats = static::find()->joinWith(['cchOwnerUser', 'cchClient as c', 'cchProject as p', 'cchDep as d', 'cchChannel as ch']);
        $queryChats->select([
            'cch_id',
            'cch_client_id',
            'cch_created_dt',
            'cch_owner_user_id',
            'username',
            'COALESCE(MD5(email), " ") as email',
            'CONCAT(COALESCE(c.first_name, " "), " ", COALESCE(c.last_name, " ") ) as clientName',
            'p.name as project',
            'd.dep_name as department',
            'ch.ccc_name as channel',
        ]);
        $queryChats->where(['cch_status_id' => ClientChat::STATUS_PENDING]);
        if ($params['formDate']) {
            $queryChats->where(['between', 'cch_created_dt', $params['formDate'], date('Y-m-d H:i:s')]);
        } else {
            $queryChats->limit(10);
        }
        $queryChats->orderBy('cch_created_dt DESC');
        $chatCmd = $queryChats->createCommand();
        $clientChats = $chatCmd->queryAll();

        $queryMessages = ClientChatMessage::find();
        $queryMessages->select([
            'ccm_cch_id AS chatId',
            'SUM(CASE WHEN ccm_user_id IS NOT NULL THEN 1 ELSE 0 END) AS outMsg',
            'SUM(CASE WHEN ccm_user_id IS NULL THEN 1 ELSE 0 END) AS inMsg',
        ]);

        $queryMessages->groupBy('ccm_cch_id');
        $msgCmd = $queryMessages->createCommand();
        $chatMessages = $msgCmd->queryAll();

        $queryLastClientMsg = ClientChatMessage::find();
        $queryLastClientMsg->select([
            'each_item.ccm_cch_id',
            'latest_data',
            'ccm_client_id',
            'ccm_user_id',
            new Expression("ccm_body->>'msg' as msg"),
        ]);
        $queryLastClientMsg->innerJoin('(SELECT ccm_cch_id, MAX(ccm_sent_dt) AS latest_data FROM client_chat_message AS st 
                       where ccm_client_id IS NOT NULL and ccm_user_id IS NULL GROUP BY ccm_cch_id) AS each_item', 'each_item.latest_data = client_chat_message.ccm_sent_dt AND each_item.ccm_cch_id = client_chat_message.ccm_cch_id');

        $queryLastAgentMsg = ClientChatMessage::find();
        $queryLastAgentMsg->select([
            'each_item.ccm_cch_id',
            'latest_data',
            'ccm_client_id',
            'ccm_user_id',
            new Expression("ccm_body->>'msg' as msg"),
        ]);
        $queryLastAgentMsg->innerJoin('(SELECT ccm_cch_id, MAX(ccm_sent_dt) AS latest_data FROM client_chat_message AS st 
                       where ccm_client_id IS NOT NULL and ccm_user_id IS NOT NULL GROUP BY ccm_cch_id) AS each_item', 'each_item.latest_data = client_chat_message.ccm_sent_dt AND each_item.ccm_cch_id = client_chat_message.ccm_cch_id');

        $unionLastMsg = $queryLastClientMsg->union($queryLastAgentMsg);

        $lastMsgCmd = $unionLastMsg->createCommand();
        $latestMsgs = $lastMsgCmd->queryAll();


        foreach ($clientChats as $key => $chat) {
            $clientChats[$key]['outMsg'] = 0;
            $clientChats[$key]['inMsg'] = 0;

            $clientChats[$key]['client_msg_date'] = '';
            $clientChats[$key]['client_msg_date'] = '';
            $clientChats[$key]['latest_client_msg'] = '';
            $clientChats[$key]['agent_msg_period'] = '';
            $clientChats[$key]['agent_msg_date'] = '';
            $clientChats[$key]['latest_agent_msg'] = '';

            foreach ($chatMessages as $message) {
                if ($chat['cch_id'] == $message['chatId']) {
                    $clientChats[$key]['outMsg'] = $message['outMsg'];
                    $clientChats[$key]['inMsg'] = $message['inMsg'];
                }
            }

            foreach ($latestMsgs as $msg) {
                if ($chat['cch_id'] == $msg['ccm_cch_id']) {
                    if (!is_null($msg['ccm_client_id']) && is_null($msg['ccm_user_id'])) {
                        $clientChats[$key]['client_msg_period'] = \Yii::$app->formatter->asRelativeTime(strtotime($msg['latest_data']));
                        $clientChats[$key]['client_msg_date'] = \Yii::$app->formatter->asDatetime(strtotime($msg['latest_data']), 'php: Y-m-d H:i:s');
                        $clientChats[$key]['latest_client_msg'] = $msg['msg'];
                    }

                    if (!is_null($msg['ccm_client_id']) && !is_null($msg['ccm_user_id'])) {
                        $clientChats[$key]['agent_msg_period'] = \Yii::$app->formatter->asRelativeTime(strtotime($msg['latest_data']));
                        $clientChats[$key]['agent_msg_date'] = \Yii::$app->formatter->asDatetime(strtotime($msg['latest_data']), 'php: Y-m-d H:i:s');
                        $clientChats[$key]['latest_agent_msg'] = $msg['msg'];
                    }
                }
            }
        }

        return $clientChats;
    }

    public function getListOfChats(Employee $user, array $channelsIds, FilterForm $filter, int $page, bool $increaseLimit): ArrayDataProvider
    {
        if (GroupFilter::isNothing($filter->group)) {
            return new ArrayDataProvider([
                'allModels' => [],
            ]);
        }

        $query = $this->listOfChatsQuery($filter, $user, $channelsIds);
        $totalCount = $query->count();


        $limit = $this->pageSize;
        $offset = $this->pageSize * $page;
        if ($increaseLimit) {
            $limit *= ($page);
            $offset = 0;
        }
//        $limit = $increaseLiVmit ? $this->pageSize * (($page === 0) ? $page+1 : $page)  : $this->pageSize;

        $query->limit($limit)->offset($offset);
        $data = $query->asArray()->all();
//        $data = ArrayHelper::index($data, 'cch_id');
//        $chatIds = ArrayHelper::map($data, 'cch_id', 'cch_id');
//        $lastMessages = ClientChatMessage::find()->select(['ccm_sent_dt' => 'MAX(ccm_sent_dt)', 'ccm_cch_id'])->byChatIds($chatIds)->groupBy(['ccm_cch_id'])->asArray()->all();
//        $lastMessages = ArrayHelper::index($lastMessages, 'ccm_cch_id');

//        foreach ($data as $key => $item) {
//            if (isset($lastMessages[$key])) {
//                $data[$key]['ccm_sent_dt'] = $lastMessages[$key]['ccm_sent_dt'] ? strtotime($lastMessages[$key]['ccm_sent_dt']) : 0;
//            } else {
//                $data[$key]['ccm_sent_dt'] = 0;
//            }
//            $data[$key]['count_unread_messages'] = (int) $item['ccu_count'];
//        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => ['pageSize' => $this->pageSize],
            'totalCount' => $totalCount
            //            'sort' => [
            //                'defaultOrder' => [
            //                    'count_unread_messages' => SORT_DESC,
            //                    'ccm_sent_dt' => SORT_DESC,
            //                    'cch_created_dt' => SORT_DESC,
            //                ],
            //                'attributes' => [
            //                    'count_unread_messages',
            //                    'ccm_sent_dt',
            //                    'cch_created_dt',
            //                ],
            //            ],
        ]);

        return $dataProvider;
    }

    public function getListOfChatsIds(Employee $user, array $channelsIds, FilterForm $filter): array
    {
        $query = $this->listOfChatsQuery($filter, $user, $channelsIds);
        $query->select(['client_chat.cch_id', 'last_message.cclm_dt as last_message_date']);
        return ArrayHelper::map($query->asArray()->all(), 'cch_id', 'cch_id');
    }

    private function listOfChatsQuery(FilterForm $filter, Employee $user, array $channelsIds)
    {
        $query = ClientChat::find()->select([
            ClientChat::tableName() . '.*',
            new Expression('trim(concat_ws(\' \', client.first_name, client.last_name)) as client_full_name'),
            'client_id' => 'client.id',
            'dep_name',
            'project.name as project_name',
            'ccc_name',
            'ccu_count',
            'ccu_updated_dt',
            'owner_username' => 'owner.username',
            'last_message.cclm_message as last_message',
            'last_message.cclm_type_id as last_message_type_id',
            'last_message.cclm_dt as last_message_date',
            'last_message.cclm_platform_id as last_message_platform'
        ]);

        if (ClientChat::isTabAll($filter->showFilter)) {
        } elseif (ClientChat::isTabActive($filter->showFilter)) {
            $query->notInClosedGroup();
        } elseif (ClientChat::isTabClosed($filter->showFilter)) {
            $query->archive();
        } elseif (ClientChat::isTabUnassigned($filter->showFilter)) {
            $query->byOwner(null);
            $query->byStatus(ClientChat::STATUS_PENDING);
        }

        if (GroupFilter::isMy($filter->group)) {
            $query->byOwner($user->id);
            $query->orderBy([
                '(cch_status_id = ' . ClientChat::STATUS_ARCHIVE .
                    ' OR cch_status_id = ' . ClientChat::STATUS_CLOSED . ')' => SORT_ASC,
                'cch_updated_dt' => SORT_DESC,
            ]);
        } elseif (GroupFilter::isOther($filter->group)) {
            $query->andWhere(['OR',
                ['!=', 'cch_owner_user_id', $user->id],
                ['IS', 'cch_owner_user_id', null]
            ]);
//            $query->orderBy([
//                '(cch_status_id = ' . ClientChat::STATUS_ARCHIVE .
//                    ' OR cch_status_id = ' . ClientChat::STATUS_CLOSED . ')' => SORT_ASC,
//                '(cch_owner_user_id IS NULL)' => SORT_DESC,
//                'cch_created_dt' => SORT_ASC,
//            ]);
        } elseif (GroupFilter::isFreeToTake($filter->group)) {
            $query->freeToTake($user->id);
//            $query->orderBy([
////                '(cch_status_id = ' . ClientChat::STATUS_TRANSFER . ')' => SORT_DESC,
//                'cch_created_dt' => SORT_ASC,
//            ]);
        } elseif (GroupFilter::isTeamChats($filter->group)) {
            $commonUsers = EmployeeGroupAccess::getUsersIdsInCommonGroups($user->id);
            if (isset($commonUsers[$user->id])) {
                unset($commonUsers[$user->id]);
            }
            $query->andWhere(['cch_owner_user_id' => array_keys($commonUsers)]);
//            $query->orderBy([
//                '(cch_status_id = ' . ClientChat::STATUS_ARCHIVE .
//                ' OR cch_status_id = ' . ClientChat::STATUS_CLOSED . ')' => SORT_ASC,
//                'last_message_date' => SORT_DESC,
//            ]);
        } else {
            $query->byOwner($user->id);
//            $query->orderBy(['cch_updated_dt' => SORT_DESC]);
        }

        $query->orderBy([
            'cch_status_id not in (' . implode(',', ClientChat::CLOSED_STATUS_GROUP) . ')' => SORT_ASC,
            'if (ccu_count > 0, 1, 0)' => SORT_DESC
        ]);

        if ($filter->getOrderBy()) {
            $query->addOrderBy($filter->getOrderBy());
        }

        if ($filter->channelId) {
            $query->byChannelIds($filter->channelId);
        } else {
            if (!GroupFilter::isMy($filter->group)) {
                $query->byChannelIds($channelsIds);
            }
        }

        if ($filter->dep) {
            $query->byDepartment($filter->dep);
        }

        if ($filter->project) {
//            $query->byProject($filter->project);
            $query->andWhere(['cch_project_id' => $filter->project]);
        }

        if ($filter->userId) {
            $query->andWhere(['cch_owner_user_id' => $filter->userId]);
        }

        if ($filter->createdDate) {
            \src\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cch_created_dt', $filter->createdDate, $user->timezone);
        }

        if ($filter->fromDate && $filter->toDate) {
            $fromDate = date('Y-m-d', strtotime($filter->fromDate));
            $toDate = date('Y-m-d', strtotime($filter->toDate));
            $query->andWhere(['BETWEEN', 'DATE(cch_created_dt)', $fromDate, $toDate]);
        }

        if ($filter->status) {
            $query->byStatuses($filter->status);
        }

        if ($filter->clientName) {
            $query->andWhere(['OR',
                ['like', 'client.first_name', $filter->clientName],
                ['like', 'client.last_name', $filter->clientName]
            ]);
        }

        if ($filter->chatId) {
            $query->andWhere(['cch_id' => $filter->chatId]);
        }

        if ($filter->clientEmail) {
            $subQuery = ClientEmail::find()->select(['DISTINCT(client_id)'])->where(['=', 'email', $filter->clientEmail]);
            $query->andWhere(['IN', 'cch_client_id', $subQuery]);
        }

        if (GroupFilter::isMy($filter->group)) {
            if (ReadUnreadFilter::isUnread($filter->readUnread)) {
                $query->andWhere(['>', 'ccu_count', 0]);
            }
        }

        $query->join('JOIN', ['client' => Client::tableName()], 'cch_client_id = client.id');
        $query->join('JOIN', [ClientChatChannel::tableName()], 'cch_channel_id = ccc_id');
        $query->leftJoin(Department::tableName(), 'cch_dep_id = dep_id');
        $query->leftJoin(['project' => Project::tableName()], 'cch_project_id = project.id');
        $query->leftJoin(ClientChatUnread::tableName(), 'ccu_cc_id = cch_id');
        $query->leftJoin(['owner' => Employee::tableName()], 'cch_owner_user_id = owner.id');
        $query->leftJoin(['last_message' => ClientChatLastMessage::tableName()], 'cch_id = last_message.cclm_cch_id');

        $query->limit(1000);

        if ($filter->userGroups) {
            $query->innerJoin(['ownerUserGroupAssign' => UserGroupAssign::tableName()], new Expression('ownerUserGroupAssign.ugs_user_id = owner.id'));
            $query->andWhere(['ownerUserGroupAssign.ugs_group_id' => $filter->userGroups]);
        }

        return $query;
    }

    public function searchChatGraph($params, $user_id): array
    {
        $query = new Query();
        $query->addSelect(['DATE(cch_created_dt) as createdDate,
               SUM(IF(cch_status_id = ' . ClientChat::STATUS_NEW . ', 1, 0)) AS chatNew,
               SUM(IF(cch_status_id = ' . ClientChat::STATUS_PENDING . ', 1, 0)) AS chatPending,
               SUM(IF(cch_status_id = ' . ClientChat::STATUS_IN_PROGRESS . ', 1, 0)) AS chatProgress,
               SUM(IF(cch_status_id = ' . ClientChat::STATUS_TRANSFER . ', 1, 0)) AS chatTransfer,             
               SUM(IF(cch_status_id = ' . ClientChat::STATUS_HOLD . ', 1, 0)) AS chatHold,             
               SUM(IF(cch_status_id = ' . ClientChat::STATUS_IDLE . ', 1, 0)) AS chatIdle,             
               SUM(IF(cch_status_id = ' . ClientChat::STATUS_CLOSED . ', 1, 0)) AS chatClosed             
        ']);

        $query->from(static::tableName());
        $query->where('cch_owner_user_id IS NOT NULL');
        $query->andWhere(['cch_owner_user_id' => $user_id]);
        if ($this->timeStart && $this->timeEnd) {
            $query->andWhere(['>=', 'cch_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->timeStart))]);
            $query->andWhere(['<=', 'cch_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->timeEnd))]);
        }

        $query->groupBy('createdDate');

        return $query->createCommand()->queryAll();
    }

    public function countFreeToTake(Employee $user, array $channelsIds, FilterForm $filter): int
    {
        $query = ClientChat::find()->freeToTake($user->id);

        if ($filter->channelId) {
            $query->byChannelIds($filter->channelId);
        } else {
            $query->byChannelIds($channelsIds);
        }
        if ($filter->project) {
//            $query->byProject($filter->project);
            $query->andWhere(['cch_project_id' => $filter->project]);
        }
        if ($filter->userId) {
            $query->andWhere(['cch_owner_user_id' => $filter->userId]);
        }
        if ($filter->status) {
            $query->byStatuses($filter->status);
        }
        if ($filter->clientName) {
            $query->join('JOIN', ['client' => Client::tableName()], 'cch_client_id = client.id');
            $query->andWhere(['OR',
                ['like', 'client.first_name', $filter->clientName],
                ['like', 'client.last_name', $filter->clientName]
            ]);
        }
        if ($filter->fromDate && $filter->toDate) {
            $fromDate = date('Y-m-d', strtotime($filter->fromDate));
            $toDate = date('Y-m-d', strtotime($filter->toDate));
            $query->andWhere(['BETWEEN', 'DATE(cch_created_dt)', $fromDate, $toDate]);
        }
        return (int) $query->count();
    }
}
