<?php

namespace modules\offer\src\entities\offerSendLog\search;

use common\models\Employee;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerSendLog\OfferSendLog;
use modules\offer\src\entities\offerSendLog\OfferSendLogType;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

class OfferSendLogCrudSearch extends OfferSendLog
{
    public function rules(): array
    {
        return [
            ['ofsndl_id', 'integer'],
            ['ofsndl_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['ofsndl_id' => 'ofsndl_id']],

            ['ofsndl_offer_id', 'integer'],
            ['ofsndl_offer_id', 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['ofsndl_offer_id' => 'of_id']],

            ['ofsndl_type_id', 'integer'],
            ['ofsndl_type_id', 'in', 'range' => array_keys(OfferSendLogType::getList())],

            ['ofsndl_send_to', 'string', 'max' => 160],

            ['ofsndl_created_user_id', 'integer'],
            ['ofsndl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ofsndl_created_user_id' => 'id']],

            ['ofsndl_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user): ActiveDataProvider
    {
        $query = self::find()->with(['createdUser', 'offer']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->ofsndl_created_dt) {
            QueryHelper::dayEqualByUserTZ($query, 'ofsndl_created_dt', $this->ofsndl_created_dt, $user->timezone);
        }

        $query->andFilterWhere([
            'ofsndl_id' => $this->ofsndl_id,
            'ofsndl_offer_id' => $this->ofsndl_offer_id,
            'ofsndl_type_id' => $this->ofsndl_type_id,
            'ofsndl_created_user_id' => $this->ofsndl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'ofsndl_send_to', $this->ofsndl_send_to]);

        return $dataProvider;
    }
}
