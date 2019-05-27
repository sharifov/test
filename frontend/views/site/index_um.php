<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'Dashboard - User Manager';
?>


<?php
$userId = Yii::$app->user->id;
?>

<div class="site-index">

    <h1><?=$this->title?></h1>
    <div class="row">
        <div class="col-md-3">
            <table class="table table-bordered">
                <tr>
                    <th>Server Date Time (UTC)</th>
                    <td><i class="fa fa-calendar"></i> <?= date('Y-M-d [H:i]')?></td>
                </tr>
                <tr>
                    <th>Current Time Zone</th>
                    <td><i class="fa fa-globe"></i> <?= Yii::$app->formatter->timeZone?></td>
                </tr>
                <tr>
                    <th>Formatted Local Date Time</th>
                    <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(time())?></td>
                </tr>
            </table>

        </div>

        <div class="col-md-3">
            <table class="table table-bordered">
                <tr>
                    <th>My Username:</th>
                    <td><i class="fa fa-user"></i> <?= Yii::$app->user->identity->username?> (<?=Yii::$app->user->id?>)</td>
                </tr>
                <tr>
                    <th>My Role:</th>
                    <td><?=implode(', ', Yii::$app->user->identity->getRoles())?></td>
                </tr>
            </table>

        </div>


    </div>



    <div class="">
        <div class="row top_tiles">

            <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-users"></i></div>
                    <div class="count">
                        <?=\common\models\UserConnection::find()->select('uc_user_id')->groupBy(['uc_user_id'])->count()?> /
                        <?=\common\models\UserConnection::find()->count()?>
                    </div>
                    <h3>Online Employees</h3>
                    <p>Current state Online Employees / Connections</p>
                </div>
            </div>


        </div>

    </div>

</div>