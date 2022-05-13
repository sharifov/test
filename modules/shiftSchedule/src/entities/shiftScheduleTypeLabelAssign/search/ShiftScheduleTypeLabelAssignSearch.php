<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;

/**
 * ShiftScheduleTypeLabelAssignSearch represents the model behind the search form of `modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign`.
 */
class ShiftScheduleTypeLabelAssignSearch extends ShiftScheduleTypeLabelAssign
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tla_stl_key', 'tla_created_dt'], 'safe'],
            [['tla_sst_id'], 'integer'],
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
        $query = ShiftScheduleTypeLabelAssign::find();

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
            'tla_sst_id' => $this->tla_sst_id,
            'tla_created_dt' => $this->tla_created_dt,
        ]);

        $query->andFilterWhere(['like', 'tla_stl_key', $this->tla_stl_key]);

        return $dataProvider;
    }
}
