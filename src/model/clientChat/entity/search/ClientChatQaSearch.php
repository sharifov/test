<?php

namespace src\model\clientChat\entity\search;

use common\models\Employee;
use src\access\EmployeeProjectAccess;
use src\model\clientChat\entity\Scopes;
use src\model\clientChatCase\entity\ClientChatCase;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\clientChatUserChannel\entity\ClientChatUserChannel;
use src\model\clientChatVisitor\entity\ClientChatVisitor;
use src\model\clientChatVisitorData\entity\ClientChatVisitorData;
use Yii;
use yii\data\ActiveDataProvider;
use src\model\clientChat\entity\ClientChat;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * ClientChatQaSearch represents the model behind the search form of `ClientChat`.
 *
 * @property $createdRangeDate
 * @property string|null $dataCountry
 * @property string|null $dataCity
 * @property int|null $messageBy
 * @property string|null $messageText
 * @property int|null $leadId
 * @property int|null $caseId
 */
class ClientChatQaSearch extends ClientChat
{
    public $createdRangeDate;
    public $dataCountry;
    public $dataCity;
    public $messageBy;
    public $messageText;
    public $leadId;
    public $caseId;
    public string $ownerUserID = '';

    public const MESSAGE_BY_CLIENT = 1;
    public const MESSAGE_BY_USER = 2;

    public const MESSAGE_BY_LIST = [
        self::MESSAGE_BY_CLIENT => 'Client',
        self::MESSAGE_BY_USER => 'User',
    ];

    public function rules(): array
    {
        return [
            [
                [
                    'cch_id', 'cch_ccr_id', 'cch_project_id',
                    'cch_dep_id', 'cch_channel_id', 'cch_client_id',
                    'cch_status_id', 'cch_ua', 'cch_created_user_id',
                    'cch_updated_user_id', 'cch_client_online', 'messageBy',
                    'cch_owner_user_id', 'caseId', 'leadId', 'cch_source_type_id', 'cch_parent_id'
                ],
                'integer',
            ],
            [['createdRangeDate'], 'safe'],
            [['cch_rid', 'cch_title', 'cch_description', 'cch_note', 'cch_ip', 'cch_language_id'], 'string'],
            [['cch_created_dt', 'cch_updated_dt'], 'date', 'format' => 'php:Y-m-d'],
            [['dataCountry', 'dataCity', 'messageText'], 'string', 'max' => 100],
            ['ownerUserID', 'string'],
            [['createdRangeDate'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['createdRangeDate'], 'validateRange', 'params' => ['minStartDate' => '2020-01-01 00:00:00', 'maxEndDate' => date("Y-m-d 23:59:59")]],
        ];
    }

    public function attributeLabels(): array
    {
        $labels = [
            'cch_client_id' => 'Client ID',
            'ownerUserID' => 'Owner User'
        ];
        return ArrayHelper::merge(parent::attributeLabels(), $labels);
    }

    /**
     * @param $params
     * @param Employee $employee
     * @return ActiveDataProvider
     */
    public function searchCommon($params, Employee $employee): ActiveDataProvider
    {
        $query = ClientChat::find();
        $query->andProjectEmployee($employee);
        $query->andChannelEmployee($employee);
        $query->orOwner($employee);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cch_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $this->createdRangeDate = null;
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cch_id' => $this->cch_id,
            'cch_ccr_id' => $this->cch_ccr_id,
            'cch_project_id' => $this->cch_project_id,
            'cch_dep_id' => $this->cch_dep_id,
            'cch_channel_id' => $this->cch_channel_id,
            'cch_client_id' => $this->cch_client_id,
            'cch_owner_user_id' => $this->cch_owner_user_id,
            'cch_status_id' => $this->cch_status_id,
            'cch_ua' => $this->cch_ua,
            'DATE(cch_created_dt)' => $this->cch_created_dt,
            'cch_updated_dt' => $this->cch_updated_dt,
            'cch_created_user_id' => $this->cch_created_user_id,
            'cch_updated_user_id' => $this->cch_updated_user_id,
            'cch_client_online' => $this->cch_client_online,
            'cch_source_type_id' => $this->cch_source_type_id,
            'cch_parent_id' => $this->cch_parent_id,
        ]);

        $query->andFilterWhere(['like', 'cch_rid', $this->cch_rid])
            ->andFilterWhere(['like', 'cch_title', $this->cch_title])
            ->andFilterWhere(['like', 'cch_description', $this->cch_description])
            ->andFilterWhere(['like', 'cch_note', $this->cch_note])
            ->andFilterWhere(['like', 'cch_ip', $this->cch_ip])
            ->andFilterWhere(['like', 'cch_language_id', $this->cch_language_id]);

        if ($this->ownerUserID) {
            $query->andWhere(['cch_owner_user_id' => $this->ownerUserID]);
        }

