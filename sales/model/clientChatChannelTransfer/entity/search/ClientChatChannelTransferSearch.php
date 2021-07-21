<?php

namespace sales\model\clientChatChannelTransfer\entity\search;

use common\models\Employee;
use common\models\Project;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\data\ActiveDataProvider;
use sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatChannelTransferSearch
 * @package sales\model\clientChatChannelTransfer\entity\search
 *
 * @property int|null $channelProjectId
 */
class ClientChatChannelTransferSearch extends ClientChatChannelTransfer
{

    public $channelProjectId;

    public function rules(): array
    {
        return [
            ['cctr_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['cctr_created_user_id', 'integer'],

            [['channelProjectId'], 'integer'],

            ['cctr_from_ccc_id', 'integer'],

            ['cctr_to_ccc_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cctr_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cctr_created_dt', $this->cctr_created_dt, $user->timezone);
        }

        if ($this->channelProjectId) {
            $query->innerJoin(ClientChatChannel::tableName() . ' as fromChannel', 'cctr_from_ccc_id = fromChannel.ccc_id and fromChannel.ccc_project_id = :fromProjectId', [
                'fromProjectId' => $this->channelProjectId
            ]);
            $query->innerJoin(ClientChatChannel::tableName() . ' as toChannel', 'cctr_to_ccc_id = toChannel.ccc_id and toChannel.ccc_project_id = :toProjectId', [
                'toProjectId' => $this->channelProjectId
            ]);
        }

        $query->andFilterWhere([
            'cctr_from_ccc_id' => $this->cctr_from_ccc_id,
            'cctr_to_ccc_id' => $this->cctr_to_ccc_id,
            'cctr_created_user_id' => $this->cctr_created_user_id,
        ]);

        return $dataProvider;
    }

    public function attributeLabels(): array
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
           'channelProjectId' => 'Channel Project',
        ]);
    }
}
