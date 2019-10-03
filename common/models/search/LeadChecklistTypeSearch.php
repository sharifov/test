<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LeadChecklistType;

/**
 * LeadChecklistTypeSearch represents the model behind the search form of `common\models\LeadChecklistType`.
 */
class LeadChecklistTypeSearch extends LeadChecklistType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lct_id', 'lct_enabled', 'lct_sort_order', 'lct_updated_user_id'], 'integer'],
            [['lct_key', 'lct_name', 'lct_description', 'lct_updated_dt'], 'safe'],
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
        $query = LeadChecklistType::find();

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

        if ($this->lct_updated_dt) {
            $query->andFilterWhere(['>=', 'lct_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lct_updated_dt))])
                ->andFilterWhere(['<=', 'lct_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->lct_updated_dt) + 3600 *24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'lct_id' => $this->lct_id,
            'lct_enabled' => $this->lct_enabled,
            'lct_sort_order' => $this->lct_sort_order,
            'lct_updated_user_id' => $this->lct_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'lct_key', $this->lct_key])
            ->andFilterWhere(['like', 'lct_name', $this->lct_name])
            ->andFilterWhere(['like', 'lct_description', $this->lct_description]);

        return $dataProvider;
    }
}
