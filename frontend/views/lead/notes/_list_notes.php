<?php
/* @var $this yii\web\View */
/* @var $model \common\models\Note */
/* @var $index integer */

use yii\web\View;
?>
<table class="table table-striped table-bordered">
    <tr>
        <td style="width: 40px"><?= $index + 1 ?>.</td>
        <td><i class="fa fa-user"></i> <?=$model->employee->username ? $model->employee->username . ' (' . $model->employee->id . ')': '-'?>,
            <i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->created)) ?>
        </td>
    </tr>
    <tr>
        <td><i class="fa fa-sticky-note-o" aria-hidden="true"></i></td>
        <td><?=nl2br($model->message ? \yii\helpers\Html::encode($model->message) : '-')?></td>
    </tr>
</table>