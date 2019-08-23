<?php

namespace frontend\controllers;

use common\models\Employee;
use sales\repositories\lead\LeadBadgesRepository;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use Yii;

/**
 * Class BadgesController
 * @param LeadBadgesRepository $leadBadgesRepository
 */
class BadgesController extends FController
{
    private $leadBadgesRepository;

    public function __construct($id, $module, LeadBadgesRepository $leadBadgesRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadBadgesRepository = $leadBadgesRepository;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin', 'supervision', 'agent', 'qa', 'ex_agent', 'ex_super']
                    ]
                ]
            ],
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
    public function actionGetBadgesCount($type): array
    {
        switch ($type) {
            case 'pending':
                $count = $this->getPending();
                break;
            case 'inbox':
                $count = $this->getInbox();
                break;
            case 'follow-up':
                $count = $this->getFollowUp();
                break;
            case 'processing':
                $count = $this->getProcessing();
                break;
            case 'booked':
                $count = $this->getBooked();
                break;
            case 'sold':
                $count = $this->getSold();
                break;
            case 'duplicate':
                $count = $this->getDuplicate();
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
        if (!Yii::$app->user->can('/lead/pending')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->leadBadgesRepository->getPendingCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getInbox(): array
    {
        if (!Yii::$app->user->can('/lead/inbox')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->leadBadgesRepository->getInboxCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getFollowUp(): array
    {
        if (!Yii::$app->user->can('/lead/follow-up')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->leadBadgesRepository->getFollowUpCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getProcessing(): array
    {
        if (!Yii::$app->user->can('/lead/processing')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->leadBadgesRepository->getProcessingCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getBooked(): array
    {
        if (!Yii::$app->user->can('/lead/booked')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->leadBadgesRepository->getBookedCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getSold(): array
    {
        if (!Yii::$app->user->can('/lead/sold')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->leadBadgesRepository->getSoldCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getTrash(): array
    {
        if (!Yii::$app->user->can('/lead/trash')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->leadBadgesRepository->getTrashCount($user);
        return ['result' => 'success', 'count' => $count];
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    private function getDuplicate(): array
    {
        if (!Yii::$app->user->can('/lead/duplicate')) {
            throw new ForbiddenHttpException();
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $count = $this->leadBadgesRepository->getDuplicateCount($user);
        return ['result' => 'success', 'count' => $count];
    }

}