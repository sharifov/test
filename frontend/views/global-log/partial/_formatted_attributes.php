<?php
use common\models\GlobalLog;
/**
 * @var GlobalLog $model
 */
?>

<p><?= $model->getActionTypeName() ?></p>
<table class="table table-bordered table-hover table-condensed">
    <tbody>
    <tr>
        <th>Field</th>
        <th style="width: 40%;">Old Value</th>
        <th style="width: 40%;">New Value</th>
    </tr>
    </tbody>
    <tbody>
    <?php $formattedAttributes = @json_decode($model->gl_formatted_attr, true); ?>
	<?php foreach ($formattedAttributes as $fieldName => $data) : ?>
        <tr>
            <th>
				<?= \yii\helpers\Html::encode($fieldName) ?>
            </th>
            <td style="width: 40%; word-break: break-word;">
                <span class="item-new">
                    <?= isset($data[0]) ? \yii\helpers\Html::encode($data[0]) : '-' ?>
                </span>
            </td>
            <td style="width: 40%; word-break: break-word;">
                <span class="item-old">
                    <?= isset($data[1]) ? \yii\helpers\Html::encode($data[1]) : '-' ?>
                </span>
            </td>
        </tr>
	<?php endforeach; ?>


    </tbody>
</table>

