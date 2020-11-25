<?php

namespace frontend\models\search;

use common\models\Employee;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\UserFailedLogin;

/**
 * UserFailedLoginSearch represents the model behind the search form of `frontend\models\UserFailedLogin`.
 */
class UserFailedLoginSearch extends UserFailedLogin
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ufl_id', 'ufl_user_id'], 'integer'],
            [['ufl_username', 'ufl_ua', 'ufl_ip', 'ufl_session_id'], 'safe'],
            [['ufl_created_dt'], 'date', 'format' => 'php:Y-m-d'],
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
        $query = UserFailedLogin::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ufl_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ufl_id' => $this->ufl_id,
            'ufl_user_id' => $this->ufl_user_id,
        ]);

        if ($this->ufl_created_dt) {
            $query->andFilterWhere(['>=', 'ufl_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ufl_created_dt))])
                ->andFilterWhere(['<=', 'ufl_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->ufl_created_dt) + 3600 * 24)]);
        }

        $query->andFilterWhere(['like', 'ufl_username', $this->ufl_username])
            ->andFilterWhere(['like', 'ufl_ua', $this->ufl_ua])
            ->andFilterWhere(['like', 'ufl_ip', $this->ufl_ip])
            ->andFilterWhere(['like', 'ufl_session_id', $this->ufl_session_id]);

        return $dataProvider;
    }
}
