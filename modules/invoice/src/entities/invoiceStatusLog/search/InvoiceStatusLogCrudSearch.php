<?php

namespace modules\invoice\src\entities\invoiceStatusLog\search;

use common\models\Employee;
use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoice\InvoiceStatus;
use modules\invoice\src\entities\invoice\InvoiceStatusAction;
use modules\invoice\src\entities\invoiceStatusLog\InvoiceStatusLog;
use src\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

class InvoiceStatusLogCrudSearch extends InvoiceStatusLog
{
    public function rules(): array
    {
        return [
            ['invsl_id', 'integer'],
            ['invsl_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['invsl_id' => 'invsl_id']],

            ['invsl_invoice_id', 'integer'],
            ['invsl_invoice_id', 'exist', 'skipOnError' => true, 'targetClass' => Invoice::class, 'targetAttribute' => ['invsl_invoice_id' => 'inv_id']],

            ['invsl_start_status_id', 'integer'],
            ['invsl_start_status_id', 'in', 'range' => array_keys(InvoiceStatus::getList())],

            ['invsl_end_status_id', 'integer'],
            ['invsl_end_status_id', 'in', 'range' => array_keys(InvoiceStatus::getList())],

            ['invsl_start_dt', 'date', 'format' => 'php:Y-m-d'],

            ['invsl_end_dt', 'date', 'format' => 'php:Y-m-d'],

            ['invsl_duration', 'integer'],

            ['invsl_description', 'string', 'max' => 255],

            ['invsl_action_id', 'integer'],
            ['invsl_action_id', 'in', 'range' => array_keys(InvoiceStatusAction::getList())],

            ['invsl_created_user_id', 'integer'],
            ['invsl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['invsl_created_user_id' => 'id']],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['createdUser', 'invoice']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->invsl_start_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'invsl_start_dt', $this->invsl_start_dt, $user->timezone);
        }

        if ($this->invsl_end_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'invsl_end_dt', $this->invsl_end_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'invsl_id' => $this->invsl_id,
            'invsl_invoice_id' => $this->invsl_invoice_id,
            'invsl_start_status_id' => $this->invsl_start_status_id,
            'invsl_end_status_id' => $this->invsl_end_status_id,
            'invsl_duration' => $this->invsl_duration,
            'invsl_action_id' => $this->invsl_action_id,
            'invsl_created_user_id' => $this->invsl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'invsl_description', $this->invsl_description]);

        return $dataProvider;
    }
}
