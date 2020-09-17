<?php

namespace sales\model\call\entity\callCommand\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use sales\model\call\entity\callCommand\PhoneLineCommand;

/**
 * PhoneLineCommandSearch represents the model behind the search form of `sales\model\call\entity\callCommand\PhoneLineCommand`.
 */
class PhoneLineCommandSearch extends PhoneLineCommand
{

    public function rules(): array
    {
        return [
            [['plc_id', 'plc_line_id', 'plc_ccom_id', 'plc_sort_order', 'plc_created_user_id'], 'integer'],
            [['plc_created_dt'], 'safe'],
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
        $query = PhoneLineCommand::find();

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
            'plc_id' => $this->plc_id,
            'plc_line_id' => $this->plc_line_id,
            'plc_ccom_id' => $this->plc_ccom_id,
            'plc_sort_order' => $this->plc_sort_order,
            'plc_created_user_id' => $this->plc_created_user_id,
            'plc_created_dt' => $this->plc_created_dt,
        ]);

        return $dataProvider;
    }
}
