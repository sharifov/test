<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequest\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;

/**
 * ShiftScheduleRequestSearch represents the model behind the search form of `modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest`.
 */
class ShiftScheduleRequestSearch extends ShiftScheduleRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['srh_id', 'srh_uss_id', 'srh_sst_id', 'srh_status_id', 'srh_created_user_id', 'srh_updated_user_id'], 'integer'],
            [['srh_description', 'srh_created_dt', 'srh_update_dt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
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
    public function search(array $params): ActiveDataProvider
    {
        $query = ShiftScheduleRequest::find();

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
            'srh_id' => $this->srh_id,
            'srh_uss_id' => $this->srh_uss_id,
            'srh_sst_id' => $this->srh_sst_id,
            'srh_status_id' => $this->srh_status_id,
            'srh_created_dt' => $this->srh_created_dt,
            'srh_update_dt' => $this->srh_update_dt,
            'srh_created_user_id' => $this->srh_created_user_id,
            'srh_updated_user_id' => $this->srh_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'srh_description', $this->srh_description]);

        return $dataProvider;
    }
}
