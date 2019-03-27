<?php
/**
 * @var $model Project
 */

use common\models\Project;
use yii\bootstrap\Html;
use yii\helpers\Url;

$js = <<<JS
    $('#email-templates, .edit-email-template-btn').click(function(e) {
        var url = $(this).data('url');
        var title = $(this).data('title');
        var editBlock = $('#modal-email-templates');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-title').html(title);
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            editBlock.modal('show');
        });
    });
JS;

$this->registerJs($js);

?>
<div class="panel panel-default">
    <div class="panel-heading">Project [<?= $model->name ?>]</div>
    <div class="panel-body">
        <div class="row mb-20">

            <div class="col-md-6">
                <h3>General Info</h3>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= Html::label($model->getAttributeLabel('name'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model, 'name', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= Html::label($model->getAttributeLabel('link'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model, 'link', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= Html::label($model->getAttributeLabel('api_key'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model, 'api_key', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                </div>


                <?= Html::label('Market source', null, [
                    'class' => 'control-label'
                ]) ?>
                <div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Source Name</th>
                                <th>CID</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model->sources as $subSource) : ?>
                            <tr>
                                <td><?= $subSource->id ?></td>
                                <td><?= $subSource->name ?></td>
                                <td><?= $subSource->cid ?></td>
                                <td><?= $subSource->phone_number ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <h3>Settings</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= Html::label($model->contactInfo->getAttributeLabel('phone'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model->contactInfo, 'phone', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= Html::label($model->contactInfo->getAttributeLabel('email'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model->contactInfo, 'email', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                    <?/*<div class="col-md-4">
                        <div class="form-group">
                            <?= Html::label($model->contactInfo->getAttributeLabel('password'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model->contactInfo, 'password', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>*/?>
                </div>
                <?/*
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= Html::label($model->contactInfo->getAttributeLabel('smtpHost'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model->contactInfo, 'smtpHost', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= Html::label($model->contactInfo->getAttributeLabel('smtpPort'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model->contactInfo, 'smtpPort', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= Html::label($model->contactInfo->getAttributeLabel('encryption'), null, [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::activeTextInput($model->contactInfo, 'encryption', [
                                'class' => 'form-control',
                                'readonly' => true,
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                </div>*/?>

                <div class=" mb-20">
                    <?= Html::button('Email Templates', [
                        'class' => 'btn-default btn',
                        'data-title' => 'ADD/EDIT Email Template',
                        'data-url' => Url::to([
                            'settings/email-template',
                            'id' => $model->id,
                            'templateId' => 0
                        ]),
                        'id' => 'email-templates'
                    ]) ?>
                </div>

                <div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Template Type</th>
                            <th>Subject</th>
                            <th>Updated</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model->getEmailTemplates() as $emailTemplate) : ?>
                            <tr>
                                <td><?= $emailTemplate::getTypes($emailTemplate->type) ?></td>
                                <td><?= $emailTemplate->subject ?></td>
                                <td><?= $emailTemplate->updated ?></td>
                                <td>
                                    <?= Html::a('<span class="glyphicon glyphicon-edit"></span>', '#', [
                                        'title' => 'Edit',
                                        'class' => 'edit-email-template-btn',
                                        'data-title' => 'ADD/EDIT Source',
                                        'data-url' => Url::to([
                                            'settings/email-template',
                                            'id' => $model->id,
                                            'templateId' => $emailTemplate->id
                                        ])
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal-email-templates" style="display: none;">
    <div class="modal-dialog" role="document" style="width: 1024px;">
        <div class="modal-content">
            <div class="modal-header">
                <?= Html::button('<span>×</span>', [
                    'class' => 'close',
                    'data-dismiss' => 'modal'
                ]) ?>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
