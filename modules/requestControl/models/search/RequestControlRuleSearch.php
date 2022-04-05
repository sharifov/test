<?php

namespace modules\requestControl\models\search;

use modules\requestControl\models\RequestControlRule;
use yii\data\ActiveDataProvider;

/**
 * Class RequestControlRuleSearch
 * @package modules\requestControl\models\search
 */
class RequestControlRuleSearch extends RequestControlRule
{
    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = RequestControlRule::find();
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);
        return $dataProvider;
    }
}
