<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLine\PhoneLine */

$this->title = $model->line_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Lines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-line-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->line_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->line_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'line_id',
                'line_name',
                'line_project_id:projectName',
                'line_dep_id:departmentName',
                'line_language_id',
                'line_settings_json:dumpJson',
                'line_personal_user_id:username',
                'line_uvm_id',
                'line_allow_in:BooleanByLabel',
                'line_allow_out:BooleanByLabel',
                'line_enabled:BooleanByLabel',
                'line_created_user_id:username',
                'line_updated_user_id:username',
                'line_created_dt:byUserDateTime',
                'line_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
