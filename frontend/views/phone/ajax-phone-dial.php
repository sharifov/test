<?php

use common\models\Employee;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $project \common\models\Project */
/* @var $phone_number string */
/* @var $model common\models\Client */
/* @var $fromPhoneNumbers [] */
/* @var $lead_id int */
/* @var $case_id int */
/* @var $selectProjectPhone string */
/* @var $currentCall \common\models\Call */

/** @var Employee $user */
$user = Yii::$app->user->identity;

?>
<div class="phone-update">

    <?php if($model): ?>
    <div class="row">
        <div class="col-md-6">
            <?= \yii\widgets\DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'first_name',
                    'middle_name',
                    'last_name',
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= \yii\widgets\DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Phones',
                        'value' => function(\common\models\Client $model) {

                            $phones = $model->clientPhones;
                            $data = [];
                            if($phones) {
                                foreach ($phones as $k => $phone) {
                                    $data[] = '<i class="fa fa-phone"></i> <code>'.Html::encode($phone->phone).'</code>'; //<code>'.Html::a($phone->phone, ['client-phone/view', 'id' => $phone->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                                }
                            }

                            $str = implode('<br>', $data);
                            return ''.$str.'';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],

                    [
                        'label' => 'Emails',
                        'value' => function(\common\models\Client $model) {

                            $emails = $model->clientEmails;
                            $data = [];
                            if($emails) {
                                foreach ($emails as $k => $email) {
                                    $data[] = '<i class="fa fa-envelope"></i> <code>'.Html::encode($email->email).'</code>';
                                }
                            }

                            $str = implode('<br>', $data);
                            return ''.$str.'';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],

                    //'created',
                    //'updated',

                    [
                        'attribute' => 'created',
                        'value' => function(\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDate(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'updated',
                        'value' => function(\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDate(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],
                ],
            ]) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
        <?php if($currentCall): ?>

            <div class="alert alert-warning" role="alert">
                <h5><i class="fa fa-warning"></i> Warning!</h5>
                <b>You can not call now. At the moment you have a call ID (<?=$currentCall->c_id?>). Please check that your web-phone line is free.</b>

            </div>

            <div class="text-center">
                <?=Html::a('<i class="fa fa-remove"></i> Emergency Cancel Call Process', ['call/ajax-call-cancel', 'id' => $currentCall->c_id], ['class' => 'btn btn-sm btn-danger', 'data' => [
                    'confirm' => 'Attention! This function is used only for emergency cases. ' . "\r\n". 'Are you sure you want to cancel this Call?',
                    'method' => 'post',
                ] ])?>
            </div>

            <div>
                <h2>Current Call (<?=$currentCall->c_id?>)</h2>
                <?= \yii\widgets\DetailView::widget([
                    'model' => $currentCall,
                    'attributes' => [
                        //'c_id',
                        'c_call_sid',
                        [
                            'attribute' => 'c_call_type_id',
                            'value' => static function (\common\models\Call $model) {
                                return $model->getCallTypeName();
                            },
                        ],
                        'c_from',
                        'c_to',
                        //'c_call_status',
                        [
                            'attribute' => 'c_status_id',
                            'value' => static function (\common\models\Call $model) {
                                return $model->getStatusName();
                            },
                        ],
                        'c_lead_id',
                        'c_case_id',
                        //'c_dep_id',
                        [
                            'attribute' => 'c_dep_id',
                            'value' => static function (\common\models\Call $model) {
                                return $model->cDep ? $model->cDep->dep_name : '-';
                            },
                        ],
                        //'c_caller_name',
                        [
                            'attribute' => 'c_project_id',
                            'value' => static function (\common\models\Call $model) {
                                return $model->cProject ? $model->cProject->name : '-';
                            },
                        ],
                        [
                            'attribute' => 'c_created_dt',
                            'value' => static function (\common\models\Call $model) {
                                return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                            },
                            'format' => 'raw'
                        ],
                    ],
                ]) ?>
            </div>

            <?php /*<div class="alert alert-info" role="alert">
                <p>If there is no call and an error has occurred, please inform the manager of the problem.</p>
            </div>*/?>



        <?php else: ?>
            <h2>Call</h2>
            <table class="table table-bordered">
                <tr>
                    <td>
                        From:
                    </td>
                    <td>
                        To:
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?=Html::dropDownList('call-from-number', $selectProjectPhone, $fromPhoneNumbers, ['id' => 'call-from-number', 'class' => 'form-control'])?>
                    </td>
                    <td>
                        <?=Html::textInput('call-to-number', $phone_number, ['id' => 'call-to-number', 'class' => 'form-control',
                                'readonly' => !$user->isAdmin(),
                                'disable' => !$user->isAdmin()
                            ])
                        ?>
                        <?=Html::hiddenInput('call-lead-id', $lead_id, ['id' => 'call-lead-id'])?>
                        <?=Html::hiddenInput('call-case-id', $case_id, ['id' => 'call-case-id'])?>
                        <?=Html::hiddenInput('call-project-id', $project ? $project->id : '', ['id' => 'call-project-id'])?>
                    </td>
                    <td>
                        <?=\yii\helpers\Html::button('<i class="fa fa-phone-square"></i> Make Call', ['class' => 'btn btn-sm btn-success', 'id' => 'btn-make-call'])?>
                    </td>
                </tr>
            </table>
        <?php endif; ?>
        </div>
    </div>

</div>
