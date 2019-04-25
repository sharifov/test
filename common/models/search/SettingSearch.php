<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Setting;

/**
 * SettingSearch represents the model behind the search form of `common\models\Setting`.
 */
class SettingSearch extends Setting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['s_id', 's_updated_user_id'], 'integer'],
            [['s_key', 's_name', 's_type', 's_value', 's_updated_dt'], 'safe'],
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
        $query = Setting::find();

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
            's_id' => $this->s_id,
            's_updated_dt' => $this->s_updated_dt,
            's_updated_user_id' => $this->s_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 's_key', $this->s_key])
            ->andFilterWhere(['like', 's_name', $this->s_name])
            ->andFilterWhere(['like', 's_type', $this->s_type])
            ->andFilterWhere(['like', 's_value', $this->s_value]);

        return $dataProvider;
    }
}
