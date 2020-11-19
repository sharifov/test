<?php

namespace sales\model\clientAccountSocial\entity;

use yii\data\ActiveDataProvider;
use sales\model\clientAccountSocial\entity\ClientAccountSocial;

class ClientAccountSocialSearch extends ClientAccountSocial
{
    public function rules(): array
    {
        return [
            ['cas_ca_id', 'integer'],

            ['cas_created_dt', 'safe'],

            ['cas_identity', 'safe'],

            ['cas_type_id', 'integer'],
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
            'cas_ca_id' => $this->cas_ca_id,
            'cas_type_id' => $this->cas_type_id,
            'cas_created_dt' => $this->cas_created_dt,
        ]);

        $query->andFilterWhere(['like', 'cas_identity', $this->cas_identity]);

        return $dataProvider;
    }
}