        if ($this->createdRangeDate) {
            $dateRange = explode(' - ', $this->createdRangeDate);
            if ($dateRange[0] && $dateRange[1]) {
                $fromDate = date('Y-m-d', strtotime($dateRange[0]));
                $toDate = date('Y-m-d', strtotime($dateRange[1]));
                $query->andWhere(['BETWEEN', 'DATE(cch_created_dt)', $fromDate, $toDate]);
            }
        }
        if ($this->leadId) {
            $query->andWhere(['cch_id' =>
                ClientChatLead::find()->select('ccl_chat_id')
                    ->andWhere(['ccl_lead_id' => $this->leadId])->distinct()]);
        }
        if ($this->caseId) {
            $query->andWhere(['cch_id' =>
                ClientChatCase::find()->select('cccs_chat_id')
                    ->andWhere(['cccs_case_id' => $this->caseId])->distinct()]);
        }
        if ($this->dataCountry) {
            $query->andWhere(['cch_id' =>
                ClientChatVisitor::find()->select('ccv_cch_id')
                    ->innerJoin(ClientChatVisitorData::tableName(), 'ccv_cvd_id = cvd_id')
                    ->andWhere(['cvd_country' => $this->dataCountry])
                    ->distinct()]);
        }
        if ($this->dataCity) {
            $query->andWhere(['cch_id' =>
                ClientChatVisitor::find()->select('ccv_cch_id')
                    ->innerJoin(ClientChatVisitorData::tableName(), 'ccv_cvd_id = cvd_id')
                    ->andWhere(['cvd_city' => $this->dataCity])
                    ->distinct()]);
        }
        if ($this->messageText) {
            $by = ArrayHelper::isIn($this->messageBy, self::MESSAGE_BY_LIST) ? $this->messageBy : null;
            $query->andWhere(['cch_id' => self::getIdsByChatMessage($this->messageText, $by)]);
        }
        if ($this->messageBy && !$this->messageText) {
            $query->andWhere(['cch_id' => self::getIdsByCreatorType($this->messageBy)]);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = ClientChat::find()
            ->byUserGroupsRestriction()
            ->byProjectRestrictionQa()
            ->byDepartmentRestriction();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['cch_id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $this->createdRangeDate = null;
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cch_id' => $this->cch_id,
            'cch_ccr_id' => $this->cch_ccr_id,
            'cch_project_id' => $this->cch_project_id,
            'cch_dep_id' => $this->cch_dep_id,
            'cch_channel_id' => $this->cch_channel_id,
            'cch_client_id' => $this->cch_client_id,
            'cch_owner_user_id' => $this->cch_owner_user_id,
            'cch_status_id' => $this->cch_status_id,
            'cch_ua' => $this->cch_ua,
            'DATE(cch_created_dt)' => $this->cch_created_dt,
            'cch_updated_dt' => $this->cch_updated_dt,
            'cch_created_user_id' => $this->cch_created_user_id,
            'cch_updated_user_id' => $this->cch_updated_user_id,
            'cch_client_online' => $this->cch_client_online,
            'cch_source_type_id' => $this->cch_source_type_id,
            'cch_parent_id' => $this->cch_parent_id,
        ]);

        $query->andFilterWhere(['like', 'cch_rid', $this->cch_rid])
            ->andFilterWhere(['like', 'cch_title', $this->cch_title])
            ->andFilterWhere(['like', 'cch_description', $this->cch_description])
            ->andFilterWhere(['like', 'cch_note', $this->cch_note])
            ->andFilterWhere(['like', 'cch_ip', $this->cch_ip])
            ->andFilterWhere(['like', 'cch_language_id', $this->cch_language_id]);

        if ($this->ownerUserID) {
            $query->andWhere(['cch_owner_user_id' => $this->ownerUserID]);
        }

        if ($this->createdRangeDate) {
            $dateRange = explode(' - ', $this->createdRangeDate);
            if ($dateRange[0] && $dateRange[1]) {
                $fromDate = date('Y-m-d', strtotime($dateRange[0]));
                $toDate = date('Y-m-d', strtotime($dateRange[1]));
                $query->andWhere(['BETWEEN', 'DATE(cch_created_dt)', $fromDate, $toDate]);
            }
        }
        if ($this->leadId) {
            $query->andWhere(['cch_id' =>
                ClientChatLead::find()->select('ccl_chat_id')
                    ->andWhere(['ccl_lead_id' => $this->leadId])->distinct()]);
        }
        if ($this->caseId) {
            $query->andWhere(['cch_id' =>
                ClientChatCase::find()->select('cccs_chat_id')
                    ->andWhere(['cccs_case_id' => $this->caseId])->distinct()]);
        }
        if ($this->dataCountry) {
            $query->andWhere(['cch_id' =>
                ClientChatVisitor::find()->select('ccv_cch_id')
                    ->innerJoin(ClientChatVisitorData::tableName(), 'ccv_cvd_id = cvd_id')
                    ->andWhere(['cvd_country' => $this->dataCountry])
                    ->distinct()]);
        }
        if ($this->dataCity) {
            $query->andWhere(['cch_id' =>
                ClientChatVisitor::find()->select('ccv_cch_id')
                    ->innerJoin(ClientChatVisitorData::tableName(), 'ccv_cvd_id = cvd_id')
                    ->andWhere(['cvd_city' => $this->dataCity])
                    ->distinct()]);
        }
        if ($this->messageText) {
            $by = ArrayHelper::isIn($this->messageBy, self::MESSAGE_BY_LIST) ? $this->messageBy : null;
            $query->andWhere(['cch_id' => self::getIdsByChatMessage($this->messageText, $by)]);
        }
        if ($this->messageBy && !$this->messageText) {
            $query->andWhere(['cch_id' => self::getIdsByCreatorType($this->messageBy)]);
        }

