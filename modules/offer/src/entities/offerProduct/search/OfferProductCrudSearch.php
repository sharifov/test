<?php

namespace modules\offer\src\entities\offerProduct\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\offer\src\entities\offerProduct\OfferProduct;

/**
 * OfferProductSearch represents the model behind the search form of `common\models\OfferProduct`.
 */
class OfferProductCrudSearch extends OfferProduct
{
    public function rules(): array
    {
        return [
            [['op_offer_id', 'op_product_quote_id', 'op_created_user_id'], 'integer'],

            ['op_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['opProductQuote', 'opCreatedUser']);

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

        if ($this->op_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'op_created_dt', $this->op_created_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'op_offer_id' => $this->op_offer_id,
            'op_product_quote_id' => $this->op_product_quote_id,
            'op_created_user_id' => $this->op_created_user_id,
        ]);

        return $dataProvider;
    }
}
