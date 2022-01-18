<?php

namespace modules\user\userFeedback\entity\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use modules\user\userFeedback\entity\UserFeedback;

/**
 * UserFeedbackSearch represents the model behind the search form of `modules\user\userFeedback\entity\UserFeedback`.
 */
class UserFeedbackSearch extends UserFeedback
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uf_id', 'uf_type_id', 'uf_status_id', 'uf_created_user_id', 'uf_updated_user_id'], 'integer'],
            [['uf_title', 'uf_message', 'uf_data_json', 'uf_created_dt', 'uf_updated_dt'], 'safe'],
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
        $query = UserFeedback::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['uf_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
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
            'uf_id' => $this->uf_id,
            'uf_type_id' => $this->uf_type_id,
            'uf_status_id' => $this->uf_status_id,
            'uf_created_dt' => $this->uf_created_dt,
            'uf_updated_dt' => $this->uf_updated_dt,
            'uf_created_user_id' => $this->uf_created_user_id,
            'uf_updated_user_id' => $this->uf_updated_user_id,
        ]);

        $query->andFilterWhere(['ilike', 'uf_title', $this->uf_title])
            ->andFilterWhere(['ilike', 'uf_message', $this->uf_message])
            ->andFilterWhere(['ilike', 'uf_data_json', $this->uf_data_json]);

        return $dataProvider;
    }
}
