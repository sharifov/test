<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider ActiveDataProvider
 * @var $model Project
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use common\models\Project;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$template = <<<HTML
<div class="pagination-container row" style="margin-bottom: 10px;">
    <div class="col-sm-4" style="/*padding-top: 20px;*/">
        {summary}
    </div>
    <div class="col-sm-8" style="text-align: right;">
       {pager}
    </div>
</div>
<div class="table-responsive">
    {items}
</div>
HTML;

$js = <<<JS
$('.project-data-btn').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var editBlock = $('#project-data-modal');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            editBlock.modal({
              backdrop: 'static',
              show: true
            });
        });
    });

JS;
$this->registerJs($js);
?>

<div class="card card-default">
    <div class="card-header">Projects</div>
    <div class="card-body">
        <?php if (Yii::$app->user->identity->canRole('admin')) : ?>
            <div class="mb-20">

                <?= Html::a('Sync Project', '#', [
                    'class' => 'btn-success btn sync',
                    'data-url' => Url::to([
                        'settings/sync',
                        'type' => 'projects'
                    ])
                ]) ?>

                <?= Html::a('Synchronization Projects from BO', ['settings/synchronization'], ['class' => 'btn btn-warning', 'data' => [
                    'confirm' => 'Are you sure you want synchronization all projects from BackOffice Server?',
                    'method' => 'post',
                ],]) ?>

            </div>
        <?php endif; ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'layout' => $template,
            'columns' => [
                'id',
                'name',
                [
                    'attribute' => 'link',
                    'value' => function ($model) {
                        /**
                         * @var $model Project
                         */
                        return Html::a($model->link, $model->link, ['target' => '_blank']);
                    },
                    'format' => 'raw'
                ],

                [
                    'attribute' => 'last_update',
                    'value' => function (\common\models\Project $model) {
                        return $model->last_update ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->last_update)) : '-';
                    },
                    'format' => 'raw'
                ],
                //'api_key',
                'closed:boolean',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {list} {emails}',
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            /**
                             * @var $model Project
                             */
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', [
                                    'settings/projects',
                                    'id' => $model->id
                                ], [
                                'title' => 'Edit'
                            ]).'&nbsp;'.
                            Html::a('<span class="glyphicon glyphicon-cog"></span>',
                                ['settings/project-data', 'id' => $model->id], [
                                'title' => 'Custom params',
                                'class' => 'project-data-btn',
                            ]);
                        }
                    ]
                ],
            ]
        ])
        ?>
    </div>
</div>
<div class="modal fade" id="modal-email-templates" style="display: none;">
    <div class="modal-dialog" role="document" style="width: 1024px;">
        <div class="modal-content">
            <div class="modal-header">
                <?= Html::button('<span>×</span>', [
                    'class' => 'close',
                    'data-dismiss' => 'modal'
                ]) ?>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<?php Modal::begin(['id' => 'project-data-modal',
    'header' => '<h2>Project Custom Params</h2>',
    'size' => Modal::SIZE_LARGE,
])?>
<?php Modal::end()?>