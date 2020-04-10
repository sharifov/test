<?php

namespace sales\model\callLog\entity\callLog\search;

use common\models\Employee;
use sales\model\callLog\entity\callLog\CallLogCategory;
use sales\model\callLog\entity\callLog\CallLogStatus;
use sales\model\callLog\entity\callLog\CallLogType;
use yii\data\ActiveDataProvider;
use sales\model\callLog\entity\callLog\CallLog;

/**
 * Class CallLogSearch
 *
 * @property int|null $lead_id
 * @property int|null $case_id
 */
class CallLogSearch extends CallLog
{
    public $lead_id;
    public $case_id;

    public function rules(): array
    {
        return [
            ['cl_type_id', 'integer'],
            ['cl_type_id', 'in', 'range' => array_keys(CallLogType::getList())],

            ['cl_category_id', 'integer'],
            ['cl_category_id', 'in', 'range' => array_keys(CallLogCategory::getList())],

            ['cl_status_id', 'integer'],
            ['cl_status_id', 'in', 'range' => array_keys(CallLogStatus::getList())],

            ['cl_is_transfer', 'boolean'],

            [['cl_phone_from', 'cl_phone_to'], 'string'],

            ['cl_phone_list_id', 'integer'],

            [['cl_id', 'cl_parent_id', 'cl_category_id', 'cl_is_transfer', 'cl_phone_list_id', 'cl_user_id', 'cl_department_id', 'cl_project_id', 'cl_client_id'], 'integer'],

            [['cl_call_sid', 'cl_phone_from', 'cl_phone_to'], 'string'],

            [['cl_price'], 'number'],

            [['cl_call_created_dt', 'cl_call_finished_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['lead_id', 'integer'],
            ['case_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find()
            ->with(['project', 'department', 'phoneList', 'user'])
            ->joinWith(['callLogLead.lead', 'callLogCase.case']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['lead_id'] = [
            'asc' => ['cll_lead_id' => SORT_ASC],
            'desc' => ['cll_lead_id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['case_id'] = [
            'asc' => ['clc_case_id' => SORT_ASC],
            'desc' => ['clc_case_id' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->cl_call_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cl_call_created_dt', $this->cl_call_created_dt, $user->timezone);
        }

        if ($this->cl_call_finished_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'cl_call_finished_dt', $this->cl_call_finished_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cl_id' => $this->cl_id,
            'cl_parent_id' => $this->cl_parent_id,
            'cl_type_id' => $this->cl_type_id,
            'cl_category_id' => $this->cl_category_id,
            'cl_is_transfer' => $this->cl_is_transfer,
            'cl_duration' => $this->cl_duration,
            'cl_phone_list_id' => $this->cl_phone_list_id,
            'cl_user_id' => $this->cl_user_id,
            'cl_department_id' => $this->cl_department_id,
            'cl_project_id' => $this->cl_project_id,
            'cl_status_id' => $this->cl_status_id,
            'cl_client_id' => $this->cl_client_id,
            'cl_price' => $this->cl_price,
            'cll_lead_id' => $this->lead_id,
            'clc_case_id' => $this->case_id,
        ]);

        $query->andFilterWhere(['like', 'cl_call_sid', $this->cl_call_sid])
            ->andFilterWhere(['like', 'cl_phone_from', $this->cl_phone_from])
            ->andFilterWhere(['like', 'cl_phone_to', $this->cl_phone_to]);

        return $dataProvider;
    }
}
