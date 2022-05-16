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
                    <?php
                    if (isset($data[0])) {
                        echo is_array($data[0]) ? '<pre>' . implode(', ', $data[0]) . '</pre>' : \yii\helpers\Html::encode($data[0]);
                    } else {
                        echo '-';
                    }
                    ?>
                </span>
            </td>
            <td style="width: 40%; word-break: break-word;">
                <span class="item-old">
                    <?php
                    if (isset($data[1])) {
                        echo is_array($data[1]) ? '<pre>' . implode(', ', $data[1]) . '</pre>' : \yii\helpers\Html::encode($data[1]);
                    } else {
                        echo '-';
                    }
                    ?>
                </span>
            </td>
        </tr>
    <?php endforeach; ?>


    </tbody>
</table>

