<?php

namespace modules\experiment\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\experiment\models\ExperimentTarget;

/**
 * ExperimentTargetSearch represents the model behind the search form of `modules\experiment\models\ExperimentTarget`.
 */
class ExperimentTargetSearch extends ExperimentTarget
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ext_id', 'ext_target_id', 'ext_experiment_id'], 'integer'],
            [['ext_target_type_id'], 'safe'],
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
        $query = ExperimentTarget::find();

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
            'ext_id' => $this->ext_id,
            'ext_target_id' => $this->ext_target_id,
            'ext_experiment_id' => $this->ext_experiment_id,
        ]);

        $query->andFilterWhere(['like', 'ext_target_type_id', $this->ext_target_type_id]);

        return $dataProvider;
    }
}
