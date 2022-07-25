<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Lead;
use common\models\search\LeadQcallSearch;
use modules\featureFlag\FFlag;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\repositories\lead\LeadBadgesRepository;
use src\services\badges\BadgesObjectFactory;
use src\services\badges\form\BadgeForm;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
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
    private LeadBadgesRepository $leadBadgesRepository;

    public $enableCsrfValidation = false;

    /**
     * @param $id
     * @param $module
     * @param LeadBadgesRepository $leadBadgesRepository
     * @param array $config
     */
    public function __construct($id, $module, LeadBadgesRepository $leadBadgesRepository, array $config = [])
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

    public function actionBadgesCount(): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['message' => '', 'status' => 0, 'data' => []];

            try {
                /** @fflag FFlag::FF_KEY_BADGE_COUNT_ENABLE, Badge Count Enable/Disable */
                if (!Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BADGE_COUNT_ENABLE)) {
                    throw new \RuntimeException('Feature Flag (' . FFlag::FF_KEY_BADGE_COUNT_ENABLE . ') is disabled');
                }

                if (!$badgesCollection = Yii::$app->request->post('badgesCollection')) {
                    throw new \RuntimeException('Param badgesCollection is required');
                }
                if (!is_array($badgesCollection)) {
                    throw new \RuntimeException('Param badgesCollection not is array');
                }

                foreach ($badgesCollection as $badge) {
                    try {
                        $badgeForm = new BadgeForm();
                        if (!$badgeForm->load($badge)) {
                            throw new \RuntimeException('BadgeForm not loaded');
                        }
                        if (!$badgeForm->validate()) {
                            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($badgeForm));
                        }

                        $badgeCounter = (new BadgesObjectFactory($badgeForm->objectKey))->create();
                        $result['data'][$badgeForm->idName] = $badgeCounter->countTypes((array) $badgeForm->types);
                    } catch (\RuntimeException | \DomainException $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['data' => $badge]);
                        \Yii::warning($message, 'BadgesController:actionBadgesCount:badgesCollection:Exception');
                    } catch (\Throwable $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['data' => $badge]);
                        \Yii::error($message, 'BadgesController:actionBadgesCount:badgesCollection:Throwable');
                    }
                }
                if (empty($result['data'])) {
                    throw new \RuntimeException('Data is empty');
                }

                $result['message'] = 'Success';
                $result['status'] = 1;
            } catch (\RuntimeException | \DomainException $exception) {
                Yii::warning(AppHelper::throwableLog($exception), 'BadgesController:actionBadgesCount::Exception');
                $result['message'] = VarDumper::dumpAsString($exception->getMessage());
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'BadgesController:actionBadgesCount:Throwable');
                $result['message'] = 'Internal Server Error';
            }
            return $result;
        }
        throw new BadRequestHttpException();
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
                /*case 'sold':
                    if ($count = $this->getSold()) {
                        $result['sold'] = $count;
                    }
                    break;
                case 'duplicate':
                    if ($count = $this->getDuplicate()) {
                        $result['duplicate'] = $count;
                    }
                    break;*/
                case 'redial':
                    if ($count = $this->getRedial()) {
                        $result['redial'] = $count;
                    }
                    break;
                /*case 'trash':
                    if ($count = $this->getTrash()) {
                        $result['trash'] = $count;
                    }
                    break;*/
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
                case 'alternative':
                    if ($count = $this->getAlternative()) {
                        $result['alternative'] = $count;
                    }
                    break;
                case 'business-inbox':
                    if ($count = $this->getBusinessInbox()) {
                        $result['business-inbox'] = $count;
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
    private function getBusinessInbox(): ?int
    {
        if (!Yii::$app->user->can('/lead/business-inbox')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        /** @fflag FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT, Business Queue Limit Enable */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT)) {
            $limit = 0;
            if ($user->isAgent()) {
                $userParams = $user->userParams;
                if ($userParams) {
                    if ($userParams->up_business_inbox_show_limit_leads > 0) {
                        $limit = $userParams->up_business_inbox_show_limit_leads;
                    }
                } else {
                    return null;
                }
            }
            $count = $this->leadBadgesRepository->getBusinessInboxCount($user);
            if ($limit > 0 && $count > 0 && $count > $limit) {
                return $limit;
            }
            return $count;
        }

        return $this->leadBadgesRepository->getBusinessInboxCount($user);
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
        $count = $this->leadBadgesRepository->getInboxCount($user);
        if ($limit > 0 && $count > 0 && $count > $limit) {
            return $limit;
        }
        return $count;
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

    private function getAlternative(): ?int
    {
        if (!Yii::$app->user->can('/lead/alternative')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->leadBadgesRepository->getAlternativeCount($user);
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
