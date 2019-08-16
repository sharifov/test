<?php
/* @var $this yii\web\View */
/* @var $model \common\models\CaseNote */
/* @var $index integer */

use yii\web\View;
?>
<table class="table table-striped table-bordered">
    <tr>
        <td title="ID: <?=$model->cn_cs_id?>">
            <i class="fa fa-user"></i> <?=$model->cnUser ? \yii\helpers\Html::encode($model->cnUser->username): '-'?>,
            <i class="fa fa-calendar"></i> <?=$model->cn_created_dt ? Yii::$app->formatter->asDatetime(strtotime($model->cn_created_dt)) : '' ?>
        </td>
    </tr>
    <tr>
        <td><?=$model->cn_text ? nl2br(\yii\helpers\Html::encode($model->cn_text)) : '-'?></td>
    </tr>
</table>