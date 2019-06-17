<?php

use unclead\multipleinput\MultipleInput;
use yii\widgets\ActiveForm;
use \unclead\multipleinput\MultipleInputColumn;

/**
 * @var $this yii\web\View
 * @var $form ActiveForm
 * @var $leadForm sales\forms\lead\LeadCreateForm
 */

?>

<div class="sidebar__section">
    <h3 class="sidebar__subtitle">
        <i class="fa fa-user"></i>
    </h3>
    <div class="sidebar__subsection">

        <?= $form->field($leadForm->client, 'firstName')->textInput() ?>

        <?= $form->field($leadForm->client, 'middleName')->textInput() ?>

        <?= $form->field($leadForm->client, 'lastName')->textInput() ?>

    </div>

    <div class="sidebar__subsection">
        <div id="client-emails">
            <?= $form->field($leadForm, 'emails')->widget(MultipleInput::class, [
                'max' => 10,
                'enableError' => true,
                'columns' => [
                    [
                        'name' => 'email',
                        'title' => 'Email',
                    ],
                    [
                        'name' => 'help',
                        'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT
                    ],
                ]
            ])->label(false) ?>
        </div>
    </div>

    <div class="sidebar__subsection">
        <div id="client-phones">
            <?= $form->field($leadForm, 'phones')->widget(MultipleInput::class, [
                'max' => 10,
                'enableError' => true,
                'columns' => [
                    [
                        'name' => 'phone',
                        'title' => 'Phone',
                    ],
                    [
                        'name' => 'help',
                        'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT
                    ],
                ]
            ])->label(false) ?>
        </div>
    </div>

    <div class="sidebar__subsection">
        <?= $form->field($leadForm, 'requestIp')->textInput() ?>
    </div>

</div>