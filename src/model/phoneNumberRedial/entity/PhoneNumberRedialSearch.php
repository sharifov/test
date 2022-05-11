<?php

namespace src\model\phoneNumberRedial\entity;

use src\model\phoneList\entity\PhoneList;
use src\model\phoneNumberRedial\entity\Scopes\PhoneNumberRedialQuery;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class PhoneNumberRedialSearch extends PhoneNumberRedial
{
    public $searchPatternByPhone;

    public function rules(): array
    {
        return [
            ['pnr_created_dt', 'safe'],

            ['pnr_enabled', 'integer'],

            ['pnr_id', 'integer'],

            ['pnr_name', 'safe'],

            ['pnr_phone_pattern', 'safe'],

            ['pnr_pl_id', 'string'],

            ['pnr_priority', 'integer'],

            ['pnr_project_id', 'integer'],

            ['pnr_updated_dt', 'safe'],

            ['pnr_updated_user_id', 'integer'],

            ['searchPatternByPhone', 'string'],
            ['searchPatternByPhone', 'trim'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = static::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->with('phoneList');

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pnr_id' => $this->pnr_id,
            'pnr_project_id' => $this->pnr_project_id,
            'pnr_pl_id' => $this->pnr_pl_id,
            'pnr_enabled' => $this->pnr_enabled,
            'pnr_priority' => $this->pnr_priority,
            'date(pnr_created_dt)' => $this->pnr_created_dt,
            'date(pnr_updated_dt)' => $this->pnr_updated_dt,
            'pnr_updated_user_id' => $this->pnr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'pnr_phone_pattern', $this->pnr_phone_pattern])
            ->andFilterWhere(['like', 'pnr_name', $this->pnr_name]);

        if ($this->searchPatternByPhone) {
            $query->join('inner join', PhoneList::tableName(), 'pnr_pl_id = pl_id');
            $query->andWhere(new Expression(" :phone REGEXP CONCAT('^', pnr_phone_pattern, '$') = 1 "), [
                'phone' => $this->searchPatternByPhone
            ]);
        }

        return $dataProvider;
    }

    public function searchIds($params): array
    {
        $query = static::find();

        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
            return [];
        }
        $query->select('pnr_id');
        $this->filterQuery($query);

        return ArrayHelper::map($query->asArray()->all(), 'pnr_id', 'pnr_id');
    }

    /**
     * @param PhoneNumberRedialQuery $query
     * @return ActiveQuery
     */
    private function filterQuery(PhoneNumberRedialQuery $query): ActiveQuery
    {
        $query->andFilterWhere([
            'pnr_id' => $this->pnr_id,
            'pnr_project_id' => $this->pnr_project_id,
            'pnr_pl_id' => $this->pnr_pl_id,
            'pnr_enabled' => $this->pnr_enabled,
            'pnr_priority' => $this->pnr_priority,
            'date(pnr_created_dt)' => $this->pnr_created_dt,
            'date(pnr_updated_dt)' => $this->pnr_updated_dt,
            'pnr_updated_user_id' => $this->pnr_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'pnr_phone_pattern', $this->pnr_phone_pattern])
            ->andFilterWhere(['like', 'pnr_name', $this->pnr_name]);

        if ($this->searchPatternByPhone) {
            $query->join('inner join', PhoneList::tableName(), 'pnr_pl_id = pl_id');
            $query->andWhere(new Expression(" :phone REGEXP CONCAT('^', pnr_phone_pattern, '$') = 1 "), [
                'phone' => $this->searchPatternByPhone
            ]);
        }

        return $query;
    }
}
