<?php

use src\helpers\nestedSets\NestedSetsHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use src\entities\cases\CaseCategory;

/* @var $this yii\web\View */
/* @var $model src\entities\cases\CaseCategory */
/* @var $parentCategoryName string */
/* @var $parentCategoryId string */
/* @var $parentsCategoriesHierarchy string */


$this->title = $model->cc_name;
$this->params['breadcrumbs'][] = ['label' => 'Case Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="case-category-view">

    <div class="col-sm-4">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cc_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cc_id], [
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
                'cc_id',
                'cc_key',
                'cc_name',
                [
                    'attribute' => 'cc_dep_id',
                    'format' => 'raw',
                    'value' => static function (CaseCategory $model) {
                        return $model->dep ? $model->dep->dep_name : '';
                    },
                ],
                'cc_system:boolean',
                'cc_enabled:boolean',
                'cc_allow_to_select:boolean',
                'cc_lft',
                'cc_rgt',
                'cc_depth',
                'cc_tree',
                [
                    'attribute' => 'parentCategoryName',
                    'value' => $parentCategoryName,
                ],
                [
                    'attribute' => 'parentCategoryId',
                    'value' => $parentCategoryId,
                ],
                [
                    'attribute' => 'parentsCategoriesHierarchy',
                    'value' => $parentsCategoriesHierarchy,
                ],
                'cc_created_dt:byUserDateTime',
                'cc_updated_dt:byUserDateTime',
                'cc_updated_user_id:username',
            ],
        ]) ?>
    </div>

</div>
