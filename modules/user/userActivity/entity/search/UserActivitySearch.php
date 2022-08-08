<?php

namespace modules\user\userActivity\entity\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\user\userActivity\entity\UserActivity;

/**
 * UserActivitySearch represents the model behind the search form of `modules\user\userActivity\entity\UserActivity`.
 */
class UserActivitySearch extends UserActivity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ua_user_id', 'ua_object_id', 'ua_type_id', 'ua_shift_event_id'], 'integer'],
            [['ua_object_event', 'ua_start_dt', 'ua_end_dt', 'ua_description'], 'safe'],
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
        $query = UserActivity::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'ua_start_dt' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ua_user_id' => $this->ua_user_id,
            'ua_object_id' => $this->ua_object_id,
            'ua_start_dt' => $this->ua_start_dt,
            'DATE(ua_end_dt)' => $this->ua_end_dt,
            'ua_type_id' => $this->ua_type_id,
            'ua_shift_event_id' => $this->ua_shift_event_id,
        ]);

        $query->andFilterWhere(['like', 'ua_object_event', $this->ua_object_event])
            ->andFilterWhere(['like', 'ua_description', $this->ua_description]);

        return $dataProvider;
    }
}
