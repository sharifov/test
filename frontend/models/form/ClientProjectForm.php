<?php

namespace frontend\models\form;

use common\models\Client;
use common\models\Project;
use yii\base\Model;

/**
 * This is the model Form class for table "client_project".
 *
 * @property int|null $leadID
 * @property int $clientID
 * @property int $projectID
 * @property bool $action
 *
 */
class ClientProjectForm extends Model
{
    public $leadID;
    public $clientID;
    public $projectID;
    public $action;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['clientID', 'projectID', 'action'], 'required'],
            [['leadID', 'clientID', 'projectID'], 'integer'],
            [['projectID'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['projectID' => 'id']],
            [['clientID'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['clientID' => 'id']],
            [['action'], 'boolean'],
        ];
    }
}