        return $dataProvider;
    }

    protected static function getIdsByCreatorType(int $by): array
    {
        $sql = 'SELECT 
                    ccm_cch_id
                FROM
                    client_chat_message';

        if ($by === self::MESSAGE_BY_CLIENT) {
            $sql .= ' WHERE ccm_client_id IS NOT NULL';
        } elseif ($by === self::MESSAGE_BY_USER) {
            $sql .= ' WHERE ccm_user_id IS NOT NULL';
        }
        $sql .= ' GROUP BY ccm_cch_id';

        return ArrayHelper::getColumn(
            Yii::$app->db_postgres->createCommand($sql)->queryAll(),
            'ccm_cch_id'
        );
    }

    protected static function getIdsByChatMessage(string $msg, ?int $by = null): array
    {
        $sql = "SELECT 
                    ccm_cch_id
                FROM
                    client_chat_message
                WHERE 
                    ccm_body->>'msg' LIKE :msg";

        if ($by === self::MESSAGE_BY_CLIENT) {
            $sql .= ' AND ccm_client_id IS NOT NULL';
        } elseif ($by === self::MESSAGE_BY_USER) {
            $sql .= ' AND ccm_user_id IS NOT NULL';
        }
        $sql .= ' GROUP BY ccm_cch_id';

        return ArrayHelper::getColumn(
            Yii::$app->db_postgres->createCommand(
                $sql,
                [':msg' => '%' . $msg . '%']
            )->queryAll(),
            'ccm_cch_id'
        );
    }

    public function searchIds($params): array
    {
        $query = static::find();

        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
            return [];
        }
        $query->select('cch_id');
        $this->filterQuery($query);

        return ArrayHelper::map($query->asArray()->all(), 'cch_id', 'cch_id');
    }

    /**
     * @param Scopes $query
     * @return Scopes
     */
    private function filterQuery(Scopes $query): ActiveQuery
    {
        if ($this->ownerUserID) {
            $query->andWhere(['cch_owner_user_id' => $this->ownerUserID]);
        }

        if ($this->createdRangeDate) {
            $dateRange = explode(' - ', $this->createdRangeDate);
            if ($dateRange[0] && $dateRange[1]) {
                $fromDate = date('Y-m-d', strtotime($dateRange[0]));
                $toDate = date('Y-m-d', strtotime($dateRange[1]));
                $query->andWhere(['BETWEEN', 'DATE(cch_created_dt)', $fromDate, $toDate]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'cch_id' => $this->cch_id,
            'cch_ccr_id' => $this->cch_ccr_id,
            'cch_project_id' => $this->cch_project_id,
            'cch_dep_id' => $this->cch_dep_id,
            'cch_channel_id' => $this->cch_channel_id,
            'cch_client_id' => $this->cch_client_id,
            'cch_owner_user_id' => $this->cch_owner_user_id,
            'cch_status_id' => $this->cch_status_id,
            'cch_ua' => $this->cch_ua,
            'DATE(cch_created_dt)' => $this->cch_created_dt,
            'cch_updated_dt' => $this->cch_updated_dt,
            'cch_created_user_id' => $this->cch_created_user_id,
            'cch_updated_user_id' => $this->cch_updated_user_id,
            'cch_client_online' => $this->cch_client_online,
            'cch_source_type_id' => $this->cch_source_type_id,
        ]);

        $query->andFilterWhere(['like', 'cch_rid', $this->cch_rid])
            ->andFilterWhere(['like', 'cch_title', $this->cch_title])
            ->andFilterWhere(['like', 'cch_description', $this->cch_description])
            ->andFilterWhere(['like', 'cch_note', $this->cch_note])
            ->andFilterWhere(['like', 'cch_ip', $this->cch_ip])
            ->andFilterWhere(['like', 'cch_language_id', $this->cch_language_id]);

        return $query;
    }

    public function validateRange($attribute, $params)
    {
        $range = explode(' - ', $this->$attribute);
        if ((count($range) == count($params)) == 2) {
            if (
                (strtotime(reset($range)) < strtotime(reset($params)) ||
                    strtotime(reset($range)) > strtotime(end($params))) ||

                (strtotime(end($range)) > strtotime(end($params)) ||
                    strtotime(end($range)) < strtotime(reset($params))) ||

                (strtotime(reset($range)) > strtotime(end($range)) ||
                    strtotime(end($range)) < strtotime(reset($range)))
            ) {
                $this->addError($attribute, 'Range start date or end date is incorrect');
            }
        } else {
            $this->addError($attribute, 'Range format or validation params set wrong');
        }
    }
}
