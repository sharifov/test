<?php

namespace src\model\coupon\entity\couponUserAction;

use yii\data\ActiveDataProvider;
use src\model\coupon\entity\couponUserAction\CouponUserAction;

class CouponUserActionSearch extends CouponUserAction
{
    public function rules(): array
    {
        return [
            ['cuu_action_id', 'integer'],

            ['cuu_api_user_id', 'integer'],

            ['cuu_coupon_id', 'integer'],

            [['cuu_created_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['cuu_description', 'safe'],

            ['cuu_id', 'integer'],

            ['cuu_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cuu_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cuu_id' => $this->cuu_id,
            'cuu_coupon_id' => $this->cuu_coupon_id,
            'cuu_action_id' => $this->cuu_action_id,
            'cuu_api_user_id' => $this->cuu_api_user_id,
            'cuu_user_id' => $this->cuu_user_id,
            'CATE(cuu_created_dt)' => $this->cuu_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cuu_description', $this->cuu_description]);

        return $dataProvider;
    }
}
