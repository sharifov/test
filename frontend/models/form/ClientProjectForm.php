<?php

namespace frontend\models\form;

use common\models\Client;
use common\models\Project;
use yii\base\Model;

/**
 * This is the model Form class for table "client_project".
 *
 * @property int|null $leadId
 * @property int $clientId
 * @property int $projectID
 * @property bool $action
 *
 */
class ClientProjectForm extends Model
{
    public $leadId;
    public $clientId;
    public $projectId;
    public $action;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['clientId', 'projectId', 'action'], 'required'],
            [['leadId', 'clientId', 'projectId'], 'integer'],
            [['projectId'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['projectId' => 'id']],
            [['clientId'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['clientId' => 'id']],
            [['action'], 'boolean'],
        ];
    }
}
