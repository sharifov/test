<?php

namespace sales\services\badges\objects;

use common\models\Employee;
use sales\repositories\cases\CasesQRepository;
use sales\services\badges\BadgeCounterInterface;
use Yii;

/**
 * Class CasesBadgeCounter
 *
 * @property CasesQRepository $casesQRepository
 */
class CasesBadgeCounter implements BadgeCounterInterface
{
    private CasesQRepository $casesQRepository;

    public function __construct(CasesQRepository $casesQRepository)
    {
        $this->casesQRepository = $casesQRepository;
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
                case 'trash':
                    if ($count = $this->getTrash()) {
                        $result['trash'] = $count;
                    }
                    break;
                case 'need-action':
                    if ($count = $this->getNeedAction()) {
                        $result['need-action'] = $count;
                    }
                    break;
                case 'unidentified':
                    if ($count = $this->getUnidentifiedAction()) {
                        $result['unidentified'] = $count;
                    }
                    break;
                case 'first-priority':
                    if ($count = $this->getFirstPriorityAction()) {
                        $result['first-priority'] = $count;
                    }
                    break;
                case 'second-priority':
                    if ($count = $this->getSecondPriorityAction()) {
                        $result['second-priority'] = $count;
                    }
                    break;
                case 'pass-departure':
                    if ($count = $this->getPassDepartureAction()) {
                        $result['pass-departure'] = $count;
                    }
                    break;
                case 'error':
                    if ($count = $this->getError()) {
                        $result['error'] = $count;
                    }
                    break;
                case 'awaiting':
                    if ($count = $this->getAwaiting()) {
                        $result['awaiting'] = $count;
                    }
                    break;
                case 'auto-processing':
                    if ($count = $this->getAutoProcessing()) {
                        $result['auto-processing'] = $count;
                    }
                    break;
            }
        }
        return $result;
    }

    private function getPending(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/pending')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getPendingCount($user);
    }

    /**
     * @return int|null
     */
    private function getInbox(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/inbox')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getInboxCount($user);
    }

    /**
     * @return int|null
     */
    private function getError(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/error')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getErrorCount($user);
    }

    /**
     * @return int|null
     */
    private function getAwaiting(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/awaiting')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getAwaitingCount($user);
    }

    /**
     * @return int|null
     */
    private function getAutoProcessing(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/auto-processing')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getAutoProcessingCount($user);
    }

    /**
     * @return int|null
     */
    private function getFollowUp(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/follow-up')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getFollowUpCount($user);
    }

    /**
     * @return int|null
     */
    private function getProcessing(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/processing')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getProcessingCount($user);
    }

    /**
     * @return int|null
     */
    private function getSolved(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/solved')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getSolvedCount($user);
    }

    /**
     * @return int|null
     */
    private function getTrash(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/trash')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getTrashCount($user);
    }

    /**
     * @return int|null
     */
    private function getNeedAction(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/need-action')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getNeedActionCount($user);
    }

    private function getUnidentifiedAction(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/unidentified')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getUnidentifiedCount($user);
    }

    private function getFirstPriorityAction(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/first-priority')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getFirstPriorityCount($user);
    }

    private function getSecondPriorityAction(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/second-priority')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getSecondPriorityCount($user);
    }

    private function getPassDepartureAction(): ?int
    {
        if (!Yii::$app->user->can('/cases-q/pass-departure')) {
            return null;
        }
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        return $this->casesQRepository->getPassDepartureCount($user);
    }
}
