<?php
/* @var $this yii\web\View */
/* @var $model \common\models\Note */
/* @var $index integer */

use yii\web\View;
?>
<table class="table table-striped table-bordered">
    <tr>
        <td title="ID: <?=$model->id?>">
            <i class="fa fa-user"></i> <?=$model->employee ? \yii\helpers\Html::encode($model->employee->username): '-'?>,
            <i class="fa fa-calendar"></i> <?=$model->created ? Yii::$app->formatter->asDatetime(strtotime($model->created)) : '' ?>
        </td>
    </tr>
    <tr>
        <td><?=nl2br($model->message ? \yii\helpers\Html::encode($model->message) : '-')?></td>
    </tr>
</table>