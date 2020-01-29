<?php

namespace modules\offer\src\entities\offerSendLog\search;

use common\models\Employee;
use modules\offer\src\entities\offerSendLog\OfferSendLog;
use modules\offer\src\entities\offerSendLog\OfferSendLogType;
use sales\helpers\query\QueryHelper;
use yii\data\ActiveDataProvider;

class OfferSendLogSearch extends OfferSendLog
{
    public function rules(): array
    {
        return [
            ['ofsndl_id', 'integer'],
            ['ofsndl_id', 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['ofsndl_id' => 'ofsndl_id']],

            ['ofsndl_type_id', 'integer'],
            ['ofsndl_type_id', 'in', 'range' => array_keys(OfferSendLogType::getList())],

            ['ofsndl_send_to', 'string', 'max' => 160],

            ['ofsndl_created_user_id', 'integer'],
            ['ofsndl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ofsndl_created_user_id' => 'id']],

            ['ofsndl_created_dt', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params, Employee $user, int $offerId): ActiveDataProvider
    {
        $query = self::find()->with(['createdUser', 'offer']);

        $query->andWhere(['ofsndl_offer_id' => $offerId]);

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
            'ofsndl_type_id' => $this->ofsndl_type_id,
            'ofsndl_created_user_id' => $this->ofsndl_created_user_id,
        ]);

        $query->andFilterWhere(['like', 'ofsndl_send_to', $this->ofsndl_send_to]);

        return $dataProvider;
    }
}
