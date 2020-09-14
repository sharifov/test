<?php

namespace sales\model\phoneLine\phoneLinePhoneNumber\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber;

class PhoneLinePhoneNumberSearch extends PhoneLinePhoneNumber
{
    public function rules(): array
    {
        return [
            ['plpn_created_dt', 'safe'],

            ['plpn_created_user_id', 'integer'],

            ['plpn_default', 'integer'],

            ['plpn_enabled', 'integer'],

            ['plpn_line_id', 'integer'],

            ['plpn_pl_id', 'integer'],

            ['plpn_settings_json', 'safe'],

            ['plpn_updated_dt', 'safe'],

            ['plpn_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

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

        $query->andFilterWhere(['like', 'plpn_settings_json', $this->plpn_settings_json]);

        return $dataProvider;
    }
}
