<?php

namespace src\model\clientChatCouchNote\entity;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use src\model\clientChatCouchNote\entity\ClientChatCouchNote;
use yii\db\Expression;

class ClientChatCouchNoteSearch extends ClientChatCouchNote
{
    public function rules(): array
    {
        return [
            ['cccn_cch_id', 'integer'],
            ['cccn_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['cccn_created_user_id', 'integer'],
            ['cccn_id', 'integer'],

            ['cccn_alias', 'string', 'max' => 50],
            ['cccn_rid', 'string', 'max' => 150],
            ['cccn_message', 'string', 'max' => 500],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'cccn_id' => SORT_DESC,
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
            'cccn_id' => $this->cccn_id,
            'cccn_cch_id' => $this->cccn_cch_id,
            'cccn_created_user_id' => $this->cccn_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'cccn_rid', $this->cccn_rid])
            ->andFilterWhere(['like', 'cccn_message', $this->cccn_message])
            ->andFilterWhere(['like', 'cccn_alias', $this->cccn_alias]);

        if ($this->cccn_created_dt) {
            $query->andWhere(new Expression(
                'DATE(cccn_created_dt) = :created_dt',
                [':created_dt' => date('Y-m-d', strtotime($this->cccn_created_dt))]
            ));
        }

        return $dataProvider;
    }
}
