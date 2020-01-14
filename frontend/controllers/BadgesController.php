<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\search\LeadQcallSearch;
use sales\repositories\lead\LeadBadgesRepository;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
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

    public $enableCsrfValidation = false;

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
                        'roles' => [
                            'admin',
                            'sales_senior',
                            'supervision', 'agent', 'qa', 'ex_agent', 'ex_super'
                        ]
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
     * @return array
     */
    public function actionGetBadgesCount(): array
    {

        $types = Yii::$app->request->post('types');

        if (!is_array($types)) {
            return [];
        }

        $result = [];

        foreach ($types as $type) {
            switch ($type) {
                case 'pending':
                    if ($count = $this->getPending()) {
                        $result['pending'] = $count;
                    }
                    break;
                case 'inbox':
                    if ($count = $this->getInbox()) {
                        $result['inbox'] = $count;
                    }
                    break;
                case 'follow-up':
                    if ($count = $this->getFollowUp()) {
                        $result['follow-up'] = $count;
                    }
                    break;
                case 'processing':
                    if ($count = $this->getProcessing()) {
                        $result['processing'] = $count;
                    }
                    break;
                case 'booked':
                    if ($count = $this->getBooked()) {
                        $result['booked'] = $count;
                    }
                    break;
                case 'sold':
                    if ($count = $this->getSold()) {
                        $result['sold'] = $count;
                    }
                    break;
                case 'duplicate':
                    if ($count = $this->getDuplicate()) {
                        $result['duplicate'] = $count;
                    }
                    break;
                case 'redial':
                    if ($count = $this->getRedial()) {
                        $result['redial'] = $count;
                    }
                    break;
                case 'trash':
                    if ($count = $this->getTrash()) {
                        $result['trash'] = $count;
                    }
                    break;
            }
        }
        return $result;
    }

    /**
     * @return int|null
     */
    private function getPending(): ?int
    {
        if (!Yii::$app->user->can('/lead/pending')) {
           return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getPendingCount($user);
    }

    /**
     * @return int|null
     */
    private function getInbox(): ?int
    {
        if (!Yii::$app->user->can('/lead/inbox')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getInboxCount($user);
    }

    /**
     * @return int|null
     */
    private function getFollowUp(): ?int
    {
        if (!Yii::$app->user->can('/lead/follow-up')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getFollowUpCount($user);
    }

    /**
     * @return int|null
     */
    private function getProcessing(): ?int
    {
        if (!Yii::$app->user->can('/lead/processing')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getProcessingCount($user);
    }

    /**
     * @return int|null
     */
    private function getBooked(): ?int
    {
        if (!Yii::$app->user->can('/lead/booked')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getBookedCount($user);
    }

    /**
     * @return int|null
     */
    private function getSold(): ?int
    {
        if (!Yii::$app->user->can('/lead/sold')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getSoldCount($user);
    }

    /**
     * @return int|null
     */
    private function getTrash(): ?int
    {
        if (!Yii::$app->user->can('/lead/trash')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getTrashCount($user);
    }

    /**
     * @return int|null
     */
    private function getDuplicate(): ?int
    {
        if (!Yii::$app->user->can('/lead/duplicate')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getDuplicateCount($user);
    }

    /**
     * @return int|null
     */
    private function getRedial(): ?int
    {
        if (!Yii::$app->user->can('/lead-redial/*')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return (new LeadQcallSearch())->searchByRedial([], $user)->query->count();
    }

}