<?php

namespace sales\model\phoneLine\userPersonalPhoneNumber\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\phoneLine\userPersonalPhoneNumber\entity\UserPersonalPhoneNumber;

class UserPersonalPhoneNumberSearch extends UserPersonalPhoneNumber
{
    public function rules(): array
    {
        return [
            ['upn_approved', 'integer'],

            ['upn_created_dt', 'safe'],

            ['upn_created_user_id', 'integer'],

            ['upn_enabled', 'integer'],

            ['upn_id', 'integer'],

            ['upn_phone_number', 'safe'],

            ['upn_title', 'safe'],

            ['upn_updated_dt', 'safe'],

            ['upn_updated_user_id', 'integer'],

            ['upn_user_id', 'integer'],
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
            'upn_id' => $this->upn_id,
            'upn_user_id' => $this->upn_user_id,
            'upn_approved' => $this->upn_approved,
            'upn_enabled' => $this->upn_enabled,
            'upn_created_user_id' => $this->upn_created_user_id,
            'upn_updated_user_id' => $this->upn_updated_user_id,
            'date_format(upn_created_dt, "%Y-%m-%d")' => $this->upn_created_dt,
            'date_format(upn_updated_dt, "%Y-%m-%d")' => $this->upn_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'upn_phone_number', $this->upn_phone_number])
            ->andFilterWhere(['like', 'upn_title', $this->upn_title]);

        return $dataProvider;
    }
}
