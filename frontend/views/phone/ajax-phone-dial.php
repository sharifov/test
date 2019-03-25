<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $project \common\models\Project */
/* @var $phone_number string */
/* @var $model common\models\Client */
/* @var $isAgent bool */
/* @var $fromPhoneNumbers [] */
/* @var $lead_id int */
/* @var $selectProjectPhone string */

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
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'updated',
                        'value' => function(\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
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
                            'readonly' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? false : true,
                            'disable' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? false : true
                        ])
                    ?>
                    <?=Html::hiddenInput('call-lead-id', $lead_id, ['id' => 'call-lead-id'])?>
                    <?=Html::hiddenInput('call-project-id', $project ? $project->id : '', ['id' => 'call-project-id'])?>
                </td>
                <td>
                    <?=\yii\helpers\Html::button('<i class="fa fa-phone-square"></i> Make Call', ['class' => 'btn btn-sm btn-success', 'id' => 'btn-make-call'])?>
                </td>
            </tr>
        </table>
        </div>
    </div>

</div>
