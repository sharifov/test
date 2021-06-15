<?php

namespace sales\model\visitorSubscription\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\visitorSubscription\entity\VisitorSubscription;

class VisitorSubscriptionSearch extends VisitorSubscription
{
    public function rules(): array
    {
        return [
            ['vs_created_dt', 'safe'],

            ['vs_enabled', 'integer'],

            ['vs_expired_date', 'safe'],

            ['vs_id', 'integer'],

            ['vs_subscription_uid', 'safe'],

            ['vs_type_id', 'integer'],

            ['vs_updated_dt', 'safe'],
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
            'vs_id' => $this->vs_id,
            'vs_type_id' => $this->vs_type_id,
            'vs_enabled' => $this->vs_enabled,
            'vs_expired_date' => $this->vs_expired_date,
            'date(vs_created_dt)' => $this->vs_created_dt,
            'date(vs_updated_dt)' => $this->vs_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'vs_subscription_uid', $this->vs_subscription_uid]);

        return $dataProvider;
    }
}
