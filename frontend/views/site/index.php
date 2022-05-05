<?php

use common\models\Employee;
use common\models\UserProjectParams;
use src\model\userAuthClient\entity\UserAuthClient;
use src\model\userAuthClient\entity\UserAuthClientSources;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user Employee */
/* @var $sourcesDataProvider \yii\data\ActiveDataProvider */


$this->title = 'Home Page'; // . $user->username;
?>


<div class="site-index">
    <h1><span class="fa fa-home"></span> <?=$this->title?></h1>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Welcome</h2>
                    <ul class="nav navbar-right panel_toolbox">

                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="bs-example">
                        <div class="jumbotron">
                            <h1>Hello, <?=Html::encode($user->username)?>!</h1>
                            <p></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="x_panel">
                <div class="x_title" >
                    <h2><i class="fa fa-user"></i> User Info</h2>

                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered">
                        <tr>
                            <th><i class="fa fa-user"></i> My Username:</th>
                            <td><?= Html::encode($user->username)?> (<?=$user->id?>)</td>
                        </tr>
                        <tr>
                            <th>My Role:</th>
                            <td><?=implode(', ', $user->getRoles())?></td>
                        </tr>
                        <tr>
                            <th>My Department:</th>
                            <td><?=implode(', ', $user->getUserDepartmentList())?></td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-users"></i> My User Groups:</th>
                            <td>
                                <?php
                                $groupsValue = '';
                                if ($groupsModel =  $user->ugsGroups) {
                                    $groups = \yii\helpers\ArrayHelper::map($groupsModel, 'ug_id', 'ug_name');
                                    $groupsValueArr = [];
                                    foreach ($groups as $group) {
                                        $groupsValueArr[] = Html::tag('span', Html::encode($group), ['class' => 'label label-default']);
                                    }
                                    $groupsValue = implode(' ', $groupsValueArr);
                                }
                                echo $groupsValue;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-list"></i> My Project Access:</th>
                            <td>
                                <?php
                                //\yii\helpers\VarDumper::dump(Yii::$app->user->identity->projects, 10, true);

                                $projectsValue = '';

                                //$projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
                                $projectList = $user->projects;

                                if ($projectList) {
                                    $groupsValueArr = [];
                                    foreach ($projectList as $project) {
                                        $groupsValueArr[] = Html::tag('span', Html::encode($project->name), ['class' => 'label label-default']);
                                    }
                                    $projectsValue = implode(' ', $groupsValueArr);
                                }
                                echo $projectsValue;
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="x_panel">
                <div class="x_title" >
                    <h2><i class="fa fa-cog"></i> Settings</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered">
                <tr>
                    <th><i class="fa fa-calendar"></i> Server Date Time (UTC)</th>
                    <td> <?= date('Y-M-d [H:i]')?></td>
                </tr>
                <tr>
                    <th><i class="fa fa-globe"></i> Current Time Zone</th>
                    <td><?= Yii::$app->formatter->timeZone?></td>
                </tr>
                <tr>
                    <th><i class="fa fa-calendar"></i> Local Date Time</th>
                    <td><?= Yii::$app->formatter->asDatetime(time())?></td>
                </tr>
            </table>
                </div>
            </div>
        </div>



        <div class="col-md-6">
            <?php if ($user->userProjectParams) : ?>
            <div class="x_panel">
                <div class="x_title" >
                    <h2><i class="fa fa-sticky-note-o"></i> Project params</h2>
<!--                    <ul class="nav navbar-right panel_toolbox">-->
<!--                        <li>-->
<!--                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>-->
<!--                        </li>-->
<!--                    </ul>-->
                    <div class="clearfix"></div>
                </div>
                <div class="x_content table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>Project</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Allow General Line</th>
                            <th>Voice mail enabled</th>
                            <th>Voice mail user status</th>
                            <th>Voice mail</th>
                        </tr>
                        <?php foreach ($user->userProjectParams as $projectParam) :?>
                            <tr>
                                <td><?=Html::encode($projectParam->uppProject->name)?></td>
<!--                                <td>--><?php //=Html::encode($projectParam->upp_email)?><!--</td>-->
                                <td><?= Yii::$app->formatter->asEmailLIst($projectParam->emailList) ?></td>
<!--                                <td>--><?php //=Html::encode($projectParam->upp_tw_phone_number)?><!--</td>-->
                                <td><?= Yii::$app->formatter->asPhoneList($projectParam->phoneList) ?></td>
                                <td><?=($projectParam->uppDep ? $projectParam->uppDep->dep_name : '-')?></td>
                                <td><?=$projectParam->upp_allow_general_line ? '<i class="fa fa-check-square-o"> </i>' : '-'; ?></span></td>
                                <td><?= $projectParam->upp_vm_enabled ? '<i class="fa fa-check-square-o"> </i>' : '-'; ?></td>
                                <td><?= UserProjectParams::VM_USER_STATUS_LIST[$projectParam->upp_vm_user_status_id] ?? '<span class="not-set">(not set)</span>'; ?></td>
                                <td><?= $projectParam->upp_vm_id ? $projectParam->voiceMail->uvm_name : '<span class="not-set">(not set)</span>'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <?php if (\src\helpers\setting\SettingHelper::isEnabledAuthClients()) : ?>
                <div class="x_panel">
                    <div class="x_title" >
                        <h2><i class="fa fa-sticky-note-o"></i> Auth clients</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <?= \yii\grid\GridView::widget([
                            'dataProvider' => $sourcesDataProvider,
                            'columns' => [
                                'uac_id',
                                [
                                    'attribute' => 'uac_source',
                                    'value' => static function (UserAuthClient $model) {
                                        return Html::encode(UserAuthClientSources::getName($model->uac_source));
                                    }
                                ],
                                'uac_email',
                                [
                                    'attribute' => 'uac_created_dt',
                                    'format' => 'byUserDateTime',
                                    'label' => 'When assigned'
                                ],
                            ],
                            'layout' => "{items}",
                        ]) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
