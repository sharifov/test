<?php

namespace frontend\controllers;

use common\models\Notifications;
use src\helpers\setting\SettingHelper;
use src\model\userAuthClient\entity\UserAuthClientRepository;
use src\model\userAuthClient\entity\UserAuthClientSources;
use src\repositories\NotFoundException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;

/**
 * @property-read UserAuthClientRepository $repository
 */
class UserAuthClientController extends FController
{
    private UserAuthClientRepository $repository;

    public function __construct($id, $module, UserAuthClientRepository $repository, $config = [])
    {
        $this->repository = $repository;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'detach' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionDetach()
    {
        if (!\Yii::$app->request->isPost && \Yii::$app->request->isAjax) {
            throw new MethodNotAllowedHttpException('Method not allowed');
        }

        if (!SettingHelper::isEnabledAuthClients()) {
            throw new ForbiddenHttpException('Access denied');
        }

        $authClientId = \Yii::$app->request->post('authClientId');
        if (!$authClientId) {
            throw new MethodNotAllowedHttpException('Auth client id not found');
        }

        try {
            $result = [
                'error' => false,
                'message' => ''
            ];

            $authClient = $this->repository->find($authClientId);

            if ($authClient->uac_source == UserAuthClientSources::GOOGLE && !SettingHelper::isEnabledGoogleAuthClient()) {
                throw new ForbiddenHttpException('Access denied');
            }
            if ($authClient->uac_source == UserAuthClientSources::MICROSOFT && !SettingHelper::isEnabledMicrosoftAuthClient()) {
                throw new ForbiddenHttpException('Access denied');
            }

            if (!$authClient->delete()) {
                throw new \RuntimeException('Cannot delete auth client:' . $authClient->getErrorSummary(true)[0]);
            }

            Notifications::create($authClient->uac_user_id, 'Auth client detached', 'Source: ' . UserAuthClientSources::getName($authClient->uac_source) . ' was detached from your account.', Notifications::TYPE_INFO, true);

            $result['message'] = 'Auth client detached successfully';
        } catch (NotFoundException | \RuntimeException $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }
        return $this->asJson($result);
    }
}
