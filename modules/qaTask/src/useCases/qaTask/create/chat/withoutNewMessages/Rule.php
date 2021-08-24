<?php

namespace modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages;

use Webmozart\Assert\Assert;

/**
 * Class Rule
 * @package modules\qaTask\src\useCases\qaTask\create\chat\withoutNewMessages
 *
 * @property string $qa_task_category_key
 * @property int $hours_passed
 */
class Rule
{
    public string $qa_task_category_key = '';

    public int $hours_passed = 0;

    public function __construct(array $params)
    {
        try {
            Assert::keyExists($params, 'qa_task_category_key');
            Assert::keyExists($params, 'hours_passed');
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $this->qa_task_category_key = (string)$params['qa_task_category_key'];
        $this->hours_passed = (int)$params['hours_passed'];
    }
}
