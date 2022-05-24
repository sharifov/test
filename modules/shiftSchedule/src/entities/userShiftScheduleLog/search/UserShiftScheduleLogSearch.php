<?php

namespace modules\shiftSchedule\src\entities\userShiftScheduleLog\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog;

/**
 * UserShiftScheduleLogSearch represents the model behind the search form of `modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog`.
 */
class UserShiftScheduleLogSearch extends UserShiftScheduleLog
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ussl_id', 'ussl_uss_id', 'ussl_created_user_id', 'ussl_month_start', 'ussl_year_start'], 'integer'],
            [['ussl_old_attr', 'ussl_new_attr', 'ussl_formatted_attr', 'ussl_created_dt'], 'safe'],
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
        $query = UserShiftScheduleLog::find();

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
            'ussl_id' => $this->ussl_id,
            'ussl_uss_id' => $this->ussl_uss_id,
            'ussl_created_user_id' => $this->ussl_created_user_id,
            'ussl_created_dt' => $this->ussl_created_dt,
            'ussl_month_start' => $this->ussl_month_start,
            'ussl_year_start' => $this->ussl_year_start,
        ]);

        $query->andFilterWhere(['like', 'ussl_old_attr', $this->ussl_old_attr])
            ->andFilterWhere(['like', 'ussl_new_attr', $this->ussl_new_attr])
            ->andFilterWhere(['like', 'ussl_formatted_attr', $this->ussl_formatted_attr]);

        return $dataProvider;
    }
}
