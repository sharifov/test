<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $users */

$this->title = 'User Map';
$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if($users):?>
    <table class="table table-bordered table-striped table-hover">
        <tr>
            <th>Id</th>
            <th>Username</th>
            <th>Is Ready</th>
            <th>Last Call Status</th>
            <th>SIP</th>
            <th>Calls Count</th>
        </tr>
    <?php foreach ($users as $userMap):
        /*tbl_user_id' => '1'
        'tbl_call_status_id' => '2'
        'tbl_last_call_status' => 'queued'
        'tbl_sip_id' => null
        'tbl_calls_count' => '0'*/

            $user = \common\models\Employee::findOne($userMap['tbl_user_id']);
    ?>
        <tr>
            <td><?=$user->id?></td>
            <td><?=$user->username?></td>
            <td><?=($userMap['tbl_call_status_id'] == 1 ? 'Ready' : 'Occupied')?></td>
            <td><?=$userMap['tbl_last_call_status']?></td>
            <td><?=$userMap['tbl_sip_id']?></td>
            <td><?=$userMap['tbl_calls_count']?></td>
        </tr>
    <?php endforeach; ?>
    </table>
    <?php endif; ?>

    <?php
        //\yii\helpers\VarDumper::dump($users, 10, true);
    ?>

</div>
