<?php

namespace sales\model\clientChat\entity\actionReason\search;

use yii\data\ActiveDataProvider;
use sales\model\clientChat\entity\actionReason\ClientChatActionReason;

class actionReasonSearch extends ClientChatActionReason
{
    public function rules(): array
    {
        return [
            ['ccar_action_id', 'integer'],

            ['ccar_comment_required', 'integer'],

            ['ccar_created_dt', 'safe'],

            ['ccar_created_user_id', 'integer'],

            ['ccar_enabled', 'integer'],

            ['ccar_id', 'integer'],

            ['ccar_key', 'safe'],

            ['ccar_name', 'safe'],

            ['ccar_updated_dt', 'safe'],

            ['ccar_updated_user_id', 'integer'],
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
            'ccar_id' => $this->ccar_id,
            'ccar_action_id' => $this->ccar_action_id,
            'ccar_enabled' => $this->ccar_enabled,
            'ccar_comment_required' => $this->ccar_comment_required,
            'ccar_created_user_id' => $this->ccar_created_user_id,
            'ccar_updated_user_id' => $this->ccar_updated_user_id,
            'date_format(ccar_created_dt, "%Y-%m-%d")' => $this->ccar_created_dt,
            'date_format(ccar_updated_dt, "%Y-%m-%d")' => $this->ccar_updated_dt,
        ]);

        $query->andFilterWhere(['like', 'ccar_key', $this->ccar_key])
            ->andFilterWhere(['like', 'ccar_name', $this->ccar_name]);

        return $dataProvider;
    }
}
