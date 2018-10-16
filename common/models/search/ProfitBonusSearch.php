<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProfitBonus;

/**
 * ProfitBonusSearch represents the model behind the search form of `common\models\ProfitBonus`.
 */
class ProfitBonusSearch extends ProfitBonus
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pb_id', 'pb_user_id', 'pb_min_profit', 'pb_bonus', 'pb_updated_user_id'], 'integer'],
            [['pb_updated_dt'], 'safe'],
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
        $query = ProfitBonus::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'pb_id' => $this->pb_id,
            'pb_user_id' => $this->pb_user_id,
            'pb_min_profit' => $this->pb_min_profit,
            'pb_bonus' => $this->pb_bonus,
            'pb_updated_dt' => $this->pb_updated_dt,
            'pb_updated_user_id' => $this->pb_updated_user_id,
        ]);

        return $dataProvider;
    }
}
