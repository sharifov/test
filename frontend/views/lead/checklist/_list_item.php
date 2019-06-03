<?php
/* @var $this yii\web\View */
/* @var $model \common\models\LeadChecklist */
/* @var $index integer */
?>


<tr>
    <td style="width: 40px">
        <?php echo ($index + 1)?>.
    </td>
    <td title="<?=$model->lcType ? \yii\helpers\Html::encode($model->lcType->lct_description) : '-'?>">
        <span class="fa fa-check-square-o success"></span>
        <?=$model->lcType ? \yii\helpers\Html::encode($model->lcType->lct_name) : '-'?>
    </td>
    <?php /*<td>
        <?=$model->lc_notes ? \yii\helpers\Html::encode($model->lc_notes) : '-'?>
    </td>*/ ?>
    <td style="width: 160px">
        <i class="fa fa-calendar"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->lc_created_dt)) ?>
    </td>
    <td style="width: 150px">
        <i class="fa fa-user"></i> <?=$model->lcUser ? $model->lcUser->username : '-'?>
    </td>
</tr>
