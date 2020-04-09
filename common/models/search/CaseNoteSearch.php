<?php

namespace common\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CaseNote;

/**
 * CaseNoteSearch represents the model behind the search form of `common\models\CaseNote`.
 */
class CaseNoteSearch extends CaseNote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cn_id', 'cn_cs_id', 'cn_user_id'], 'integer'],
            [['cn_text', 'cn_created_dt', 'cn_updated_dt'], 'safe'],
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
        $query = CaseNote::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['cn_id' => SORT_ASC]],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->cn_created_dt) {
            $query->andFilterWhere(['>=', 'cn_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cn_created_dt))])
                ->andFilterWhere(['<=', 'cn_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->cn_created_dt) + 3600 * 24)]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cn_id' => $this->cn_id,
            'cn_cs_id' => $this->cn_cs_id,
            'cn_user_id' => $this->cn_user_id,
            'cn_updated_dt' => $this->cn_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cn_text', $this->cn_text]);

        return $dataProvider;
    }
}
