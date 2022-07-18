<?php

use common\components\grid\Select2Column;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use src\auth\Auth;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $users \common\models\Employee[] */
/* @var $phones array */
/* @var $departments DepartmentPhoneProject[] */
/* @var $call \common\models\Call */
/* @var $error string */
/* @var $canWarmTransfer bool */
?>
<style>
    #user-redirect-grid .select2-selection__rendered {
        width: 150px
    }
</style>
<div class="ajax-redirect-call">
    <?php if ($error) :?>
        <pre><?=$error?></pre>
    <?php else : ?>
        <?php if ($phones) :?>
            <h2><i class="fa fa-phone"></i> Phone Numbers:</h2>
            <table class="table table-bordered table-hover" style="margin: 0">
                <thead>
                <tr class="bg-info">
                    <th style="width: 40px" class="text-center">Nr</th>
                    <th class="text-center">Name</th>
                    <th style="width: 200px" class="text-center">Phone</th>
                    <th class="text-center" style="width: 100px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $n = 1; ?>
                <?php foreach ($phones as $pk => $phone) : ?>
                    <tr>
                        <td class="text-right"><?=$n++?>.</td>

                        <td><b><?=Html::encode($pk)?></b></td>
                        <td><i class="fa fa-phone"></i> <?=Html::encode($phone)?></td>
                        <td class="text-center">

                            <?=Html::button('<i class="fa fa-forward"></i> Redirect', [
                                'class' => 'btn btn-xs btn-success btn-transfer-number',
                                'data-type' => 'number',
                                'data-value' => Html::encode($phone),
                                'data-call-sid' => $call->c_call_sid
                               ])?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        <?php endif;?>




        <?php if ($departments) :?>
            <h2><i class="fa fa-list"></i> Departments:</h2>
            <table class="table table-bordered table-hover" style="margin: 0">
                <thead>
                <tr class="bg-info">
                    <th style="width: 40px" class="text-center">Nr</th>
                    <th class="text-center">Project</th>
                    <th class="text-center">Department</th>
                    <th class="text-center">Team</th>
                    <th style="width: 150px" class="text-center">Phone</th>
                    <th class="text-center" style="width: 100px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $n = 1; ?>
                <?php foreach ($departments as $department) : ?>
                    <tr>
                        <td class="text-right"><?=$n++?>.</td>

                        <td><?=$department->dppProject ? Html::encode($department->dppProject->name) : ''?></td>
                        <td><b><?=$department->dppDep ? Html::encode($department->dppDep->dep_name) : ''?></b></td>
                        <td>
                            <?php
                                $userGroupList = [];
                            if ($department->dugUgs) {
                                foreach ($department->dugUgs as $userGroup) {
                                    $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
                                }
                            }
                                echo $userGroupList ? implode(' ', $userGroupList) : '-';
                            ?>
                        </td>
<!--                        <td><i class="fa fa-phone"></i> --><?php //=Html::encode($department->dpp_phone_number) ?><!--</td>-->
                        <td><i class="fa fa-phone"></i> <?=Html::encode($department->getPhone())?> </td>
                        <td class="text-center">
                            <?php if (Auth::can('PhoneWidget_TransferToDepartment')) : ?>
                                <?= Html::button('<i class="fa fa-forward"></i> Redirect', [
                                    'class' => 'btn btn-xs btn-success btn-transfer',
                                    'data-type' => 'department',
                                    'data-value' => $department->dpp_id,
                                    'data-call-sid' => $call->c_call_sid
                                ])?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        <?php endif;?>

        <?php Pjax::begin(['id' => 'redirect-call-employee-pjax', 'enablePushState' => false]); ?>

            <h2><i class="fa fa-users"></i> Users:</h2>

            <?php
            echo \yii\grid\GridView::widget([
                'id' => 'user-redirect-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'filterUrl' => \yii\helpers\Url::to(['/phone/ajax-call-get-agents', 'sid' => $call->c_call_sid]),
                'showHeader' => true,
                'summary' => false,
                'tableOptions' => ['class' => 'table table-bordered table-hover'],
                'rowOptions' => function ($user) {
                    $isBusy = $user['isBusy'];
                    if ($isBusy) {
                        return ['style' => 'background-color: #ccc'];
                    }
                },
                'headerRowOptions' => ['class' => 'bg-info'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn', 'header' => 'Nr', 'headerOptions' => ['style' => 'width:40px', 'class' => 'text-center']],
                    [
                        'attribute' => 'username',
                        'format' => 'raw',
                        'headerOptions' => ['style' => 'width:150px', 'class' => 'text-center'],
                        'value' => function ($user) {
                            $userModel = $user['model'];
                            $isBusy = $user['isBusy'];

                            return '<i class="fa fa-user"></i> <b>' . Html::encode($userModel->username) . '</b>' . ($isBusy ? ' (Busy)' : '');
                        }],
                    [
                        'class' => Select2Column::class,
                        'attribute' => 'roles',
                        'id' => 'roles-search',
                        'format' => 'raw',
                        'value' => function ($user) {
                            $userModel = $user['model'];
                            $roles = $userModel->getRoles();
                            return (is_array($roles) ? implode(', ', $roles) : '-');
                        },
                        'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                        'data' => Employee::getAllRoles(Auth::user()),
                    ],
                    [
                        'attribute' => 'action',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width:100px'],
                        'contentOptions' => ['class' => 'text-center'],
                        'value' => function ($user) use ($call, $canWarmTransfer) {
                            $action = '';
                            $userModel = $user['model'];
                            $isBusy = $user['isBusy'];

                            $isReady = $userModel->isCallStatusReady();

                            if ($isBusy) {
                                $btnClass = 'btn-danger';
                            } elseif (!$isReady) {
                                $btnClass = 'btn-warning';
                            } else {
                                $btnClass = 'btn-success';
                            }
                            if (Auth::can('PhoneWidget_TransferToUser', ['call' => $call])) {
                                $action .= Html::button('<i class="fa fa-forward"></i> Redirect', [
                                    'class' => 'btn btn-xs ' . $btnClass . ' btn-transfer',
                                    'data-type' => 'user',
                                    'data-value' => $userModel->id,
                                    'data-call-sid' => $call->c_call_sid
                                ]);
                            }
                            if ($canWarmTransfer && Auth::can('PhoneWidget_WarmTransferToUser')) {
                                $action .= Html::button('<i class="fa fa-feed"></i> Warm transfer', [
                                    'class' => 'btn btn-xs ' . $btnClass . ' btn-warm-transfer-to-user',
                                    'data-user-id' => $userModel->id,
                                    'data-call-sid' => $call->c_call_sid
                                ]);
                            }
                            return $action;
                        }],
                ],
            ]);
            ?>
        <?php Pjax::end() ?>
    <?php endif; ?>
</div>