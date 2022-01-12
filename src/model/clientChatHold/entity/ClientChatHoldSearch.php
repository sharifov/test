<?php

namespace src\model\clientChatHold\entity;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\clientChatHold\entity\ClientChatHold;

/**
 * ClientChatHoldSearch represents the model behind the search form of `src\model\clientChatHold\entity\ClientChatHold`.
 */
class ClientChatHoldSearch extends ClientChatHold
{
    public function rules(): array
    {
        return [
            [['cchd_id', 'cchd_cch_id', 'cchd_cch_status_log_id'], 'integer'],
            [['cchd_start_dt', 'cchd_deadline_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = ClientChatHold::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cchd_deadline_dt' => SORT_DESC]],
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

        if ($this->cchd_deadline_dt) {
            $query->andFilterWhere(['>=', 'cchd_deadline_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cchd_deadline_dt))])
                ->andFilterWhere(['<=', 'cchd_deadline_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cchd_deadline_dt) + 3600 * 24)]);
        }
        if ($this->cchd_start_dt) {
            $query->andFilterWhere(['>=', 'cchd_start_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cchd_start_dt))])
                ->andFilterWhere(['<=', 'cchd_start_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cchd_start_dt) + 3600 * 24)]);
        }

        return $dataProvider;
    }
}
