<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\project\ProjectColumn;
use common\components\grid\UserColumn;
use yii\bootstrap4\Modal;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\project\entity\projectRelation\search\ProjectRelationSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Project Relations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-relation-index">

    <h1><?= Html::encode($this->title) ?>
        <sup>
            <?php echo
            Html::a(
                Html::tag('i', '', ['class' => 'fa fa-info-circle', 'style' => 'color: #53a265;']),
                null,
                ['id' => 'js-info_project-relation']
            ) ?>
        </sup>
    </h1>

    <p>
        <?= Html::a('Create Project Relation', ['create'], ['class' => 'btn btn-success']) ?>

    </p>

    <?php Pjax::begin(['id' => 'pjax-project-relation', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            [
                'class' => ProjectColumn::class,
                'attribute' => 'prl_project_id',
                'relation' => 'prlProject',
            ],
            [
                'class' => ProjectColumn::class,
                'attribute' => 'prl_related_project_id',
                'relation' => 'prlRelatedProject',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'prl_created_user_id',
                'relation' => 'prlCreatedUser',
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'prl_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

<?php
Modal::begin([
    'title' => '<i class="fa fa-info-circle"></i> Info block for project relation',
    'id' => 'project_relation_popup',
    'size' => Modal::SIZE_DEFAULT
]);
Modal::end();
?>
    <div id="info_data_project_relation" style="display: none;">
        <p>Projects can be related.</p>
        <p>There is a main project (Project) and related projects (Related Project).</p>
        <p>Access to the quota via the API (quote/get-info) will be provided to ApiUser not only to the quotas associated with the project, but also to the quotas of the main project.</p>
        <p>Relate via lead.project_id or via quotes.provider_project_id.</p>
    </div>
</div>

<?php
$js = <<<JS

$(document).on('click', '#js-info_project-relation', function (e) { 
        e.preventDefault();
    
        let infoData = $('#info_data_project_relation').html();
        
        $('#project_relation_popup .modal-body').html(infoData);
        $('#project_relation_popup').modal('show');     
    });
JS;
$this->registerJs($js);

