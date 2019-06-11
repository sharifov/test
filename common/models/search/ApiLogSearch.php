<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApiLog;

/**
 * ApiLogSearch represents the model behind the search form of `common\models\ApiLog`.
 */
class ApiLogSearch extends ApiLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['al_id', 'al_user_id', 'al_memory_usage', 'al_db_query_count'], 'integer'],
            [['al_request_data', 'al_request_dt', 'al_response_data', 'al_response_dt', 'al_ip_address', 'al_action', 'al_execution_time', 'al_db_execution_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ApiLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['al_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'al_id' => $this->al_id,
            'al_request_dt' => $this->al_request_dt,
            'al_response_dt' => $this->al_response_dt,
            'al_user_id' => $this->al_user_id,
            'al_memory_usage' => $this->al_memory_usage,
            'al_db_query_count' => $this->al_db_query_count,
        ]);

        $query->andFilterWhere(['like', 'al_request_data', $this->al_request_data])
            ->andFilterWhere(['like', 'al_response_data', $this->al_response_data])
            ->andFilterWhere(['like', 'al_action', $this->al_action])
            ->andFilterWhere(['like', 'al_ip_address', $this->al_ip_address]);

        return $dataProvider;
    }
}
