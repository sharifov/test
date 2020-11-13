<?php

namespace sales\model\phoneLine\phoneLinePhoneNumber\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber;

class PhoneLinePhoneNumberSearch extends PhoneLinePhoneNumber
{
    public $phoneNumber;

    public function rules(): array
    {
        return [
            [['plpn_created_dt', 'plpn_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['plpn_created_user_id', 'integer'],

            ['plpn_default', 'integer'],

            ['plpn_enabled', 'integer'],

            ['plpn_line_id', 'integer'],

            ['plpn_pl_id', 'integer'],

            ['plpn_settings_json', 'safe'],

            ['plpn_updated_user_id', 'integer'],
            ['phoneNumber', 'match', 'pattern' => '/^[+]\d*$/i'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find()->joinWith('plpnPl');
        $query->select([
            'plpn_line_id',
            'plpn_pl_id',
            'plpn_default',
            'plpn_enabled',
            'plpn_created_user_id',
            'plpn_updated_user_id',
            'plpn_created_dt',
            'plpn_updated_dt',
            'pl_phone_number'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['phoneNumber'] = [
            'asc' => ['pl_phone_number' => SORT_ASC],
            'desc' => ['pl_phone_number' => SORT_DESC],
        ];

        $query->andFilterWhere([
            'plpn_line_id' => $this->plpn_line_id,
            'plpn_pl_id' => $this->plpn_pl_id,
            'plpn_default' => $this->plpn_default,
            'plpn_enabled' => $this->plpn_enabled,
            'plpn_created_user_id' => $this->plpn_created_user_id,
            'plpn_updated_user_id' => $this->plpn_updated_user_id,
            'date_format(plpn_created_dt, "%Y-%m-%d")' => $this->plpn_created_dt,
            'date_format(plpn_updated_dt, "%Y-%m-%d")' => $this->plpn_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'plpn_settings_json', $this->plpn_settings_json])
            ->andFilterWhere(['like', 'pl_phone_number', $this->phoneNumber]);

        return $dataProvider;
    }
}
