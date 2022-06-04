<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\ShiftScheduleRequestHistory;

/**
 * ShiftScheduleRequestHistorySearch represents the model behind the search form of `modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\ShiftScheduleRequestHistory`.
 */
class ShiftScheduleRequestHistorySearch extends ShiftScheduleRequestHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ssrh_id', 'ssrh_ssr_id', 'ssrh_from_status_id', 'ssrh_to_status_id', 'ssrh_created_user_id', 'ssrh_updated_user_id'], 'integer'],
            [['ssrh_from_description', 'ssrh_to_description', 'ssrh_created_dt', 'ssrh_updated_dt'], 'safe'],
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
        $query = ShiftScheduleRequestHistory::find();

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
            'ssrh_id' => $this->ssrh_id,
            'ssrh_ssr_id' => $this->ssrh_ssr_id,
            'ssrh_from_status_id' => $this->ssrh_from_status_id,
            'ssrh_to_status_id' => $this->ssrh_to_status_id,
            'ssrh_created_dt' => $this->ssrh_created_dt,
            'ssrh_updated_dt' => $this->ssrh_updated_dt,
            'ssrh_created_user_id' => $this->ssrh_created_user_id,
            'ssrh_updated_user_id' => $this->ssrh_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ssrh_from_description', $this->ssrh_from_description])
            ->andFilterWhere(['like', 'ssrh_to_description', $this->ssrh_to_description]);

        return $dataProvider;
    }
}
