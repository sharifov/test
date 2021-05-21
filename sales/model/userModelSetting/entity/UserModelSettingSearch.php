<?php

namespace sales\model\userModelSetting\entity;

use common\models\Employee;
use yii\data\ActiveDataProvider;
use sales\model\userModelSetting\entity\UserModelSetting;
use yii\db\Expression;

/**
 * Class UserModelSettingSearch
 */
class UserModelSettingSearch extends UserModelSetting
{
    public function rules(): array
    {
        return [
            ['ums_class', 'string', 'max' => 255],

            [['ums_name', 'ums_key'], 'string', 'max' => 50],

            [['ums_id', 'ums_per_page', 'ums_type'], 'integer'],

            ['ums_enabled', 'boolean'],

            ['ums_user_id', 'integer'],
            ['ums_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ums_user_id' => 'id']],

            [['ums_created_dt', 'ums_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ums_id' => SORT_DESC]],
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
            'ums_id' => $this->ums_id,
            'ums_user_id' => $this->ums_user_id,
            'ums_per_page' => $this->ums_per_page,
            'ums_enabled' => $this->ums_enabled,
            'ums_type' => $this->ums_type,
        ]);

        $query->andFilterWhere(['like', 'ums_key', $this->ums_key])
            ->andFilterWhere(['like', 'ums_name', $this->ums_type])
            ->andFilterWhere(['like', 'ums_class', $this->ums_class]);


        if ($this->ums_created_dt) {
            $query->andWhere(new Expression(
                'DATE(ums_created_dt) = :created_dt',
                [':created_dt' => date('Y-m-d', strtotime($this->ums_created_dt))]
            ));
        }
        if ($this->ums_updated_dt) {
            $query->andWhere(new Expression(
                'DATE(ums_updated_dt ) = :updated_dt',
                [':updated_dt' => date('Y-m-d', strtotime($this->ums_updated_dt))]
            ));
        }
        return $dataProvider;
    }
}
