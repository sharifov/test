<?php
/* @var $this yii\web\View */
/* @var $model \common\models\Note */
/* @var $index integer */

use yii\web\View;
?>

<tr>
    <td style="width: 40px">
        <?php echo ($index + 1)?>.
    </td>
    <td style="width: 150px">
        <i class="fa fa-user"></i> <?=$model->employee->username ? $model->employee->username : '-'?>
    </td>
    <td style="width: 160px">
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->created)) ?>
    </td>
    <td>
        <?=$model->message ? \yii\helpers\Html::encode($model->message) : '-'?>
    </td>
</tr>
