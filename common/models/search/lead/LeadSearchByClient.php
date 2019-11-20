<?php

namespace common\models\search\lead;

use common\models\Employee;
use common\models\Lead;
use sales\access\EmployeeProjectAccess;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class LeadSearchByClient
 *
 * @property $clientId
 */
class LeadSearchByClient extends Model
{

    public $clientId;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['clientId', 'required'],
            ['clientId', 'integer']
        ];
    }

    /**
     * @param $params
     * @param Employee $user
     * @return ActiveDataProvider
     */
    public function search($params, Employee $user): ActiveDataProvider
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

        if ($user->isAgent()) {
            $query->andWhere(['<>', Lead::tableName() . '.status', Lead::STATUS_PENDING]);
        }

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andWhere(['client_id' => $this->clientId]);

        $query->andWhere(['project_id' => array_keys(EmployeeProjectAccess::getProjects($user->id))]);

        return $dataProvider;
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
