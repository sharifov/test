<?php

namespace src\model\contactPhoneList\entity;

use src\model\contactPhoneData\entity\ContactPhoneData;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class ContactPhoneListSearch
 */
class ContactPhoneListSearch extends ContactPhoneList
{
    public $cpd_key;
    public $cpd_value;

    public function rules(): array
    {
        return [
            ['cpl_id', 'integer'],
            ['cpl_phone_number', 'safe'],
            ['cpl_title', 'string'],
            ['cpl_uid', 'string'],
            [['cpl_created_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['cpd_key', 'string'],
            ['cpd_value', 'string'],
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

        if (!empty($this->cpd_key) || !empty($this->cpd_value)) {
            $query->leftJoin(ContactPhoneData::tableName(), 'cpl_id = cpd_cpl_id');
            if (!empty($this->cpd_key)) {
                $query->andWhere(['cpd_key' => $this->cpd_key]);
            }
            if (!empty($this->cpd_value)) {
                $query->andWhere(['cpd_value' => $this->cpd_value]);
            }
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

    public function attributeLabels(): array
    {
        $labels = [
            'cpd_key' => 'Data Key',
            'cpd_value' => 'Data Value',
        ];
        return ArrayHelper::merge(parent::attributeLabels(), $labels);
    }
}
