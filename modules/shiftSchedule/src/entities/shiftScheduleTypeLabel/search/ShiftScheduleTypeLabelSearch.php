<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;

/**
 * ShiftScheduleTypeLabelSearch represents the model behind the search form of `modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel`.
 */
class ShiftScheduleTypeLabelSearch extends ShiftScheduleTypeLabel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stl_key', 'stl_name', 'stl_color', 'stl_icon_class', 'stl_params_json', 'stl_updated_dt'], 'safe'],
            [['stl_enabled', 'stl_sort_order', 'stl_updated_user_id'], 'integer'],
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
        $query = ShiftScheduleTypeLabel::find();

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
            'stl_enabled' => $this->stl_enabled,
            'stl_sort_order' => $this->stl_sort_order,
            'stl_updated_dt' => $this->stl_updated_dt,
            'stl_updated_user_id' => $this->stl_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'stl_key', $this->stl_key])
            ->andFilterWhere(['like', 'stl_name', $this->stl_name])
            ->andFilterWhere(['like', 'stl_color', $this->stl_color])
            ->andFilterWhere(['like', 'stl_icon_class', $this->stl_icon_class])
            ->andFilterWhere(['like', 'stl_params_json', $this->stl_params_json]);

        return $dataProvider;
    }
}
