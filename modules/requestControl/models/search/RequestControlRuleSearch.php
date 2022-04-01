<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 7:27 PM
 */

namespace modules\requestControl\models\search;

use modules\requestControl\models\RequestControlRule;
use yii\data\ActiveDataProvider;

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