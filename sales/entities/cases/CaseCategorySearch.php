<?php

namespace sales\entities\cases;

use yii\data\ActiveDataProvider;

/**
 * Class CaseCategorySearch
 */
class CaseCategorySearch extends CaseCategory
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['cc_key', 'cc_name'], 'string'],
            [['cc_dep_id', 'cc_system'], 'integer'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = CaseCategory::find();

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
            'cc_dep_id' => $this->cc_dep_id,
            'cc_system' => $this->cc_system,
        ]);

        $query->andFilterWhere(['like', 'cc_key', $this->cc_key])
            ->andFilterWhere(['like', 'cc_name', $this->cc_name]);

        return $dataProvider;
    }
}
