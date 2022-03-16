<?php

namespace modules\eventManager\src\entities\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\eventManager\src\entities\EventHandler;

/**
 * EventHandlerSearch represents the model behind the search form of `modules\eventManager\src\entities\EventHandler`.
 */
class EventHandlerSearch extends EventHandler
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eh_id', 'eh_el_id', 'eh_enable_type', 'eh_enable_log', 'eh_asynch', 'eh_break', 'eh_sort_order', 'eh_updated_user_id'], 'integer'],
            [['eh_class', 'eh_method', 'eh_cron_expression', 'eh_condition', 'eh_builder_json', 'eh_updated_dt', 'eh_params'], 'safe'],
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
        $query = EventHandler::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['eh_id' => SORT_DESC]],
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
            'eh_id' => $this->eh_id,
            'eh_el_id' => $this->eh_el_id,
            'eh_enable_type' => $this->eh_enable_type,
            'eh_enable_log' => $this->eh_enable_log,
            'eh_asynch' => $this->eh_asynch,
            'eh_break' => $this->eh_break,
            'eh_sort_order' => $this->eh_sort_order,
            'eh_updated_dt' => $this->eh_updated_dt,
            'eh_updated_user_id' => $this->eh_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'eh_class', $this->eh_class])
            ->andFilterWhere(['like', 'eh_method', $this->eh_method])
            ->andFilterWhere(['like', 'eh_cron_expression', $this->eh_cron_expression])
            ->andFilterWhere(['like', 'eh_condition', $this->eh_condition])
            ->andFilterWhere(['like', 'eh_params', $this->eh_params])
            ->andFilterWhere(['like', 'eh_builder_json', $this->eh_builder_json]);

        return $dataProvider;
    }
}
