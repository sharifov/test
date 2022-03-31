<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 7:27 PM
 */

namespace modules\requestControl\models\search;

use modules\requestControl\models\Rule;
use yii\data\ActiveDataProvider;

class RuleSearch extends Rule
{
    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Rule::find();
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        $this->load($params);
        return $dataProvider;
    }
}