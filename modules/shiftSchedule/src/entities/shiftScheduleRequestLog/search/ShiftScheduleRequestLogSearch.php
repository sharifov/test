<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequestLog\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\shiftScheduleRequestLog\ShiftScheduleRequestLog;

/**
 * ShiftScheduleRequestLogSearch represents the model behind the search form of `modules\shiftSchedule\src\entities\shiftScheduleRequestLog\ShiftScheduleRequestLog`.
 */
class ShiftScheduleRequestLogSearch extends ShiftScheduleRequestLog
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ssrh_id', 'ssrh_ssr_id', 'ssrh_created_user_id', 'ssrh_updated_user_id'], 'integer'],
            [['ssrh_old_attr', 'ssrh_new_attr', 'ssrh_formatted_attr', 'ssrh_created_dt', 'ssrh_updated_dt'], 'safe'],
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
        $query = ShiftScheduleRequestLog::find();

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
            'ssrh_created_dt' => $this->ssrh_created_dt,
            'ssrh_updated_dt' => $this->ssrh_updated_dt,
            'ssrh_created_user_id' => $this->ssrh_created_user_id,
            'ssrh_updated_user_id' => $this->ssrh_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ssrh_old_attr', $this->ssrh_old_attr])
            ->andFilterWhere(['like', 'ssrh_new_attr', $this->ssrh_new_attr])
            ->andFilterWhere(['like', 'ssrh_formatted_attr', $this->ssrh_formatted_attr]);

        return $dataProvider;
    }
}
