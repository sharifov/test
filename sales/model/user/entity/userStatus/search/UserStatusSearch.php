<?php

namespace sales\model\user\entity\userStatus\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\user\entity\userStatus\UserStatus;

/**
 * UserStatusSearch represents the model behind the search form of `sales\model\user\entity\userStatus\UserStatus`.
 */
class UserStatusSearch extends UserStatus
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['us_user_id', 'us_gl_call_count', 'us_call_phone_status', 'us_is_on_call', 'us_has_call_access'], 'integer'],
            [['us_updated_dt'], 'safe'],
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
        $query = UserStatus::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['us_updated_dt' => SORT_DESC, 'us_user_id' => SORT_ASC]],
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
            'us_user_id' => $this->us_user_id,
            'us_gl_call_count' => $this->us_gl_call_count,
            'us_call_phone_status' => $this->us_call_phone_status,
            'us_is_on_call' => $this->us_is_on_call,
            'us_has_call_access' => $this->us_has_call_access,
            'us_updated_dt' => $this->us_updated_dt,
        ]);

        return $dataProvider;
    }
}
