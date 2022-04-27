<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleType\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;

/**
 * ShiftScheduleTypeSearch represents the model behind the search form of `modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType`.
 */
class ShiftScheduleTypeSearch extends ShiftScheduleType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sst_id', 'sst_enabled', 'sst_readonly', 'sst_work_time', 'sst_sort_order', 'sst_updated_user_id'], 'integer'],
            [['sst_key', 'sst_name', 'sst_title', 'sst_color', 'sst_icon_class', 'sst_css_class', 'sst_params_json', 'sst_updated_dt'], 'safe'],
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
        $query = ShiftScheduleType::find();

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
            'sst_id' => $this->sst_id,
            'sst_enabled' => $this->sst_enabled,
            'sst_readonly' => $this->sst_readonly,
            'sst_work_time' => $this->sst_work_time,
            'sst_sort_order' => $this->sst_sort_order,
            'sst_updated_dt' => $this->sst_updated_dt,
            'sst_updated_user_id' => $this->sst_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'sst_key', $this->sst_key])
            ->andFilterWhere(['like', 'sst_name', $this->sst_name])
            ->andFilterWhere(['like', 'sst_title', $this->sst_title])
            ->andFilterWhere(['like', 'sst_color', $this->sst_color])
            ->andFilterWhere(['like', 'sst_icon_class', $this->sst_icon_class])
            ->andFilterWhere(['like', 'sst_css_class', $this->sst_css_class])
            ->andFilterWhere(['like', 'sst_params_json', $this->sst_params_json]);

        return $dataProvider;
    }
}
