<?php

namespace src\model\contactPhoneList\entity;

use yii\data\ActiveDataProvider;

class ContactPhoneListCrudSearch extends ContactPhoneList
{
    public function rules(): array
    {
        return [
            ['cpl_id', 'integer'],

            ['cpl_phone_number', 'safe'],

            ['cpl_title', 'safe'],

            ['cpl_uid', 'safe'],

            [['cpl_created_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cpl_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cpl_id' => $this->cpl_id,
            'DATE(cpl_created_dt)' => $this->cpl_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cpl_phone_number', $this->cpl_phone_number])
            ->andFilterWhere(['like', 'cpl_uid', $this->cpl_uid])
            ->andFilterWhere(['like', 'cpl_title', $this->cpl_title]);

        return $dataProvider;
    }
}
