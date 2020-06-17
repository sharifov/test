<?php

namespace sales\model\callNote\entity\search;

use yii\data\ActiveDataProvider;
use sales\model\callNote\entity\CallNote;

class CallNoteSearch extends CallNote
{
    public function rules(): array
    {
        return [
            ['cn_call_id', 'integer'],

            ['cn_created_dt', 'safe'],

            ['cn_created_user_id', 'integer'],

            ['cn_id', 'integer'],

            ['cn_note', 'safe'],

            ['cn_updated_dt', 'safe'],

            ['cn_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'cn_id' => SORT_DESC
				]
			]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cn_id' => $this->cn_id,
            'cn_call_id' => $this->cn_call_id,
			'date_format(cn_created_dt, "%Y-%m-%d")' => $this->cn_created_dt,
			'date_format(cn_updated_dt, "%Y-%m-%d")' => $this->cn_updated_dt,
            'cn_created_user_id' => $this->cn_created_user_id,
            'cn_updated_user_id' => $this->cn_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'cn_note', $this->cn_note]);

        return $dataProvider;
    }
}
