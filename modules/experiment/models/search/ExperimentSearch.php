<?php

namespace modules\experiment\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\experiment\models\Experiment;

/**
 * ExperimentSearch represents the model behind the search form of `modules\experiment\models\Experiment`.
 */
class ExperimentSearch extends Experiment
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ex_id'], 'integer'],
            [['ex_code'], 'safe'],
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
        $query = Experiment::find();

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
            'ex_id' => $this->ex_id,
        ]);

        $query->andFilterWhere(['like', 'ex_code', $this->ex_code]);

        return $dataProvider;
    }
}
