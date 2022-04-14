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
     * @return array
     */
    public function rules(): array
    {
        return [
            [['rcr_local', 'rcr_global'], 'integer'],
            [['rcr_type', 'rcr_subject'], 'string'],
            [['rcr_local', 'rcr_global', 'rcr_type', 'rcr_subject'], 'safe'],
        ];
    }
    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = RequestControlRule::find();
        $dataProvider = new ActiveDataProvider(['query' => $query]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['rcr_local' => $this->rcr_local, 'rcr_global' => $this->rcr_global])
            ->andFilterWhere(['like', 'rcr_type', $this->rcr_type])
            ->andFilterWhere(['like', 'rcr_subject', $this->rcr_subject]);

        return $dataProvider;
    }
}
