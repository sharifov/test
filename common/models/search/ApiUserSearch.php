<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApiUser;

/**
 * ApiUserSearch represents the model behind the search form of `common\models\ApiUser`.
 */
class ApiUserSearch extends ApiUser
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['au_id', 'au_project_id', 'au_enabled', 'au_updated_user_id'], 'integer'],
            [['au_name', 'au_api_username', 'au_api_password', 'au_email', 'au_updated_dt'], 'safe'],
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
        $query = ApiUser::find();

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
            'au_id' => $this->au_id,
            'au_project_id' => $this->au_project_id,
            'au_enabled' => $this->au_enabled,
            'au_updated_dt' => $this->au_updated_dt,
            'au_updated_user_id' => $this->au_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'au_name', $this->au_name])
            ->andFilterWhere(['like', 'au_api_username', $this->au_api_username])
            ->andFilterWhere(['like', 'au_api_password', $this->au_api_password])
            ->andFilterWhere(['like', 'au_email', $this->au_email]);

        return $dataProvider;
    }
}
