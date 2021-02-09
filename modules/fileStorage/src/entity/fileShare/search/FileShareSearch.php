<?php

namespace modules\fileStorage\src\entity\fileShare\search;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use modules\fileStorage\src\entity\fileShare\FileShare;

class FileShareSearch extends FileShare
{
    public function rules(): array
    {
        return [
            ['fsh_code', 'string'],

            ['fsh_created_user_id', 'integer'],

            ['fsh_fs_id', 'integer'],

            ['fsh_id', 'integer'],

            ['fsh_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['fsh_expired_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = static::find()->with(['createdUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->fsh_created_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'fsh_created_dt', $this->fsh_created_dt, $user->timezone);
        }
        if ($this->fsh_expired_dt) {
            \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'fsh_expired_dt', $this->fsh_expired_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'fsh_id' => $this->fsh_id,
            'fsh_fs_id' => $this->fsh_fs_id,
            'fsh_created_user_id' => $this->fsh_created_user_id,
        ]);

        $query->andFilterWhere(['ilike', 'fsh_code', $this->fsh_code]);

        return $dataProvider;
    }
}
