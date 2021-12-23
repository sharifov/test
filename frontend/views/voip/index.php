<?php

use common\models\Employee;
use frontend\widgets\newWebPhone\DeviceAsset;
use frontend\widgets\newWebPhone\DeviceStorageKey;
use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $user Employee */
/** @var $this View */

DeviceAsset::register($this);

$this->title = 'VoIP - Phone Device';
$this->params['breadcrumbs'][] = $this->title;

$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon',
    'href' => Yii::$app->request->baseUrl . '/img/phone.ico', 'key' => 'icon']);
?>
<div class="voip-index">
    <h1>
        <i class="fa fa-phone-square"></i>
        <?= Html::encode($this->title) ?>
    </h1>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Phone Device</h2>
                    <ul class="nav navbar-right panel_toolbox">

                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="bs-example">
                        <div class="jumbotron">
                            <h1>VoIP - Phone Device</h1>
                            <p>This page should be open whenever you want to make and receive calls.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
        <?php if ($user->userProjectParams) : ?>
            <div class="x_panel">
                <div class="x_title" >
                    <h2><i class="fa fa-sticky-note-o"></i> Params</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>Project</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Allow General Line</th>
                        </tr>
                        <?php foreach ($user->userProjectParams as $projectParam) :?>
                            <tr>
                                <td><?=Html::encode($projectParam->uppProject->name)?></td>
                                <td><?= Yii::$app->formatter->asPhoneList($projectParam->phoneList) ?></td>
                                <td><?=($projectParam->uppDep ? $projectParam->uppDep->dep_name : '-')?></td>
                                <td><?=$projectParam->upp_allow_general_line ? '<i class="fa fa-check-square-o"> </i>' : '-'; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        </div>
        <div class="col-md-6">
            <div class="x_panel">
                <div class="x_title" >
                    <h2><i class="fa fa-sticky-note-o"></i> Device status</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>Twilio</th>
                            <th>Speaker</th>
                            <th>Microphone</th>
                        </tr>
                        <tr>
                            <td><i class="fa fa-square-o phone-device-twilio-status"> </i></span></td>
                            <td><i class="fa fa-square-o phone-device-speaker-status"> </i></span></td>
                            <td><i class="fa fa-square-o phone-device-microphone-status"> </i></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


<?php
$phoneDeviceRemoteLogsEnabled = SettingHelper::phoneDeviceLogsEnabled() ? 'true' : 'false';
$phoneDeviceIdStorageKey = DeviceStorageKey::getPhoneDeviceIdStorageKey(Auth::id());

$js = <<<JS
window.isTwilioDevicePage = true;
window.phoneDeviceIdStorageKey = '$phoneDeviceIdStorageKey';
window.phoneDeviceRemoteLogsEnabled = $phoneDeviceRemoteLogsEnabled;
JS;
$this->registerJs($js, View::POS_READY);
