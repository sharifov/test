<?php

namespace frontend\controllers;

use sales\helpers\setting\SettingHelper;
use sales\model\authClient\entity\AuthClientRepository;
use sales\repositories\NotFoundException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;

/**
 * @property-read AuthClientRepository $repository
 */
class AuthClientController extends FController
{
    private AuthClientRepository $repository;

    public function __construct($id, $module, AuthClientRepository $repository, $config = [])
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
            if (!$authClient->delete()) {
                throw new \RuntimeException('Cannot delete auth client:' . $authClient->getErrorSummary(true)[0]);
            }
            $result['message'] = 'Auth client detached successfully';
        } catch (NotFoundException | \RuntimeException $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }
        return $this->asJson($result);
    }
}
