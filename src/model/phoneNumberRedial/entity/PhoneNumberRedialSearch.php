<?php

namespace src\model\phoneNumberRedial\entity;

use yii\data\ActiveDataProvider;
use src\model\phoneNumberRedial\entity\PhoneNumberRedial;

class PhoneNumberRedialSearch extends PhoneNumberRedial
{
    public function rules(): array
    {
        return [
            ['pnr_created_dt', 'safe'],

            ['pnr_enabled', 'integer'],

            ['pnr_id', 'integer'],

            ['pnr_name', 'safe'],

            ['pnr_phone_pattern', 'safe'],

            ['pnr_pl_id', 'integer'],

            ['pnr_priority', 'integer'],

            ['pnr_project_id', 'integer'],

            ['pnr_updated_dt', 'safe'],

            ['pnr_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->with('phoneList');

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pnr_id' => $this->pnr_id,
            'pnr_project_id' => $this->pnr_project_id,
            'pnr_pl_id' => $this->pnr_pl_id,
            'pnr_enabled' => $this->pnr_enabled,
            'pnr_priority' => $this->pnr_priority,
            'date(pnr_created_dt)' => $this->pnr_created_dt,
            'date(pnr_updated_dt)' => $this->pnr_updated_dt,
            'pnr_updated_user_id' => $this->pnr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'pnr_phone_pattern', $this->pnr_phone_pattern])
            ->andFilterWhere(['like', 'pnr_name', $this->pnr_name]);

        return $dataProvider;
    }
}
