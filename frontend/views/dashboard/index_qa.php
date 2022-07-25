<?php

use src\access\EmployeeProjectAccess;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Dashboard - QA';
?>


<?php
$userId = Yii::$app->user->id;
?>

<div class="site-index">

    <h1><?= $this->title ?></h1>
    <div class="row">
        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Server Date Time (UTC)</th>
                        <td><i class="fa fa-calendar"></i> <?= date('Y-M-d [H:i]') ?></td>
                    </tr>
                    <tr>
                        <th>Current Time Zone</th>
                        <td><i class="fa fa-globe"></i> <?= Yii::$app->formatter->timeZone ?></td>
                    </tr>
                    <tr>
                        <th>Formatted Local Date Time</th>
                        <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(time()) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>My Username:</th>
                        <td><i class="fa fa-user"></i> <?= Yii::$app->user->identity->username ?>
                            (<?= Yii::$app->user->id ?>)
                        </td>
                    </tr>
                    <tr>
                        <th>My Role:</th>
                        <td><?= implode(', ', Yii::$app->user->identity->getRoles()) ?></td>
                    </tr>
                    <tr>
                        <th>My User Groups:</th>
                        <td><i class="fa fa-users"></i>
                            <?php
                            $groupsValue = '';
                            if ($groupsModel = Yii::$app->user->identity->ugsGroups) {
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
                        <th>My Project Access:</th>
                        <td><i class="fa fa-list"></i>
                            <?php
                            $projectsValue = '';

                            if ($projectList = EmployeeProjectAccess::getProjects(Yii::$app->user->id)) {
                                $groupsValueArr = [];
                                foreach ($projectList as $project) {
                                    $groupsValueArr[] = Html::tag('span', Html::encode($project), ['class' => 'label label-default']);
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

    <?php /*




    <div class="">
        <div class="row top_tiles">

            <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-users"></i></div>
                    <div class="count">
                        <?=\common\models\UserOnline::find()->count()?> / <?=\common\models\UserConnection::find()->count()?>
                    </div>
                    <h3>Online Employees</h3>
                    <p>Current state Online Employees / Connections</p>
                </div>
            </div>

           <?php /*
                                <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="tile-stats">
                                        <div class="icon"><i class="fa fa-list"></i></div>
                                        <div class="count"><?=\common\models\Lead::find()->where("DATE(created) = DATE(NOW())")->count()?></div>
                                        <h3>Leads</h3>
                                        <p>Today count of Leads</p>
                                    </div>
                                </div>

                                <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <div class="tile-stats">
                                        <div class="icon"><i class="fa fa-cubes"></i></div>
                                        <div class="count"><?=\common\models\Quote::find()->where("DATE(created) = DATE(NOW())")->count()?></div>
                                        <h3>Quotes</h3>
                                        <p>Today count of Quotes</p>
                                    </div>
                                </div>
            ?>

            <?php /*
                                <div class="animated flipInY col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                    <div class="tile-stats">
                                        <div class="icon"><i class="fa fa-sitemap"></i></div>
                                        <div class="count"><?=\common\models\ApiLog::find()->where("DATE(al_request_dt) = DATE(NOW())")->count()?></div>
                                        <h3>API Requests</h3>
                                        <p>Today count of API Requests</p>
                                    </div>
                                </div>
            ?>

            <div class="animated flipInY col-md-3 col-sm-2 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-phone"></i></div>
                    <div class="count">
                        <?= \common\models\Call::find()->where('DATE(c_created_dt) = DATE(NOW())')->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_OUT])->count() ?>
                        /
                        <?= \common\models\Call::find()->where('DATE(c_created_dt) = DATE(NOW())')->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_IN])->count() ?>
                    </div>
                    <h3><?= Html::a('Calls', ['call/index']) ?>
                        (<?= number_format(\common\models\Call::find()->where('DATE(c_created_dt) = DATE(NOW())')->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_OUT])->sum('c_price'), 3) ?>
                        $) / In</h3>
                    <p>Today count of Calls Outgoing / Incoming</p>
                </div>
            </div>

            <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-comment"></i></div>
                    <div class="count">
                        <?= \common\models\Sms::find()->where('DATE(s_created_dt) = DATE(NOW())')->andWhere(['s_type_id' => \common\models\SMS::TYPE_OUTBOX])->count() ?>
                        /
                        <?= \common\models\Sms::find()->where('DATE(s_created_dt) = DATE(NOW())')->andWhere(['s_type_id' => \common\models\SMS::TYPE_INBOX])->count() ?>
                    </div>
                    <h3><?= Html::a('SMS', ['sms/index']) ?> Out
                        (<?= number_format(\common\models\Sms::find()->where('DATE(s_created_dt) = DATE(NOW())')->andWhere(['s_type_id' => \common\models\SMS::TYPE_OUTBOX])->sum('s_tw_price'), 3) ?>
                        $) / In</h3>
                    <p>Today count of SMS Outgoing / Incoming</p>
                </div>
            </div>


            <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-envelope"></i></div>
                    <div class="count"><?= s\rc\repositories\email\EmailRepositoryFactory::getRepository()->getTodayCount() ?></div>
                    <h3><?= Html::a('Emails', ['email/index']) ?></h3>
                    <p>Today count of Emails</p>
                </div>
            </div>


            <?php /*
                                    <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-list"></i></div>
                                            <div class="count"><?=\frontend\models\Log::find()->where("log_time BETWEEN ".strtotime(date('Y-m-d'))." AND ".strtotime(date('Y-m-d H:i:s')))->count()?></div>
                                            <h3>System Logs</h3>
                                            <p>Today count of System Logs</p>
                                        </div>
                                    </div>
             ?>
        </div>

    </div>

        */
    ?>

</div>