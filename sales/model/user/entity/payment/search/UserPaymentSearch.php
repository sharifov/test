<?php

namespace sales\model\user\entity\payment\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\user\entity\payment\UserPayment;

/**
 * UserPaymentSearch represents the model behind the search form of `sales\model\user\entity\payment\UserPayment`.
 */
class UserPaymentSearch extends UserPayment
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['upt_id', 'upt_assigned_user_id', 'upt_category_id', 'upt_status_id', 'upt_created_user_id', 'upt_updated_user_id', 'upt_payroll_id'], 'integer'],
            [['upt_amount'], 'number'],
            [['upt_description', 'upt_date', 'upt_created_dt', 'upt_updated_dt'], 'safe'],
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
        $query = UserPayment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['upt_id' => SORT_DESC]],
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
            'upt_id' => $this->upt_id,
            'upt_assigned_user_id' => $this->upt_assigned_user_id,
            'upt_category_id' => $this->upt_category_id,
            'upt_status_id' => $this->upt_status_id,
            'upt_amount' => $this->upt_amount,
            'upt_date' => $this->upt_date,
            'upt_created_user_id' => $this->upt_created_user_id,
            'upt_updated_user_id' => $this->upt_updated_user_id,
            'date_format(upt_created_dt, "%Y-%m-%d")' => $this->upt_created_dt,
            'date_format(upt_updated_dt, "%Y-%m-%d")' => $this->upt_updated_dt,
            'upt_payroll_id' => $this->upt_payroll_id,
        ]);

        $query->andFilterWhere(['like', 'upt_description', $this->upt_description]);

        return $dataProvider;
    }
}
