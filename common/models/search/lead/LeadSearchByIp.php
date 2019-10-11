<?php

namespace common\models\search\lead;

use common\models\Lead;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * Class LeadSearchByIp
 *
 * @property $requestIp
 */
class LeadSearchByIp extends Model
{
    public $requestIp;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['requestIp', 'required'],
            ['requestIp', 'safe']
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Lead::find()->with('client', 'client.clientEmails', 'client.clientPhones', 'leadFlightSegments');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['l_last_action_dt' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            VarDumper::dump($this->errors);
             $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['request_ip' => $this->requestIp]);

        return $dataProvider;
    }

    /**
     * @param string|null $requestIp
     * @return int
     */
    public static function count(?string $requestIp): int
    {
        if (!$requestIp) {
            return 0;
        }
        return (new Query())->from(Lead::tableName())->andWhere(['request_ip' => $requestIp])->count();
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public static function getShortName(): string
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

}
