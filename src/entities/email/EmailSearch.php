<?php

namespace src\entities\email;

use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\UserGroupAssign;
use src\auth\Auth;
use src\helpers\query\QueryHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * EmailSearch
 *
 * @property string $e_template_type_name
 */
class EmailSearch extends Email
{
    public $email_type_id;
    public $supervision_id;
    public $e_template_type_name;

    public $datetime_start;
    public $datetime_end;
    public $date_range;
    public const CREATE_TIME_START_DEFAULT_RANGE = '-6 days';

    public $communication_id;
    public $email_from;
    public $email_to;

    public function rules(): array
    {
        return [
            [['datetime_start', 'datetime_end', 'communication_id', 'email_from', 'email_to'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['e_id', 'e_project_id', 'e_type_id', 'e_is_deleted', 'e_status_id', 'e_created_user_id', 'supervision_id'], 'integer'],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $userTimezone = Auth::user()->userParams->up_timezone ?? 'UTC';
        $currentDate = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone($userTimezone));
        $this->date_range = ($currentDate->modify(self::CREATE_TIME_START_DEFAULT_RANGE))->format('Y-m-d') . ' 00:00:00 - ' . $currentDate->format('Y-m-d') . ' 23:59:59';
    }



    public function attributeLabels(): array
    {
        $labels = [
            'communication_id' => 'Communication ID',
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = self::find();

        $query->addSelect([
            'e_id',
            'e_project_id',
            'e_type_id',
            'e_status_id',
            'e_created_user_id',
            'e_created_dt',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['e_id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->datetime_start && $this->datetime_end) {
            $query->andFilterWhere(['>=', 'e_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_start))])
            ->andFilterWhere(['<=', 'e_created_dt', Employee::convertTimeFromUserDtToUTC(strtotime($this->datetime_end))]);
        }

        if (!empty($this->e_created_dt)) {
            $query->andFilterWhere(['DATE(e_created_dt)' => date('Y-m-d', strtotime($this->e_created_dt))]);
        }

        if ($this->supervision_id > 0) {
            $subQuery1 = UserGroupAssign::find()->select(['ugs_group_id'])->where(['ugs_user_id' => $this->supervision_id]);
            $subQuery = UserGroupAssign::find()->select(['DISTINCT(ugs_user_id)'])->where(['IN', 'ugs_group_id', $subQuery1]);
            $query->andWhere(['IN', 'e_created_user_id', $subQuery]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'e_id' => $this->e_id,
            'e_project_id' => $this->e_project_id,
            'e_type_id' => $this->e_type_id,
            'e_is_deleted' => $this->e_is_deleted,
            'e_status_id' => $this->e_status_id,
            'e_created_user_id' => $this->e_created_user_id,
        ]);

        if ($this->communication_id) {
            $query->joinWith(['emailLog' => function ($q) {
                $q->andFilterWhere(['like', 'el_communication_id', $this->communication_id]);
            }]);
        }

        if ($this->e_template_type_name) {
            $templateIds = EmailTemplateType::find()->select(['DISTINCT(etp_id) as e_template_type_id'])->where(['like', 'etp_name', $this->e_template_type_name])->asArray()->all();
            if ($templateIds) {
                $query->andFilterWhere(['e_template_type_id' => $templateIds]);
            }
        }
        if ($this->email_from) {
            $query->joinWith(['contactFrom' => function ($q) {
                $q->andFilterWhere(['like', 'ea_email', $this->email_from]);
            }]);
        }
        if ($this->email_to) {
            $query->joinWith(['contactTo' => function ($q) {
                $q->andFilterWhere(['like', 'ea_email', $this->email_to]);
            }]);
        }

        $dataProvider->totalCount = $query->count('distinct e_id');

        return $dataProvider;
    }

}
