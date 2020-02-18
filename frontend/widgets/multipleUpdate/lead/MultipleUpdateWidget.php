<?php

namespace frontend\widgets\multipleUpdate\lead;

use common\models\Employee;
use frontend\widgets\multipleUpdate\MultipleUpdateHelper;
use yii\base\Widget;

/**
 * Class MultipleUpdateWidget
 *
 * @property string $validationUrl
 * @property string $action
 * @property string $modalId
 * @property string $ids
 * @property Employee $user
 * @property string $pjaxId for reload
 * @property string $script
 * @property string $formId
 * @property string $buttonHeader
 * @property string $notifyHeader
 * @property string $summaryIdentifier
 */
class MultipleUpdateWidget extends Widget
{
    public $validationUrl;
    public $action;
    public $modalId;
    public $ids;
    public $user;

    public $pjaxId;
    public $script;
    public $formId = 'multiple-update-form';
    public $buttonHeader = 'Update';
    public $notifyHeader = 'Multiple update';
    public $summaryIdentifier = '.multiple-update-summary .card-body';

    public function init(): void
    {
        parent::init();

        if ($this->validationUrl === null) {
            throw new \InvalidArgumentException('validationUrl must be set');
        }
        if ($this->action === null) {
            throw new \InvalidArgumentException('action must be set');
        }
        if ($this->modalId === null) {
            throw new \InvalidArgumentException('modalId must be set');
        }
        if ($this->ids === null) {
            throw new \InvalidArgumentException('ids must be set');
        }
        if ($this->user === null) {
            throw new \InvalidArgumentException('user must be set');
        }
        if (!$this->user instanceof Employee) {
            throw new \InvalidArgumentException('user must be instanceof Employee');
        }
        MultipleUpdateHelper::validateIds($this->ids);
    }

    public function run(): string
    {
        $form = new MultipleUpdateForm($this->user, ['ids' => $this->ids]);

        return $this->render('_multiple_update', [
            'updateForm' => $form,
            'validationUrl' => $this->validationUrl,
            'action' => $this->action,
            'modalId' => $this->modalId,
            'pjaxId' => $this->pjaxId,
            'script' => $this->script,
            'formId' => $this->formId,
            'buttonHeader' => $this->buttonHeader,
            'notifyHeader' => $this->notifyHeader,
            'summaryIdentifier' => $this->summaryIdentifier
        ]);
    }
}
