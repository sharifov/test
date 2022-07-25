<?php

namespace src\services\badges\objects;

use common\models\Employee;
use common\models\search\LeadQcallSearch;
use modules\featureFlag\FFlag;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\repositories\lead\LeadBadgesRepository;
use src\services\badges\BadgeCounterInterface;
use Yii;

/**
 * Class LeadBadgeCounter
 *
 * @param LeadBadgesRepository $leadBadgesRepository
 */
class LeadBadgeCounter implements BadgeCounterInterface
{
    private LeadBadgesRepository $leadBadgesRepository;

    public function __construct(LeadBadgesRepository $leadBadgesRepository)
    {
        $this->leadBadgesRepository = $leadBadgesRepository;
    }

    public function countTypes(array $types): array
    {
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
                case 'redial':
                    if ($count = $this->getRedial()) {
                        $result['redial'] = $count;
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
                case 'extra-queue':
                    if ($count = $this->getLeadExtraQueue()) {
                        $result['extra-queue'] = $count;
                    }
                    break;
                case 'business-extra-queue':
                    if ($count = $this->getLeadBusinessExtraQueue()) {
                        $result['business-extra-queue'] = $count;
                    }
                    break;
                case 'closed':
                    if ($count = $this->getLeadClosedQueue()) {
                        $result['closed'] = $count;
                    }
                    break;
            }
        }
        return $result;
    }

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

    private function getLeadExtraQueue(): ?int
    {
        if (!Yii::$app->user->can('/lead/extra-queue')) {
            return null;
        }
        return $this->leadBadgesRepository->getExtraQueueCount();
    }

    private function getLeadBusinessExtraQueue(): ?int
    {
        /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) === true) {
            return null;
        }

        return $this->leadBadgesRepository->getBusinessExtraQueueCount();
    }

    private function getLeadClosedQueue(): ?int
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $dto = new LeadAbacDto(null, $user->id);
        /** @abac $leadAbacDto, LeadAbacObject::OBJ_CLOSED_QUEUE, LeadAbacObject::ACTION_ACCESS, show closed-queue badges count */
        if (!Yii::$app->abac->can($dto, LeadAbacObject::OBJ_CLOSED_QUEUE, LeadAbacObject::ACTION_ACCESS)) {
            return null;
        }
        return $this->leadBadgesRepository->getClosedCount($user);
    }
}
