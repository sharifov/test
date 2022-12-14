<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\MaskedInput;
use common\models\GlobalAcl;
use yii\bootstrap\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $models \common\models\EmployeeAcl[]
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$js = <<<JS
    $('.change-acl-rule-status').change(function() {
        var r = confirm('Are you sure you want to change rule status?');
        if (r == true) {
            var value = ($(this).is(':checked')) ? 1 : 0;
            $.post($(this).data('url'), {'EmployeeAcl[active]': value});
        } else {
            $(this).prop('checked', !$(this).is(':checked'));
        }
    });
JS;

$this->registerJs($js);
?>

<?php if (!empty($models)) : ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>IP</th>
                <th>Active</th>
                <th>Description</th>
                <th>Created</th>
                <th>Updated</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($models as $model) : ?>
                <tr>
                    <td><?= $model->mask ?></td>
                    <td>
                        <?= Html::activeCheckbox($model, 'active', [
                            'class' => 'change-acl-rule-status',
                            'label' => false,
                            'data-url' => Yii::$app->urlManager->createUrl(['employee/acl-rule', 'id' => $model->id]),
                        ]) ?>
                    </td>
                    <td><?= $model->description ?></td>
                    <td><?= date('M-d-Y', strtotime($model->created)) ?></td>
                    <td><?= date('M-d-Y', strtotime($model->updated)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>