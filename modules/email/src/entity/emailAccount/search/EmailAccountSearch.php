<?php

namespace modules\email\src\entity\emailAccount\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use modules\email\src\entity\emailAccount\EmailAccount;

class EmailAccountSearch extends EmailAccount
{
    public function rules(): array
    {
        return [
            ['ea_active', 'boolean'],

            ['ea_created_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ea_created_user_id', 'integer'],

            ['ea_email', 'string'],

            ['ea_gmail_command', 'in', 'range' => array_keys(self::GMAIL_COMMAND_LIST)],

            ['ea_gmail_token', 'string'],

            ['ea_id', 'integer'],

            ['ea_imap_settings', 'string'],

//            ['ea_options', 'string'],

            ['ea_protocol', 'in', 'range' => array_keys(EmailAccount::PROTOCOL_LIST)],

            ['ea_updated_dt', 'date', 'format' => 'php:Y-m-d'],

            ['ea_updated_user_id', 'integer'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
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

        if ($this->ea_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'ea_created_dt', $this->ea_created_dt, $user->timezone);
        }
        if ($this->ea_updated_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'ea_updated_dt', $this->ea_updated_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'ea_id' => $this->ea_id,
            'ea_protocol' => $this->ea_protocol,
            'ea_gmail_command' => $this->ea_gmail_command,
            'ea_active' => $this->ea_active,
            'ea_created_user_id' => $this->ea_created_user_id,
            'ea_updated_user_id' => $this->ea_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'ea_email', $this->ea_email])
            ->andFilterWhere(['like', 'ea_imap_settings', $this->ea_imap_settings])
            ->andFilterWhere(['like', 'ea_gmail_token', $this->ea_gmail_token])
//            ->andFilterWhere(['like', 'ea_options', $this->ea_options])
        ;

        return $dataProvider;
    }
}
