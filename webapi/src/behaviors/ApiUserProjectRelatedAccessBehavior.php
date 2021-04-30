<?php

namespace webapi\src\behaviors;

use common\models\ApiUser;
use common\models\Project;
use Yii;
use yii\base\ActionEvent;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\NotAcceptableHttpException;

/**
 * Class ApiUserProjectRelatedAccessBehavior
 *
 * @property string|null $apiKey
 */
class ApiUserProjectRelatedAccessBehavior extends ApiUserProjectAccessBehavior
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

            if (!$apiUser = $this->getApiUser($apiProject)) {
                $message = $this->addLog(
                    'ApiUser in status enabled not found',
                    $event->action->uniqueId
                );
                throw new NotAcceptableHttpException($message, 10);
            }

            Yii::$app->getUser()->login($apiUser);
        }
    }

    /**
     * @param Project $project
     * @return ApiUser|null
     */
    private function getApiUser(Project $project): ?ApiUser
    {
        $projectIds = ArrayHelper::merge([$project->id], $project->getRelatedProjectIds());
        return ApiUser::find()
            ->where(['au_enabled' => true])
            ->andWhere(['IN', 'au_project_id', $projectIds])
            ->one();
    }
}
