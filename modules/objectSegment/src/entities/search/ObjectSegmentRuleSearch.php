<?php

namespace modules\objectSegment\src\entities\search;

use modules\objectSegment\src\entities\ObjectSegmentRule;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ObjectSegmentRuleSearch extends ObjectSegmentRule
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['osr_id', 'osr_updated_user_id', 'osr_osl_id'], 'integer'],
            [
                [
                    'osr_rule_condition_json',
                    'osr_title',
                    'osr_enabled'
                ],
                'safe'
            ],
            [
                [
                    'osr_created_dt',
                    'osr_updated_dt'
                ],
                'date',
                'format' => 'php:Y-m-d'
            ],
            [['osr_rule_condition'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = ObjectSegmentRule::find();

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['osr_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'osr_id'              => $this->osr_id,
            'date_format(osr_created_dt, "%Y-%m-%d")' => $this->osr_created_dt,
            'date_format(osr_updated_dt, "%Y-%m-%d")' => $this->osr_updated_dt,
            'osr_osl_id'          => $this->osr_osl_id,
            'osr_updated_user_id' => $this->osr_updated_user_id,
            'osr_enabled'         => $this->osr_enabled,
        ]);
        $query->andFilterWhere(['like', 'osr_rule_condition_json', $this->osr_rule_condition_json])
              ->andFilterWhere(['like', 'osr_title', $this->osr_title]);

        return $dataProvider;
    }
}
