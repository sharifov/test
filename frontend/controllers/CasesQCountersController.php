<?php

namespace frontend\controllers;

use common\models\Employee;
use sales\repositories\cases\CasesQRepository;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use Yii;

/**
 * Class CasesQCountersController
 * @property CasesQRepository $casesQRepository
 */
class CasesQCountersController extends FController
{

    private $casesQRepository;

    public $enableCsrfValidation = false;

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


    public function actionGetQCount(): array
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
                case 'solved':
                    if ($count = $this->getSolved()) {
                        $result['solved'] = $count;
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
            }
        }

        return $result;
    }

    /**
     * @return int|null
     */
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
}
