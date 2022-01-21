<?php

namespace src\model\userStatDay\entity\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use src\model\userStatDay\entity\UserStatDay;

/**
 * UserStatDaySearch represents the model behind the search form of `src\model\userStatDay\entity\UserStatDay`.
 */
class UserStatDaySearch extends UserStatDay
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usd_id', 'usd_key', 'usd_user_id', 'usd_day', 'usd_month', 'usd_year'], 'integer'],
            [['usd_value'], 'number'],
            [['usd_created_dt'], 'safe'],
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
        $query = UserStatDay::find();

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
            'usd_id' => $this->usd_id,
            'usd_key' => $this->usd_key,
            'usd_value' => $this->usd_value,
            'usd_user_id' => $this->usd_user_id,
            'usd_day' => $this->usd_day,
            'usd_month' => $this->usd_month,
            'usd_year' => $this->usd_year,
            'usd_created_dt' => $this->usd_created_dt,
        ]);

        return $dataProvider;
    }
}
