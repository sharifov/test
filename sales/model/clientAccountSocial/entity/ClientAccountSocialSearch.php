<?php

namespace sales\model\clientAccountSocial\entity;

use yii\data\ActiveDataProvider;
use sales\model\clientAccountSocial\entity\ClientAccountSocial;
use yii\db\Expression;

class ClientAccountSocialSearch extends ClientAccountSocial
{
    public function rules(): array
    {
        return [
            ['cas_ca_id', 'integer'],
            ['cas_created_dt', 'datetime', 'format' => 'php:Y-m-d'],
            ['cas_identity', 'string', 'max' => 255],
            ['cas_type_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cas_ca_id' => SORT_DESC,
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
            'cas_ca_id' => $this->cas_ca_id,
            'cas_type_id' => $this->cas_type_id,
        ]);

        $query->andFilterWhere(['like', 'cas_identity', $this->cas_identity]);

        if ($this->cas_created_dt) {
            $query->andWhere(new Expression(
                'DATE(cas_created_dt) = :created_dt',
                [':created_dt' => date('Y-m-d', strtotime($this->cas_created_dt))]
            ));
        }

        return $dataProvider;
    }
}
