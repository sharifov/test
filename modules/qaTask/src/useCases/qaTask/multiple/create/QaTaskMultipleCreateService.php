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
 * @property Log $log
 * @property QaTaskRepository $taskRepository
 * @property QaTaskCreateManuallyService $createManuallyService
 */
class QaTaskMultipleCreateService
{
    private $log;
    private $taskRepository;
    private $createManuallyService;

    public function __construct(
        QaTaskRepository $taskRepository,
        QaTaskCreateManuallyService $createManuallyService
    )
    {
        $this->log = new Log();
        $this->taskRepository = $taskRepository;
        $this->createManuallyService = $createManuallyService;
    }

    /**
     * @param QaTaskMultipleCreateForm $form
     * @return Log
     */
    public function create(QaTaskMultipleCreateForm $form): Log
    {
        $objectClass = QaTaskObjectType::getObjectClass($form->objectType);

        foreach ($form->ids as $id) {

            /** @var Objectable $object */
            if (!$object = $objectClass::findOne($id)) {
                $this->log->add(new ErrorMessage($form->objectType, $id,'not found'));
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
                $this->log->add(new SuccessMessage($form->objectType, $id,'Task created'));
            } catch (\DomainException $e) {
                $this->log->add(new ErrorMessage($form->objectType, $id, $e->getMessage()));
            } catch (\Throwable $e) {
                \Yii::error(QaTaskObjectType::getName($form->objectType) . ' Id: ' . $id  . PHP_EOL . $e, 'QaTaskMultipleCreateService:create');
                $this->log->add(new ErrorMessage($form->objectType, $id, 'Server error'));
            }

        }

        return $this->log;
    }
}
