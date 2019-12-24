<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CurrencyHistory;

/**
 * CurrencyHistorySearch represents the model behind the search form of `common\models\CurrencyHistory`.
 */
class CurrencyHistorySearch extends CurrencyHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cur_his_code', 'cur_his_created', 'cur_his_main_created_dt', 'cur_his_main_updated_dt', 'cur_his_main_synch_dt'], 'safe'],
            [['cur_his_base_rate', 'cur_his_app_rate', 'cur_his_app_percent'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = CurrencyHistory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'cur_his_created' => SORT_DESC,
				]
			]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cur_his_base_rate' => $this->cur_his_base_rate,
            'cur_his_app_rate' => $this->cur_his_app_rate,
            'cur_his_app_percent' => $this->cur_his_app_percent,
            'cur_his_created' => $this->cur_his_created,
            'cur_his_main_created_dt' => $this->cur_his_main_created_dt,
            'cur_his_main_updated_dt' => $this->cur_his_main_updated_dt,
            'cur_his_main_synch_dt' => $this->cur_his_main_synch_dt,
        ]);

        $query->andFilterWhere(['like', 'cur_his_code', $this->cur_his_code]);

        return $dataProvider;
    }
}
