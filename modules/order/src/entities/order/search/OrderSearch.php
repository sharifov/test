<?php

namespace modules\order\src\entities\order\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\order\src\entities\order\Order;

/**
 * Class OrderSearch
 *
 * @property $show_fields
 * @property $createdRangeTime
 * @property $updatedRangeTime
 */
class OrderSearch extends Order
{
    public $show_fields = [];
    public $createdRangeTime;
    public $updatedRangeTime;

    public function rules(): array
    {
        return [
            [['or_id', 'or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id', 'or_created_user_id', 'or_updated_user_id'], 'integer'],
            [['or_gid', 'or_uid', 'or_name', 'or_description', 'or_client_currency'], 'safe'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate', 'or_profit_amount'], 'number'],

            ['or_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['or_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['show_fields', 'filter', 'filter' => static function ($value) {
                return is_array($value) ? $value : [];
            }, 'skipOnEmpty' => true],

            [['createdRangeTime', 'updatedRangeTime'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['orLead', 'orOwnerUser', 'orCreatedUser', 'orUpdatedUser', 'productQuotes.pqProduct.prType']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'or_id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->or_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_created_dt', $this->or_created_dt, $user->timezone);
        }

        if ($this->or_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'or_updated_dt', $this->or_updated_dt, $user->timezone);
        }

        if ($this->createdRangeTime) {
            $createdRange = explode(" - ", $this->createdRangeTime);
            if ($createdRange[0]) {
                $query->andFilterWhere(['>=', 'or_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[0]))]);
            }
            if ($createdRange[1]) {
                $query->andFilterWhere(['<=', 'or_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($createdRange[1]))]);
            }
        }

        if ($this->updatedRangeTime) {
            $updatedRange = explode(" - ", $this->updatedRangeTime);
            if ($updatedRange[0]) {
                $query->andFilterWhere(['>=', 'or_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[0]))]);
            }
            if ($updatedRange[1]) {
                $query->andFilterWhere(['<=', 'or_updated_dt', Employee::convertTimeFromUserDtToUTC(strtotime($updatedRange[1]))]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'or_id' => $this->or_id,
            'or_lead_id' => $this->or_lead_id,
            'or_status_id' => $this->or_status_id,
            'or_pay_status_id' => $this->or_pay_status_id,
            'or_app_total' => $this->or_app_total,
            'or_app_markup' => $this->or_app_markup,
            'or_agent_markup' => $this->or_agent_markup,
            'or_client_total' => $this->or_client_total,
            'or_client_currency_rate' => $this->or_client_currency_rate,
            'or_owner_user_id' => $this->or_owner_user_id,
            'or_created_user_id' => $this->or_created_user_id,
            'or_updated_user_id' => $this->or_updated_user_id,
            'or_profit_amount' => $this->or_profit_amount,
        ]);

        $query->andFilterWhere(['like', 'or_gid', $this->or_gid])
            ->andFilterWhere(['like', 'or_uid', $this->or_uid])
            ->andFilterWhere(['like', 'or_name', $this->or_name])
            ->andFilterWhere(['like', 'or_description', $this->or_description])
            ->andFilterWhere(['like', 'or_client_currency', $this->or_client_currency]);

        return $dataProvider;
    }

    public function searchByLead(int $leadId): ActiveDataProvider
    {
        $query = self::find()->andWhere(['or_lead_id' => $leadId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    public function getViewFields(): array
    {
        return [
            'or_name' => 'Name',
            'or_description' => 'Description',
            'or_app_total' => 'App total',
            'or_app_markup' => 'App markup',
            'or_agent_markup' => 'Agent markup',
            'or_client_total' => 'Client total',
            'or_client_currency' => 'Client currency',
            'or_client_currency_rate' => 'Client currency rate',
            'or_profit_amount' => 'Profit',
            'or_created_user_id' => 'Created user',
            'or_updated_user_id' => 'Updated user',
            'or_updated_dt' => 'Updated dt',
        ];
    }
}
