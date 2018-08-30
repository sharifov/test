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

?>

<div class="panel panel-default">
    <div class="panel-heading">Projects</div>
    <div class="panel-body">
        <?php if (\webvimark\modules\UserManagement\models\User::hasRole('admin')) : ?>
            <div class="mb-20">
                <?= Html::a('Sync Project', '#', [
                    'class' => 'btn-success btn sync',
                    'data-url' => Url::to([
                        'settings/sync',
                        'type' => 'projects'
                    ])
                ]) ?>
            </div>
        <?php endif; ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => $template,
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
                'api_key',
                'closed:boolean',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {list} {emails}',
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            /**
                             * @var $model Project
                             */
                            $url = \yii\helpers\Url::to([
                                'settings/projects',
                                'id' => $model->id
                            ]);
                            return Html::a('<span class="glyphicon glyphicon-edit"></span>', $url, [
                                'title' => 'Edit'
                            ]);
                        }
                    ]
                ],
            ]
        ])
        ?>
    </div>
</div>