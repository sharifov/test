<?php

namespace modules\offer\src\entities\offer\search;

use common\models\Employee;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;
use modules\offer\src\entities\offer\Offer;

/**
 * Class OfferCrudSearch
 */
class OfferCrudSearch extends Offer
{
    public function rules(): array
    {
        return [
            [['of_id', 'of_lead_id', 'of_status_id', 'of_owner_user_id', 'of_created_user_id', 'of_updated_user_id'], 'integer'],
            [['of_gid', 'of_uid', 'of_name'], 'safe'],

            ['of_created_dt', 'date', 'format' => 'php:Y-m-d'],
            ['of_updated_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['ofLead', 'ofOwnerUser', 'ofCreatedUser', 'ofUpdatedUser']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->of_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'of_created_dt', $this->of_created_dt, $user->timezone);
        }

        if ($this->of_updated_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'of_updated_dt', $this->of_updated_dt, $user->timezone);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'of_id' => $this->of_id,
            'of_lead_id' => $this->of_lead_id,
            'of_status_id' => $this->of_status_id,
            'of_owner_user_id' => $this->of_owner_user_id,
            'of_created_user_id' => $this->of_created_user_id,
            'of_updated_user_id' => $this->of_updated_user_id,
        ]);

        $query->andFilterWhere(['like', 'of_gid', $this->of_gid])
            ->andFilterWhere(['like', 'of_uid', $this->of_uid])
            ->andFilterWhere(['like', 'of_name', $this->of_name]);

        return $dataProvider;
    }
}
