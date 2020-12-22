<?php

namespace sales\model\project\entity\projectLocale\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\project\entity\projectLocale\ProjectLocale;

/**
 * ProjectLocaleSearch represents the model behind the search form of `sales\model\project\entity\projectLocale\ProjectLocale`.
 */
class ProjectLocaleSearch extends ProjectLocale
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pl_id', 'pl_project_id', 'pl_default', 'pl_enabled', 'pl_created_user_id', 'pl_updated_user_id'], 'integer'],
            [['pl_language_id', 'pl_params', 'pl_created_dt', 'pl_updated_dt', 'pl_market_country'], 'safe'],
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
        $query = ProjectLocale::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pl_updated_dt' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 40,
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
            'pl_project_id' => $this->pl_project_id,
            'pl_id' => $this->pl_id,
            'pl_default' => $this->pl_default,
            'pl_enabled' => $this->pl_enabled,
            'pl_created_user_id' => $this->pl_created_user_id,
            'pl_updated_user_id' => $this->pl_updated_user_id,
            'DATE(pl_created_dt)' => $this->pl_created_dt,
            'DATE(pl_updated_dt)' => $this->pl_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'pl_language_id', $this->pl_language_id])
            ->andFilterWhere(['like', 'pl_market_country', $this->pl_market_country])
            ->andFilterWhere(['like', 'pl_params', $this->pl_params]);

        return $dataProvider;
    }
}
