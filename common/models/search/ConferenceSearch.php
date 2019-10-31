<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Conference;

/**
 * ConferenceSearch represents the model behind the search form of `common\models\Conference`.
 */
class ConferenceSearch extends Conference
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cf_id', 'cf_cr_id', 'cf_status_id'], 'integer'],
            [['cf_sid', 'cf_options', 'cf_created_dt', 'cf_updated_dt'], 'safe'],
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
        $query = Conference::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cf_id' => SORT_DESC]],
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
            'cf_id' => $this->cf_id,
            'cf_cr_id' => $this->cf_cr_id,
            'cf_status_id' => $this->cf_status_id,
            'cf_created_dt' => $this->cf_created_dt,
            'cf_updated_dt' => $this->cf_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cf_sid', $this->cf_sid])
            ->andFilterWhere(['like', 'cf_options', $this->cf_options]);

        return $dataProvider;
    }
}
