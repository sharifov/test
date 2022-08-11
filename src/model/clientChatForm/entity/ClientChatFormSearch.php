<?php

namespace src\model\clientChatForm\entity;

use yii\data\ActiveDataProvider;
use src\model\clientChatForm\entity\ClientChatForm;
use yii\db\Expression;

class ClientChatFormSearch extends ClientChatForm
{
    public function rules(): array
    {
        return [
            [['ccf_created_dt', 'ccf_updated_dt'], 'date', 'format' => 'php:Y-m-d'],

            ['ccf_created_user_id', 'integer'],

            ['ccf_dataform_json', 'string', 'max' => 200],

            ['ccf_enabled', 'boolean'],
            ['ccf_is_system', 'boolean'],

            ['ccf_id', 'integer'],

            ['ccf_key', 'string', 'max' => 50],

            ['ccf_name', 'string', 'max' => 50],

            ['ccf_project_id', 'integer'],

            ['ccf_updated_user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'ccf_id' => SORT_DESC
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ccf_id' => $this->ccf_id,
            'ccf_project_id' => $this->ccf_project_id,
            'ccf_enabled' => $this->ccf_enabled,
            'ccf_created_user_id' => $this->ccf_created_user_id,
            'ccf_updated_user_id' => $this->ccf_updated_user_id,
            'ccf_is_system' => $this->ccf_is_system,
        ]);

        $query->andFilterWhere(['like', 'ccf_key', $this->ccf_key])
            ->andFilterWhere(['like', 'ccf_name', $this->ccf_name])
            ->andFilterWhere(['like', 'ccf_dataform_json', $this->ccf_dataform_json]);

        if ($this->ccf_created_dt) {
            $query->andWhere(new Expression(
                'DATE(ccf_created_dt) = :created_dt',
                [':created_dt' => date('Y-m-d', strtotime($this->ccf_created_dt))]
            ));
        }

        return $dataProvider;
    }
}
