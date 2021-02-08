<?php

namespace sales\model\userClientChatData\entity;

use yii\data\ActiveDataProvider;
use sales\model\userClientChatData\entity\UserClientChatData;
use yii\db\Expression;

class UserClientChatDataSearch extends UserClientChatData
{
    public function rules(): array
    {
        return [
            ['uccd_created_user_id', 'integer'],
            ['uccd_employee_id', 'integer'],
            ['uccd_active', 'boolean'],
            ['uccd_id', 'integer'],
            ['uccd_updated_user_id', 'integer'],
            [['uccd_created_dt', 'uccd_updated_dt', 'uccd_token_expired'], 'date', 'format' => 'php:Y-m-d'],
            [['uccd_auth_token'], 'string', 'max' => 50],
            [['uccd_rc_user_id'], 'string', 'max' => 20],
            [['uccd_name'], 'string', 'max' => 255],
            [['uccd_username'], 'string', 'max' => 50],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['uccd_id' => SORT_DESC]],
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
            'uccd_id' => $this->uccd_id,
            'uccd_employee_id' => $this->uccd_employee_id,
            'uccd_active' => $this->uccd_active,
            'uccd_created_user_id' => $this->uccd_created_user_id,
            'uccd_updated_user_id' => $this->uccd_updated_user_id,
            'uccd_auth_token' => $this->uccd_auth_token,
            'uccd_rc_user_id' => $this->uccd_rc_user_id,
        ]);

        $query->andFilterWhere(['like', 'uccd_username', $this->uccd_username])
            ->andFilterWhere(['like', 'uccd_name', $this->uccd_name]);

        if ($this->uccd_created_dt) {
            $query->andWhere(new Expression(
                'DATE(uccd_created_dt) = :created_dt',
                [':created_dt' => date('Y-m-d', strtotime($this->uccd_created_dt))]
            ));
        }
        if ($this->uccd_updated_dt) {
            $query->andWhere(new Expression(
                'DATE(uccd_updated_dt) = :updated_dt',
                [':updated_dt' => date('Y-m-d', strtotime($this->uccd_updated_dt))]
            ));
        }
        if ($this->uccd_token_expired) {
            $query->andWhere(new Expression(
                'DATE(uccd_token_expired) = :token_expired',
                [':token_expired' => date('Y-m-d', strtotime($this->uccd_token_expired))]
            ));
        }
        return $dataProvider;
    }
}
