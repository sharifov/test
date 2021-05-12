<?php

namespace webapi\src\behaviors;

use common\models\ApiUser;
use common\models\Project;
use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\rest\Controller;
use yii\web\NotAcceptableHttpException;

/**
 * Class ApiUserProjectAccessBehavior
 *
 * @property string|null $apiKey
 */
class ApiUserProjectAccessBehavior extends Behavior
{
    public $apiKey;

    public function events(): array
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'checkAccess',
        ];
    }

    public function checkAccess(ActionEvent $event): void
    {
        if ($this->apiKey) {
            if (!$apiProject = Project::find()->where(['api_key' => $this->apiKey])->one()) {
                $errorMessage = $this->addLog(
                    'Not init Project',
                    $event->action->uniqueId
                );
                throw new NotAcceptableHttpException($errorMessage, 9);
            }

            if (!$apiUser = ApiUser::findOne(['au_project_id' => $apiProject->id, 'au_enabled' => true])) {
                $message = $this->addLog(
                    'ApiUser in status enabled not found',
                    $event->action->uniqueId
                );
                throw new NotAcceptableHttpException($message, 10);
            }

            Yii::$app->getUser()->login($apiUser);
        }
    }

    public function addLog(string $message, string $action): string
    {
        $log['apiKey'] = $this->apiKey;
        $log['message'] = $message;
        $log['action'] = $action;
        $log['ip'] = Yii::$app->request->getRemoteIP();
        Yii::warning($log, 'ApiUserProjectAccessBehavior:checkAccess');
        return $message;
    }
}
