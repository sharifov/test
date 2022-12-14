<?php

namespace src\entities\cases;

class CasesApiMapper extends Cases
{
    public $next_flight;

    public function fields(): array
    {
        return [
            'id' => 'cs_id',
            'gid' => 'cs_gid',
            'created_dt' => 'cs_created_dt',
            'updated_dt' => function ($model) {
                return $model->cs_updated_dt ?? '';
            },
            'last_action_dt' => function ($model) {
                return $model->cs_last_action_dt ?? '';
            },
            'order_uid' => function ($model) {
                return $model->cs_order_uid ?? '';
            },
            'project_name' => 'name',
            'status_name' => function ($model) {
                return CasesStatus::getName($model->cs_status);
            },
            'category_key' => function ($model) {
                return CaseCategory::getKey($model->cs_category_id) ?: '';
            },
            'next_flight' => function ($model) {
                return $model->next_flight ?? '';
            },
        ];
    }
}
