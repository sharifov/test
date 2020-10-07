<?php

namespace sales\model\clientChatHold\entity;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\clientChatHold\entity\ClientChatHold;

/**
 * ClientChatHoldSearch represents the model behind the search form of `sales\model\clientChatHold\entity\ClientChatHold`.
 */
class ClientChatHoldSearch extends ClientChatHold
{
    public function rules(): array
    {
        return [
            [['cchd_id', 'cchd_cch_id', 'cchd_cch_status_log_id'], 'integer'],
            [['cchd_deadline_dt'], 'safe'],
        ];
    }

    /**
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ClientChatHold::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cchd_deadline_dt' => SORT_DESC]],
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
            'cchd_id' => $this->cchd_id,
            'cchd_cch_id' => $this->cchd_cch_id,
            'cchd_cch_status_log_id' => $this->cchd_cch_status_log_id,
        ]);

        if ($this->cchd_deadline_dt){
            $query->andFilterWhere(['>=', 'cchd_deadline_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cchd_deadline_dt))])
                ->andFilterWhere(['<=', 'cchd_deadline_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cchd_deadline_dt) + 3600 * 24)]);
        }

        return $dataProvider;
    }
}
