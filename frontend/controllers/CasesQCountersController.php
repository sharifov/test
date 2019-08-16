<?php

namespace frontend\controllers;

use common\models\Employee;
use sales\repositories\cases\CasesQRepository;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use Yii;


/**
 * Class CasesQCountersController
 * @property CasesQRepository $casesQRepository
 */
class CasesQCountersController extends FController
{

    private $casesQRepository;

    /**
     * CasesQCountersController constructor.
     * @param $id
     * @param $module
     * @param CasesQRepository $casesQRepository
     * @param array $config
     */
    public function __construct($id, $module, CasesQRepository $casesQRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->casesQRepository = $casesQRepository;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @param $type
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionGetQCount($type): array
    {
        switch ($type) {
            case 'pending':
                $count = $this->getPending();
                break;
            case 'inbox':
                $count = $this->getInbox();
                break;
            case 'followup':
                $count = $this->getFollowup();
                break;
            case 'processing':
                $count = $this->getProcessing();
                break;
            case 'solved':
                $count = $this->getSolved();
                break;
            case 'trash':
                $count = $this->getTrash();
                break;
            default: $count = null;
        }
        return $count;
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getPending(): array
    {
        if (!Yii::$app->user->can('/cases-q/pending')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->casesQRepository->getPendingCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getInbox(): array
    {
        if (!Yii::$app->user->can('/cases-q/inbox')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->casesQRepository->getInboxCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getFollowup(): array
    {
        if (!Yii::$app->user->can('/cases-q/followup')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->casesQRepository->getFollowUpCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getProcessing(): array
    {
        if (!Yii::$app->user->can('/cases-q/processing')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->casesQRepository->getProcessingCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getSolved(): array
    {
        if (!Yii::$app->user->can('/cases-q/solved')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->casesQRepository->getSolvedCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getTrash(): array
    {
        if (!Yii::$app->user->can('/cases-q/trash')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->casesQRepository->getTrashCount($user);
        return ['result' => 'success', 'count' => $count];
    }

}