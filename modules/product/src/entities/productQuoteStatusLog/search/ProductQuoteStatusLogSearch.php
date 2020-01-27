<?php

namespace modules\product\src\entities\productQuoteStatusLog\search;

use common\models\Employee;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatusAction;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\product\src\entities\productQuoteStatusLog\ProductQuoteStatusLog;

class ProductQuoteStatusLogSearch extends ProductQuoteStatusLog
{
    public function rules(): array
    {
        return [
            ['pqsl_id', 'integer'],
            ['pqsl_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['pqsl_id' => 'pqsl_id']],

            ['pqsl_start_status_id', 'integer'],
            ['pqsl_start_status_id', 'in', 'range' => array_keys(ProductQuoteStatus::getList())],

            ['pqsl_end_status_id', 'integer'],
            ['pqsl_end_status_id', 'in', 'range' => array_keys(ProductQuoteStatus::getList())],

            ['pqsl_start_dt', 'date', 'format' => 'php:Y-m-d'],

            ['pqsl_end_dt', 'date', 'format' => 'php:Y-m-d'],

            ['pqsl_duration', 'integer'],

            ['pqsl_description', 'string', 'max' => 255],

            ['pqsl_action_id', 'integer'],
            ['pqsl_action_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['pqsl_action_id', 'in', 'range' => array_keys(ProductQuoteStatusAction::getList())],

            ['pqsl_owner_user_id', 'integer'],
            ['pqsl_owner_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqsl_owner_user_id' => 'id']],

            ['pqsl_created_user_id', 'integer'],
            ['pqsl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqsl_created_user_id' => 'id']],
        ];
    }

    public function search($params, Employee $user, int $productQuoteId): ActiveDataProvider
    {
        $query = self::find()->with(['createdUser', 'ownerUser', 'productQuote']);

        $query->andWhere(['pqsl_product_quote_id' => $productQuoteId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->pqsl_start_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pqsl_start_dt', $this->pqsl_start_dt, $user->timezone);
        }

        if ($this->pqsl_end_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'pqsl_end_dt', $this->pqsl_end_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'pqsl_id' => $this->pqsl_id,
            'pqsl_start_status_id' => $this->pqsl_start_status_id,
            'pqsl_end_status_id' => $this->pqsl_end_status_id,
            'pqsl_duration' => $this->pqsl_duration,
            'pqsl_action_id' => $this->pqsl_action_id,
            'pqsl_owner_user_id' => $this->pqsl_owner_user_id,
            'pqsl_created_user_id' => $this->pqsl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'pqsl_description', $this->pqsl_description]);

        return $dataProvider;
    }
}
