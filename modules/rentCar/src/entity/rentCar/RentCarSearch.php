<?php

namespace modules\rentCar\src\entity\rentCar;

use yii\data\ActiveDataProvider;
use modules\rentCar\src\entity\rentCar\RentCar;
use yii\db\Expression;

class RentCarSearch extends RentCar
{
    public function rules(): array
    {
        return [
            ['prc_created_user_id', 'integer'],

            ['prc_drop_off_time', 'safe'],

            ['prc_id', 'integer'],

            [['prc_pick_up_code', 'prc_drop_off_code'], 'string', 'max' => 10],

            ['prc_pick_up_time', 'safe'],

            ['prc_product_id', 'integer'],

            ['prc_updated_user_id', 'integer'],

            [['prc_created_dt', 'prc_pick_up_date', 'prc_drop_off_date', 'prc_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['prc_request_hash_key', 'string', 'max' => 32],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['prc_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'prc_id' => $this->prc_id,
            'prc_product_id' => $this->prc_product_id,
            'prc_pick_up_time' => $this->prc_pick_up_time,
            'prc_drop_off_time' => $this->prc_drop_off_time,
            'prc_created_user_id' => $this->prc_created_user_id,
            'prc_updated_user_id' => $this->prc_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'prc_pick_up_code', $this->prc_pick_up_code])
            ->andFilterWhere(['like', 'prc_drop_off_code', $this->prc_drop_off_code])
            ->andFilterWhere(['like', 'prc_request_hash_key', $this->prc_request_hash_key]);

        if ($this->prc_pick_up_date) {
            $query->andWhere(new Expression(
                'DATE(prc_pick_up_date) = :prc_pick_up_date',
                [':prc_pick_up_date' => date('Y-m-d', strtotime($this->prc_pick_up_date))]
            ));
        }
        if ($this->prc_drop_off_date) {
            $query->andWhere(new Expression(
                'DATE(prc_drop_off_date) = :prc_drop_off_date',
                [':prc_drop_off_date' => date('Y-m-d', strtotime($this->prc_drop_off_date))]
            ));
        }
        if ($this->prc_created_dt) {
            $query->andWhere(new Expression(
                'DATE(prc_created_dt) = :search_date',
                [':search_date' => date('Y-m-d', strtotime($this->prc_created_dt))]
            ));
        }
        if ($this->prc_updated_dt) {
            $query->andWhere(new Expression(
                'DATE(prc_updated_dt) = :search_date',
                [':search_date' => date('Y-m-d', strtotime($this->prc_updated_dt))]
            ));
        }

        return $dataProvider;
    }
}
