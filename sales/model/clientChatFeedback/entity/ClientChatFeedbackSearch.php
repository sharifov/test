<?php

namespace sales\model\clientChatFeedback\entity;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChatFeedback\entity\ClientChatFeedback;

/**
 * clientChatFeedbackSearch represents the model behind the search form of `sales\model\clientChatFeedback\entity\ClientChatFeedback`.
 */
class ClientChatFeedbackSearch extends ClientChatFeedback
{
    public function rules(): array
    {
        return [
            [['ccf_id', 'ccf_client_chat_id', 'ccf_user_id', 'ccf_client_id', 'ccf_rating'], 'integer'],
            [['ccf_message', 'ccf_created_dt', 'ccf_updated_dt'], 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = ClientChatFeedback::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['ccf_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccf_id' => $this->ccf_id,
            'ccf_client_chat_id' => $this->ccf_client_chat_id,
            'ccf_user_id' => $this->ccf_user_id,
            'ccf_client_id' => $this->ccf_client_id,
            'ccf_rating' => $this->ccf_rating,
        ]);

        $query->andFilterWhere(['like', 'ccf_message', $this->ccf_message]);

        if ($this->ccf_created_dt){
            $query->andFilterWhere(['>=', 'ccf_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ccf_created_dt))])
                ->andFilterWhere(['<=', 'ccf_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ccf_created_dt) + 3600 * 24)]);
        }
        if ($this->ccf_updated_dt){
            $query->andFilterWhere(['>=', 'ccf_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ccf_updated_dt))])
                ->andFilterWhere(['<=', 'ccf_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ccf_updated_dt) + 3600 * 24)]);
        }

        return $dataProvider;
    }
}
