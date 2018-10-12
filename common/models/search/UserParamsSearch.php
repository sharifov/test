<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserParams;

/**
 * UserParamsSearch represents the model behind the search form of `common\models\UserParams`.
 */
class UserParamsSearch extends UserParams
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['up_user_id', 'up_commission_percent', 'up_updated_user_id'], 'integer'],
            [['up_base_amount'], 'number'],
            [['up_updated_dt'], 'safe'],
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
        $query = UserParams::find();

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
            'up_user_id' => $this->up_user_id,
            'up_commission_percent' => $this->up_commission_percent,
            'up_base_amount' => $this->up_base_amount,
            'up_updated_dt' => $this->up_updated_dt,
            'up_updated_user_id' => $this->up_updated_user_id,
        ]);

        return $dataProvider;
    }
}
