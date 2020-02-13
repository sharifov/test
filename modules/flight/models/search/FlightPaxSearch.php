<?php

namespace modules\flight\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\flight\models\FlightPax;

/**
 * FlightPaxSearch represents the model behind the search form of `modules\flight\models\FlightPax`.
 */
class FlightPaxSearch extends FlightPax
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fp_id', 'fp_flight_id', 'fp_pax_id'], 'integer'],
            [['fp_pax_type', 'fp_first_name', 'fp_last_name', 'fp_middle_name', 'fp_dob'], 'safe'],
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
        $query = FlightPax::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['fp_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
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
            'fp_id' => $this->fp_id,
            'fp_flight_id' => $this->fp_flight_id,
            'fp_pax_id' => $this->fp_pax_id,
            'fp_dob' => $this->fp_dob,
        ]);

        $query->andFilterWhere(['like', 'fp_pax_type', $this->fp_pax_type])
            ->andFilterWhere(['like', 'fp_first_name', $this->fp_first_name])
            ->andFilterWhere(['like', 'fp_last_name', $this->fp_last_name])
            ->andFilterWhere(['like', 'fp_middle_name', $this->fp_middle_name]);

        return $dataProvider;
    }
}
