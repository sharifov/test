<?php

namespace modules\offer\src\entities\offerStatusLog\search;

use common\models\Employee;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferStatus;
use modules\offer\src\entities\offerStatusLog\OfferStatusLog;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

class OfferStatusLogSearch extends OfferStatusLog
{
    public function rules(): array
    {
        return [
            ['osl_id', 'integer'],
            ['osl_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['osl_id' => 'osl_id']],

            ['osl_offer_id', 'integer'],
            ['osl_offer_id', 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['osl_offer_id' => 'of_id']],

            ['osl_start_status_id', 'integer'],
            ['osl_start_status_id', 'in', 'range' => array_keys(OfferStatus::getList())],

            ['osl_end_status_id', 'integer'],
            ['osl_end_status_id', 'in', 'range' => array_keys(OfferStatus::getList())],

            ['osl_start_dt', 'date', 'format' => 'php:Y-m-d'],

            ['osl_end_dt', 'date', 'format' => 'php:Y-m-d'],

            ['osl_duration', 'integer'],

            ['osl_description', 'string', 'max' => 255],

            ['osl_owner_user_id', 'integer'],
            ['osl_owner_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['osl_owner_user_id' => 'id']],

            ['osl_created_user_id', 'integer'],
            ['osl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['osl_created_user_id' => 'id']],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['createdUser', 'ownerUser', 'offer']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->osl_start_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'osl_start_dt', $this->osl_start_dt, $user->timezone);
        }

        if ($this->osl_end_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'osl_end_dt', $this->osl_end_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'osl_id' => $this->osl_id,
            'osl_offer_id' => $this->osl_offer_id,
            'osl_start_status_id' => $this->osl_start_status_id,
            'osl_end_status_id' => $this->osl_end_status_id,
            'osl_duration' => $this->osl_duration,
            'osl_owner_user_id' => $this->osl_owner_user_id,
            'osl_created_user_id' => $this->osl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'osl_description', $this->osl_description]);

        return $dataProvider;
    }
}
