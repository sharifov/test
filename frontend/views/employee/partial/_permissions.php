<?php

/**
 * @var $this \yii\web\View
 * @var $model Employee
 * @var $isProfile boolean
 */

use yii\bootstrap\Html;
use common\models\Project;
use common\models\Employee;
use yii\helpers\ArrayHelper;

/** @var Employee $user */
$user = Yii::$app->user->identity;

$disabled = '';
if ($isProfile || $model->id == $user->id) {
    $disabled = 'disabled';
}
?>

<?php if ($model->isAgent() && $model->id == $user->id) : ?>
<?php else : ?>
    <div class="card card-default">
        <div class="panel-heading collapsing-heading">
            <?= Html::a('Access to projects <i class="collapsing-heading__arrow"></i>', '#permissions-info', [
                'data-toggle' => 'collapse',
                'class' => 'collapsing-heading__collapse-link'
            ]) ?>
        </div>
        <div class="panel-body panel-collapse collapse in" id="permissions-info">
            <?php
            /**
             * @var $projects Project[]
             */
            $employeeAccess = ArrayHelper::map($model->projectEmployeeAccesses, 'project_id', 'project_id');
            $availableProjects = $employeeAccess;
            if ($user->isAdmin()) {
                $projects = Project::find()->all();
            } else {
                if ($user->isSupervision()) {
                    $availableProjects = ArrayHelper::map($user->projectEmployeeAccesses, 'project_id', 'project_id');
                }
                $projects = Project::find()
                    ->where(['id' => $availableProjects])
                    ->all();
            }
            $items = ArrayHelper::map($projects, 'id', 'name');
            ?>
            <?= Html::activeCheckboxList($model, 'employeeAccess', $items, [
                'item' => function ($index, $label, $name, $checked, $value) use ($disabled) {
                    $isChecked = ($checked) ? 'checked' : '';
                    $return = '<div class="checkbox"><label>';
                    $return .= '<input type="checkbox" name="' . $name . '" value="' . $value . '" ' . $isChecked . ' ' . $disabled . '>';
                    $return .= $label . '</label></div>';
                    return $return;
                },
            ]) ?>
            <?= Html::activeHiddenInput($model, 'viewItemsEmployeeAccess', [
                'value' => json_encode($items)
            ]) ?>
        </div>
    </div>
<?php endif; ?>