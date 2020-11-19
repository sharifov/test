<?php

namespace sales\model\clientAccount\entity;

use yii\data\ActiveDataProvider;
use sales\model\clientAccount\entity\ClientAccount;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class ClientAccountSearch
 */
class ClientAccountSearch extends ClientAccount
{
    public function rules(): array
    {
        $parentRules = parent::rules();
        unset(
            $parentRules['ca_uuid_required'],
            $parentRules['ca_username_required'],
            $parentRules['ca_hid_required']
        );

        $rules = [
            [['ca_created_dt', 'ca_updated_dt'], 'datetime', 'format' => 'php:Y-m-d'],
        ];

        return ArrayHelper::merge($parentRules, $rules);
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
             'sort' => [
                'defaultOrder' => [
                    'ca_id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ca_id' => $this->ca_id,
            'ca_project_id' => $this->ca_project_id,
            'ca_hid' => $this->ca_hid,
            'ca_dob' => $this->ca_dob,
            'ca_gender' => $this->ca_gender,
            'ca_subscription' => $this->ca_subscription,
            'ca_enabled' => $this->ca_enabled,
            'ca_origin_created_dt' => $this->ca_origin_created_dt,
            'ca_origin_updated_dt' => $this->ca_origin_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ca_uuid', $this->ca_uuid])
            ->andFilterWhere(['like', 'ca_username', $this->ca_username])
            ->andFilterWhere(['like', 'ca_first_name', $this->ca_first_name])
            ->andFilterWhere(['like', 'ca_middle_name', $this->ca_middle_name])
            ->andFilterWhere(['like', 'ca_last_name', $this->ca_last_name])
            ->andFilterWhere(['like', 'ca_nationality_country_code', $this->ca_nationality_country_code])
            ->andFilterWhere(['like', 'ca_phone', $this->ca_phone])
            ->andFilterWhere(['like', 'ca_language_id', $this->ca_language_id])
            ->andFilterWhere(['like', 'ca_currency_code', $this->ca_currency_code])
            ->andFilterWhere(['like', 'ca_timezone', $this->ca_timezone])
            ->andFilterWhere(['like', 'ca_created_ip', $this->ca_created_ip]);

        if ($this->ca_created_dt) {
            $query->andWhere(new Expression(
                'DATE(ca_created_dt) = :created_dt',
                [':created_dt' => date('Y-m-d', strtotime($this->ca_created_dt))]
            ));
        }

        return $dataProvider;
    }
}
