<?php

namespace console\controllers;

use modules\qaTask\src\entities\qaTaskRules\QaTaskRules;
use modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages\QaTaskCreateChatWithoutNewMessagesService;
use modules\qaTask\src\useCases\qaTask\create\lead\processingQuality\QaTaskCreateLeadProcessingQualityService;
use modules\qaTask\src\useCases\qaTask\create\lead\processingQuality\Rule;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class QaTaskController
 *
 * @property QaTaskCreateLeadProcessingQualityService $createLeadProcessingQuality
 * @property QaTaskCreateChatWithoutNewMessagesService $createChatWithoutNewMessagesService
 */
class QaTaskController extends Controller
{
    private $createLeadProcessingQuality;
    private QaTaskCreateChatWithoutNewMessagesService $createChatWithoutNewMessagesService;

    public function __construct(
        $id,
        $module,
        QaTaskCreateLeadProcessingQualityService $createLeadProcessingQuality,
        QaTaskCreateChatWithoutNewMessagesService $createChatWithoutNewMessagesService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->createLeadProcessingQuality = $createLeadProcessingQuality;
        $this->createChatWithoutNewMessagesService = $createChatWithoutNewMessagesService;
    }

    public function actionLeadProcessingQuality(): void
    {
        printf(PHP_EOL . ' --- Start ---' . PHP_EOL . PHP_EOL);

        try {
            if (!$parameters = QaTaskRules::getRule(QaTaskCreateLeadProcessingQualityService::CATEGORY_KEY)) {
                return;
            }
            if (!$parameters->isEnabled()) {
                return;
            }
            $rule = new Rule($parameters->getValue());
            $log = $this->createLeadProcessingQuality->handle($rule);
            printf(' --- Count Leads: %s ---' . PHP_EOL, $this->ansiFormat($log->getCount(), Console::FG_YELLOW));
            printf(' --- Count created Tasks: %s ---' . PHP_EOL, $this->ansiFormat($log->getValidCount(), Console::FG_YELLOW));
            printf(' --- Count errors: %s ---' . PHP_EOL, $this->ansiFormat($log->getInvalidCount(), Console::FG_YELLOW));
        } catch (\Throwable $e) {
            printf(' --- Error: %s ---' . PHP_EOL, $this->ansiFormat($e->getMessage(), Console::FG_YELLOW));
            \Yii::error($e, 'Console:QaTaskController');
        }

        printf(PHP_EOL . ' --- End ---' . PHP_EOL . PHP_EOL);
    }

    public function actionChatWithoutNewMessages()
    {
        printf(PHP_EOL . ' --- Start ---' . PHP_EOL . PHP_EOL);

        try {
            if (!$parameters = QaTaskRules::getRule(QaTaskCreateChatWithoutNewMessagesService::CATEGORY_KEY)) {
                return;
            }
            if (!$parameters->isEnabled()) {
                return;
            }

            $rule = new \modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages\Rule($parameters->getValue());
            if (!$rule->hours_passed) {
                return;
            }

            $log = $this->createChatWithoutNewMessagesService->handle($rule);
            printf(' --- Count Chats: %s ---' . PHP_EOL, $this->ansiFormat($log->getCount(), Console::FG_YELLOW));
            printf(' --- Count created Tasks: %s ---' . PHP_EOL, $this->ansiFormat($log->getValidCount(), Console::FG_YELLOW));
            printf(' --- Count errors: %s ---' . PHP_EOL, $this->ansiFormat($log->getInvalidCount(), Console::FG_YELLOW));
        } catch (\Throwable $e) {
            printf(' --- Error: %s ---' . PHP_EOL, $this->ansiFormat($e->getMessage(), Console::FG_YELLOW));
            \Yii::error($e, 'Console:QaTaskController');
        }

        printf(PHP_EOL . ' --- End ---' . PHP_EOL . PHP_EOL);
    }
}
