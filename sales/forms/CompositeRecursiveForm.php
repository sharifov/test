<?php

namespace sales\forms;

use yii\base\Model;

abstract class CompositeRecursiveForm extends CompositeForm
{
    public function load($data, $formName = null, $forms = [])
    {
        $success = parent::load($data, $formName);
        foreach (($forms ?: $this->_forms) as $name => $form) {
            if (is_array($form)) {
                $success = Model::loadMultiple($form, $data, $formName === null ? null : $name) || $success;
            } else {
                $success = $form->load($data, $formName !== '' ? null : $name) || $success;

                if (get_parent_class($form) === __CLASS__) {
                    $success = $this->load($data[$name] ?? [], $name, $form->getForms());
                }
            }
        }
        return $success;
    }

    public function getForms(): array
    {
        return $this->_forms;
    }
}
