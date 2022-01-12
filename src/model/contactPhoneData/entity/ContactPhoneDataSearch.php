<?php

namespace src\model\contactPhoneData\entity;

use src\model\contactPhoneList\entity\ContactPhoneList;
use yii\data\ActiveDataProvider;
use src\model\contactPhoneData\entity\ContactPhoneData;

/**
 * Class ContactPhoneDataSearch
 * @property string|null $phone
 */
class ContactPhoneDataSearch extends ContactPhoneData
{
    public $phone;

    public function rules(): array
    {
        return [
            ['cpd_cpl_id', 'integer'],
            [['cpd_created_dt', 'cpd_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            ['cpd_key', 'string'],
            ['cpd_value', 'string'],
            ['phone', 'string'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cpd_cpl_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cpd_cpl_id' => $this->cpd_cpl_id,
            'DATE(cpd_created_dt)' => $this->cpd_created_dt,
            'DATE(cpd_updated_dt)' => $this->cpd_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cpd_key', $this->cpd_key])
            ->andFilterWhere(['like', 'cpd_value', $this->cpd_value]);

        if ($this->phone) {
            $query->innerJoin(ContactPhoneList::tableName(), 'cpd_cpl_id = cpl_id')
                ->andWhere(['cpl_phone_number' => $this->phone]);
        }

        return $dataProvider;
    }
}
