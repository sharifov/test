<?php

namespace sales\model\saleTicket\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\saleTicket\entity\SaleTicket;

class SaleTicketSearch extends SaleTicket
{
    public function rules(): array
    {
        return [
            ['st_id', 'integer'],

            ['st_case_id', 'integer'],

            ['st_case_sale_id', 'integer'],

            ['st_client_name', 'safe'],

            ['st_charge_system', 'integer'],

            ['st_created_dt', 'safe'],

            ['st_created_user_id', 'integer'],

            ['st_markup', 'number'],

            ['st_original_fop', 'safe'],

            ['st_penalty_amount', 'number'],

            ['st_penalty_type', 'safe'],

            ['st_recall_commission', 'number'],

            ['st_record_locator', 'safe'],

            ['st_refundable_amount', 'number'],

            ['st_selling', 'number'],

            ['st_service_fee', 'number'],

            ['st_ticket_number', 'safe'],

            ['st_updated_dt', 'safe'],

            ['st_updated_user_id', 'integer'],

            ['st_upfront_charge', 'number'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'st_id' => $this->st_id,
            'st_case_id' => $this->st_case_id,
            'st_case_sale_id' => $this->st_case_sale_id,
            'st_client_name' => $this->st_client_name,
            'st_charge_system' => $this->st_charge_system,
            'st_penalty_amount' => $this->st_penalty_amount,
            'st_selling' => $this->st_selling,
            'st_service_fee' => $this->st_service_fee,
            'st_recall_commission' => $this->st_recall_commission,
            'st_markup' => $this->st_markup,
            'st_upfront_charge' => $this->st_upfront_charge,
            'st_refundable_amount' => $this->st_refundable_amount,
            'st_created_dt' => $this->st_created_dt,
            'st_updated_dt' => $this->st_updated_dt,
            'st_created_user_id' => $this->st_created_user_id,
            'st_updated_user_id' => $this->st_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'st_ticket_number', $this->st_ticket_number])
            ->andFilterWhere(['like', 'st_record_locator', $this->st_record_locator])
            ->andFilterWhere(['like', 'st_original_fop', $this->st_original_fop])
            ->andFilterWhere(['like', 'st_penalty_type', $this->st_penalty_type]);

        return $dataProvider;
    }
}
