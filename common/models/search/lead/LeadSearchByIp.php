<?php

namespace common\models\search\lead;

use common\models\Lead;
use sales\access\EmployeeProjectAccess;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

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
            ['requestIp', 'string']
        ];
    }

    /**
     * @param $params
     * @param int $userId
     * @return ActiveDataProvider
     */
    public function search($params, int $userId): ActiveDataProvider
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
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['request_ip' => $this->requestIp]);

        $query->andWhere(['project_id' => array_keys(EmployeeProjectAccess::getProjects($userId))]);

        return $dataProvider;
    }

    /**
     * @param string|null $requestIp
     * @param int $userId
     * @return int
     */
    public static function count(?string $requestIp, int $userId): int
    {
        if (!$requestIp) {
            return 0;
        }

        $query = (new Query())->from(Lead::tableName());

        $query->andWhere(['request_ip' => $requestIp]);

        $query->andWhere(['project_id' => array_keys(EmployeeProjectAccess::getProjects($userId))]);

        return $query->count();
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
