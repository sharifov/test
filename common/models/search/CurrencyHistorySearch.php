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
            [['ch_code', 'ch_created_date', 'ch_main_created_dt', 'ch_main_updated_dt', 'ch_main_synch_dt'], 'safe'],
            [['ch_base_rate', 'ch_app_rate', 'ch_app_percent'], 'number'],
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
					'ch_created_date' => SORT_DESC,
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
            'ch_base_rate' => $this->ch_base_rate,
            'ch_app_rate' => $this->ch_app_rate,
            'ch_app_percent' => $this->ch_app_percent,
            'ch_created_date' => $this->ch_created_date,
            'ch_main_created_dt' => $this->ch_main_created_dt,
            'ch_main_updated_dt' => $this->ch_main_updated_dt,
            'ch_main_synch_dt' => $this->ch_main_synch_dt,
        ]);

        $query->andFilterWhere(['like', 'ch_code', $this->ch_code]);

        return $dataProvider;
    }
}
