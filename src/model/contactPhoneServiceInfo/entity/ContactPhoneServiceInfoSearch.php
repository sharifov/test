<?php

namespace src\model\contactPhoneServiceInfo\entity;

use src\model\contactPhoneList\entity\ContactPhoneList;
use yii\data\ActiveDataProvider;
use src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;

/**
 * Class ContactPhoneServiceInfoSearch
 * @property string|null $phone
 */
class ContactPhoneServiceInfoSearch extends ContactPhoneServiceInfo
{
    public $phone;

    public function rules(): array
    {
        return [
            ['cpsi_cpl_id', 'integer'],

            ['cpsi_data_json', 'safe'],

            ['cpsi_service_id', 'integer'],

            [['cpsi_created_dt', 'cpsi_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['phone', 'string'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cpsi_cpl_id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cpsi_cpl_id' => $this->cpsi_cpl_id,
            'cpsi_service_id' => $this->cpsi_service_id,
            'DATE(cpsi_created_dt)' => $this->cpsi_created_dt,
            'DATE(cpsi_updated_dt)' => $this->cpsi_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'cpsi_data_json', $this->cpsi_data_json]);

        if ($this->phone) {
            $query->innerJoin(ContactPhoneList::tableName(), 'cpsi_cpl_id = cpl_id')
                ->andWhere(['cpl_phone_number' => $this->phone]);
        }

        return $dataProvider;
    }
}
