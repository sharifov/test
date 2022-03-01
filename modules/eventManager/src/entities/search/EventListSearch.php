<?php

namespace modules\eventManager\src\entities\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\eventManager\src\entities\EventList;

/**
 * EventListSearch represents the model behind the search form of `modules\eventManager\src\entities\EventList`.
 */
class EventListSearch extends EventList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['el_id', 'el_enable_type', 'el_enable_log', 'el_break', 'el_sort_order', 'el_updated_user_id'], 'integer'],
            [['el_key', 'el_category', 'el_description', 'el_cron_expression', 'el_condition', 'el_builder_json', 'el_updated_dt'], 'safe'],
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
        $query = EventList::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['el_id' => SORT_DESC]],
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
            'el_id' => $this->el_id,
            'el_enable_type' => $this->el_enable_type,
            'el_enable_log' => $this->el_enable_log,
            'el_break' => $this->el_break,
            'el_sort_order' => $this->el_sort_order,
            'el_updated_dt' => $this->el_updated_dt,
            'el_updated_user_id' => $this->el_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'el_key', $this->el_key])
            ->andFilterWhere(['like', 'el_category', $this->el_category])
            ->andFilterWhere(['like', 'el_description', $this->el_description])
            ->andFilterWhere(['like', 'el_cron_expression', $this->el_cron_expression])
            ->andFilterWhere(['like', 'el_condition', $this->el_condition])
            ->andFilterWhere(['like', 'el_builder_json', $this->el_builder_json]);

        return $dataProvider;
    }
}
