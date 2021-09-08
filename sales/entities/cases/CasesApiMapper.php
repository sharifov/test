<?php

namespace sales\entities\cases;

class CasesApiMapper extends Cases
{
    public function fields(): array
    {
        return [
            'id' => 'cs_id',
            'gid' => 'cs_gid',
            'created_dt' => 'cs_created_dt',
            'updated_dt' => 'cs_updated_dt',
            'last_action_dt' => 'cs_last_action_dt',
            'order_uid' => 'cs_order_uid',
//            'category_key' => 'cc_key',
//            'next_flight' => function ($model) {
//                return $model->next_flight;
//            },
            'project_name' => 'name',
            'cs_status',
            'cs_category_id',
        ];
    }
}
