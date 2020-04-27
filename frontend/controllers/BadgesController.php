<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use common\models\search\LeadQcallSearch;
use sales\repositories\lead\LeadBadgesRepository;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
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
                            Employee::ROLE_ADMIN,
                            Employee::ROLE_SALES_SENIOR,
                            Employee::ROLE_EXCHANGE_SENIOR,
                            Employee::ROLE_SUPPORT_SENIOR,
                            Employee::ROLE_SUPERVISION,
                            Employee::ROLE_AGENT,
                            Employee::ROLE_QA,
                            Employee::ROLE_EX_AGENT,
                            Employee::ROLE_EX_SUPER,
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
                case 'bonus':
                    if ($count = $this->getBonus()) {
                        $result['bonus'] = $count;
                    }
                    break;
                case 'failed-bookings':
                    if ($count = $this->getFailedBookings()) {
                        $result['failed-bookings'] = $count;
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
    private function getBonus(): ?int
    {
        if (!Yii::$app->user->can('/lead/bonus')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getBonusCount($user);
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

    /**
     * @return int|null
     */
    private function getFailedBookings(): ?int
    {
        if (!Yii::$app->user->can('/lead/failed-bookings')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $limit = 0;
        if ($user->isAgent()) {
            $userParams = $user->userParams;
            if ($userParams) {
                if ($userParams->up_inbox_show_limit_leads > 0) {
                    $limit = $userParams->up_inbox_show_limit_leads;
                }
            } else {
                return null;
            }
        }
        $count = $this->leadBadgesRepository->getFailedBookingsCount($user);
        if ($limit > 0 && $count > 0 && $count > $limit) {
            return $limit;
        }
        return $count;
    }
}
