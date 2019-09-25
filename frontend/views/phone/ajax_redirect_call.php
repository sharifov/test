<?php

use common\models\DepartmentPhoneProject;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $users \common\models\Employee[] */
/* @var $phones array */
/* @var $departments DepartmentPhoneProject[] */
/* @var $call \common\models\Call */
/* @var $error string */
?>
<div class="ajax-redirect-call">
    <?php if($error):?>
        <pre><?=$error?></pre>
    <?php else: ?>

        <?php if ($phones):?>
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
                <?php foreach ($phones as $pk => $phone): ?>
                    <tr>
                        <td class="text-right"><?=$n++?>.</td>

                        <td><b><?=Html::encode($pk)?></b></td>
                        <td><i class="fa fa-phone"></i> <?=Html::encode($phone)?></td>
                        <td class="text-center">

                            <?=Html::button('<i class="fa fa-forward"></i> Redirect', [
                                'class' => 'btn btn-xs btn-success btn-transfer-number',
                                'data-type' => 'number',
                                'data-value' => Html::encode($phone),
                               ])?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        <?php endif;?>




        <?php if ($departments):?>
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
                <?php foreach ($departments as $department): ?>
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
                        <td><i class="fa fa-phone"></i> <?=Html::encode($department->dpp_phone_number)?></td>
                        <td class="text-center">
                            <?=Html::button('<i class="fa fa-forward"></i> Redirect', [
                                'class' => 'btn btn-xs btn-success btn-transfer',
                                'data-type' => 'department',
                                'data-value' => $department->dpp_id,
                            ])?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        <?php endif;?>



        <?php if ($users):?>
            <h2><i class="fa fa-users"></i> Users:</h2>
            <table class="table table-bordered table-hover" style="margin: 0">
                <thead>
                <tr class="bg-info">
                    <th style="width: 40px" class="text-center">Nr</th>
                    <th style="width: 150px" class="text-center">Username</th>
                    <th class="text-center">Roles</th>
                    <th class="text-center" style="width: 100px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $n = 1; ?>
                <?php foreach ($users as $userModel): ?>
                    <?php
                        $roles = $userModel->getRoles();
                        $isReady = $userModel->isCallStatusReady();

                        $btnClass = 'btn-success';

                        if (!$isReady) {
                            $btnClass = 'btn-warning';
                        }

                    ?>
                    <tr>
                        <td class="text-right"><?=$n++;?>.</td>
                        <td><i class="fa fa-user"></i> <b><?=Html::encode($userModel->username)?></b></td>
                        <td><?=( is_array($roles) ? implode(', ', $roles) : '-')?></td>
                        <td class="text-center">
                            <?=Html::button('<i class="fa fa-forward"></i> Redirect', [
                                    'class' => 'btn btn-xs ' . $btnClass . ' btn-transfer',
                                    'data-type' => 'user',
                                    //'data-user_id' => $userModel->id,
                                    'data-value' => $userModel->id,
                                    // 'data-project_id' => $call->c_project_id,
                                    // 'data-dep_id' => $call->c_dep_id
                            ])?>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        <?php endif;?>
    <?php endif;?>
</div>