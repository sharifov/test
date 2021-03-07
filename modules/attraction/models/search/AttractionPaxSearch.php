<?php

namespace modules\attraction\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\attraction\models\AttractionPax;

/**
 * AttractionPaxSearch represents the model behind the search form of `modules\attraction\models\AttractionPax`.
 */
class AttractionPaxSearch extends AttractionPax
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atnp_id', 'atnp_atn_id', 'atnp_type_id', 'atnp_age'], 'integer'],
            [['atnp_first_name', 'atnp_last_name', 'atnp_dob'], 'safe'],
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
        $query = AttractionPax::find();

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
            'atnp_id' => $this->atnp_id,
            'atnp_atn_id' => $this->atnp_atn_id,
            'atnp_type_id' => $this->atnp_type_id,
            'atnp_age' => $this->atnp_age,
            'atnp_dob' => $this->atnp_dob,
        ]);

        $query->andFilterWhere(['like', 'atnp_first_name', $this->atnp_first_name])
            ->andFilterWhere(['like', 'atnp_last_name', $this->atnp_last_name]);

        return $dataProvider;
    }
}
