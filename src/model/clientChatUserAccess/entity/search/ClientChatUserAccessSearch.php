<?php

namespace src\model\clientChatUserAccess\entity\search;

use common\models\Client;
use common\models\Employee;
use common\models\Project;
use PHPUnit\Framework\Constraint\Count;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatUserAccess\entity\Scopes;
use src\model\userClientChatData\entity\UserClientChatData;
use yii\data\ActiveDataProvider;
use src\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\db\Expression;

class ClientChatUserAccessSearch extends ClientChatUserAccess
{
    public function rules(): array
    {
        return [
            ['ccua_id', 'integer'],

            ['ccua_cch_id', 'integer'],

            [['ccua_created_dt', 'ccua_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['ccua_status_id', 'integer'],

            ['ccua_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ccua_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccua_id' => $this->ccua_id,
            'ccua_cch_id' => $this->ccua_cch_id,
            'ccua_user_id' => $this->ccua_user_id,
            'ccua_status_id' => $this->ccua_status_id,
            'date_format(ccua_created_dt, "%Y-%m-%d")' => $this->ccua_created_dt,
            'date_format(ccua_updated_dt, "%Y-%m-%d")' => $this->ccua_updated_dt,
        ]);

        return $dataProvider;
    }

    public function searchPendingRequests(int $userId, int $offset = 0, int $limit = 20): array
    {
        $query = $this->widgetQuery();
        $query->byUserId($userId);
        $query->offset($offset)->limit($limit);
        return $query->asArray()->all();
    }

    public function getPendingRequestByChatUserAccessId(int $id): array
    {
        $query = $this->widgetQuery();
        $query->byId($id);
        return $query->asArray()->one() ?? [];
    }

    public function getTotalItems(int $userId)
    {
        $query = $this->widgetQuery();
        $query->byUserId($userId);
        return $query->count();
    }

    private function widgetQuery(): Scopes
    {
        $query = static::find()->select([
            'ccua_id',
            'ccua_cch_id',
            'ccua_user_id',
            'cch_status_id',
            'is_pending' => 'if (cch_status_id=:statusPending, 1, 0)',
            'is_transfer' => 'if (cch_status_id=:statusTransfer, 1, 0)',
            'is_idle' => 'if (cch_status_id=:statusIdle, 1, 0)',
            'cch_client_id',
            'full_name' => 'trim(concat_ws(\' \', client.first_name, client.last_name))',
            'cch_project_id',
            'project_name' => 'project.name',
            'ccc_name',
            'ccc_id',
            'ccua_created_dt',
            'cch_created_dt',
            'owner_nickname' => 'owner.nickname',
            'ccc_priority'
        ]);

        $query->innerJoin(ClientChat::tableName(), 'cch_id = ccua_cch_id');
        $query->innerJoin(Employee::tableName() . ' as user', 'user.id = ccua_user_id');
        $query->innerJoin(Client::tableName() . ' as client', 'client.id = cch_client_id');
        $query->leftJoin(Project::tableName() . ' as project', 'cch_project_id = project.id');
        $query->leftJoin(ClientChatChannel::tableName(), 'cch_channel_id = ccc_id');
        $query->leftJoin(Employee::tableName() . ' as owner', 'cch_owner_user_id = owner.id');
        $query->innerJoin(
            UserClientChatData::tableName(),
            'ccua_user_id = uccd_employee_id and uccd_chat_status_id = :chatStatusId'
        );

        $query->pending();
        $query->andWhere([
            'OR',
            ['cch_status_id' => ClientChat::STATUS_PENDING],
            ['cch_status_id' => ClientChat::STATUS_TRANSFER],
            ['cch_status_id' => ClientChat::STATUS_IDLE]]);
        $query->orderBy(['ccc_priority' => SORT_DESC, 'cch_created_dt' => SORT_ASC]);

        $query->groupBy([
            'ccua_id',
            'ccua_cch_id',
            'ccua_user_id',
            'cch_status_id',
            'is_transfer',
            'cch_client_id',
            'full_name',
            'cch_project_id',
            'project_name',
            'ccc_name',
            'ccua_created_dt',
            'cch_created_dt',
            'owner_nickname'
        ]);

        $query->params([
            ':statusTransfer' => ClientChat::STATUS_TRANSFER,
            ':statusPending' => ClientChat::STATUS_PENDING,
            ':statusIdle' => ClientChat::STATUS_IDLE,
            ':chatStatusId' => UserClientChatData::CHAT_STATUS_READY
        ]);

        return $query;
    }
}
