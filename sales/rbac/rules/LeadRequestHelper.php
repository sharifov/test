<?php

namespace sales\rbac\rules;

use Yii;

class LeadRequestHelper
{
    public static function getId($params): int
    {
        $id = Yii::$app->request->post('id');
        if (!$id) {
            $id = Yii::$app->request->get('id');
            if (!$id && isset($params['id'])) {
                $id = $params['id'];
            }
        }
        return (int)$id;
    }
}
