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

        $query
            ->andFilterWhere(['rcr_local' => $this->rcr_local, 'rcr_global' => $this->rcr_global])
            ->andFilterWhere(['like', 'rcr_type', $this->rcr_type])
            ->andFilterWhere(['like', 'rcr_subject', $this->rcr_subject]);

        return $dataProvider;
    }
}
