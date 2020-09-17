<?php

namespace sales\model\call\entity\callCommand\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\call\entity\callCommand\CallGatherSwitch;

/**
 * CallGatherSwitchSearch represents the model behind the search form of `sales\model\call\entity\callCommand\CallGatherSwitch`.
 */
class CallGatherSwitchSearch extends CallGatherSwitch
{

    public function rules(): array
    {
        return [
            [['cgs_ccom_id', 'cgs_step', 'cgs_case', 'cgs_exec_ccom_id'], 'integer'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CallGatherSwitch::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cgs_ccom_id' => $this->cgs_ccom_id,
            'cgs_step' => $this->cgs_step,
            'cgs_case' => $this->cgs_case,
            'cgs_exec_ccom_id' => $this->cgs_exec_ccom_id,
        ]);

        return $dataProvider;
    }
}
