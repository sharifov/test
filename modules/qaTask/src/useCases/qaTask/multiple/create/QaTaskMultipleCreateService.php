<?php

namespace modules\qaTask\src\useCases\qaTask\multiple\create;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTask\QaTaskRepository;
use modules\qaTask\src\useCases\qaTask\create\manually\QaTaskCreateManuallyForm;
use modules\qaTask\src\useCases\qaTask\create\manually\QaTaskCreateManuallyService;
use sales\interfaces\Objectable;

/**
 * Class QaTaskMultipleCreateService
 *
 * @property Message[] $log
 * @property QaTaskRepository $taskRepository
 * @property QaTaskCreateManuallyService $createManuallyService
 */
class QaTaskMultipleCreateService
{
    private $log = [];
    private $taskRepository;
    private $createManuallyService;

    public function __construct(
        QaTaskRepository $taskRepository,
        QaTaskCreateManuallyService $createManuallyService
    )
    {
        $this->taskRepository = $taskRepository;
        $this->createManuallyService = $createManuallyService;
    }

    /**
     * @param QaTaskMultipleCreateForm $form
     * @return Message[]
     */
    public function create(QaTaskMultipleCreateForm $form): array
    {
        $objectClass = QaTaskObjectType::getObjectClass($form->objectType);

        foreach ($form->ids as $id) {

            /** @var Objectable $object */
            if (!$object = $objectClass::findOne($id)) {
                $this->addMessage(new ErrorMessage($form->objectType, $id,'not found'));
                continue;
            }

            try {
                $this->createManuallyService->create(
                    new QaTaskCreateManuallyForm(
                        $form->objectType,
                        $id,
                        $object->getProjectId(),
                        $object->getDepartmentId(),
                        [],
                        $form->userId,
                        ['categoryId' => $form->categoryId]
                    )
                );
                $this->addMessage(new SuccessMessage($form->objectType, $id,'Task created'));
            } catch (\DomainException $e) {
                $this->addMessage(new ErrorMessage($form->objectType, $id, $e->getMessage()));
            } catch (\Throwable $e) {
                \Yii::error(QaTaskObjectType::getName($form->objectType) . ' Id: ' . $id  . PHP_EOL . $e, 'QaTaskMultipleCreateService:create');
                $this->addMessage(new ErrorMessage($form->objectType, $id, 'Server error'));
            }

        }

        return $this->log;
    }

    private function addMessage(Message $message): void
    {
        $this->log[] = $message;
    }

    public function formatMessages(Message ...$messages): string
    {
        if (!$messages) {
            return '';
        }

        $out = '<ul>';
        foreach ($messages as $message) {
            $out .= '<li>' . $message->format() . '</li>';
        }
        return $out . '</ul>';
    }
}
