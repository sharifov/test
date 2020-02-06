<?php

namespace common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UserProductType;

/**
 * UserProductTypeSearch represents the model behind the search form of `common\models\UserProductType`.
 */
class UserProductTypeSearch extends UserProductType
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['upt_user_id', 'upt_product_type_id', 'upt_product_enabled', 'upt_created_user_id', 'upt_updated_user_id'], 'integer'],
            [['upt_commission_percent'], 'number'],
            [['upt_created_dt', 'upt_updated_dt'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UserProductType::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'upt_user_id' => $this->upt_user_id,
            'upt_product_type_id' => $this->upt_product_type_id,
            'upt_commission_percent' => $this->upt_commission_percent,
            'upt_product_enabled' => $this->upt_product_enabled,
            'upt_created_user_id' => $this->upt_created_user_id,
            'upt_updated_user_id' => $this->upt_updated_user_id,
            'upt_created_dt' => $this->upt_created_dt,
            'upt_updated_dt' => $this->upt_updated_dt,
        ]);

        return $dataProvider;
    }
}
