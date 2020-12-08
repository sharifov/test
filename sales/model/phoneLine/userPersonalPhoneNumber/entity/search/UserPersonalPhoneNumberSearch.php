<?php

namespace sales\model\phoneLine\userPersonalPhoneNumber\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\phoneLine\userPersonalPhoneNumber\entity\UserPersonalPhoneNumber;

class UserPersonalPhoneNumberSearch extends UserPersonalPhoneNumber
{
    public $phoneNumber;

    public function rules(): array
    {
        return [
            ['upn_approved', 'integer'],

            [['upn_created_dt', 'upn_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['upn_created_user_id', 'integer'],

            ['upn_enabled', 'integer'],

            ['upn_id', 'integer'],

            ['upn_phone_number', 'safe'],

            ['upn_title', 'safe'],

            ['upn_updated_user_id', 'integer'],

            ['upn_user_id', 'integer'],
            ['phoneNumber', 'match', 'pattern' => '/^[+]\d*$/i'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = self::find();
        $query->joinWith('upnPhoneNumber');
        $query->select([
            'upn_id',
            'upn_user_id',
            'upn_title',
            'upn_approved',
            'upn_enabled',
            'upn_phone_number',
            'upn_created_user_id',
            'upn_updated_user_id',
            'upn_created_dt',
            'upn_updated_dt',
            'pl_phone_number'
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['phoneNumber'] = [
            'asc' => ['pl_phone_number' => SORT_ASC],
            'desc' => ['pl_phone_number' => SORT_DESC],
        ];

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
            ->andFilterWhere(['like', 'upn_title', $this->upn_title])
            ->andFilterWhere(['like', 'pl_phone_number', $this->phoneNumber]);

        return $dataProvider;
    }
}
