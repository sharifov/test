<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\MaskedInput;
use common\models\GlobalAcl;

/**
 * @var $this yii\web\View
 * @var $searchModel frontend\models\search\GlobalAclForm
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model GlobalAcl
 */

$idForm = sprintf('%s-ID', $model->formName());

$this->title = 'Global ACL';
$template = <<<HTML
<div class="pagination-container row" style="margin-bottom: 10px;">
    <div class="col-sm-3" style="padding-top: 20px;">
        {summary}
    </div>
    <div class="col-sm-9" style="text-align: right;">
       {pager}
    </div>
</div>
<div class="table-responsive">
    {items}
</div>
HTML;

$js = <<<JS
    $('#acl-rule-id').click(function() {
        $(this).addClass('hidden');
        $('#$idForm').parent().removeClass('hidden');
    });

    $('#close-btn').click(function() {
        $('#acl-rule-id').removeClass('hidden');
        $('#$idForm').parent().addClass('hidden');
    });
    
    $('#submit-btn').click(function() {
        $('#$idForm').submit();
    });
    
    $('.change-acl-rule-status').change(function() {
        var r = confirm('Are you sure you want to change rule status?');
        if (r == true) {
            var value = ($(this).is(':checked')) ? 1 : 0;
            $.post($(this).data('url'), {'GlobalAcl[active]': value});
        } else {
            $(this).prop('checked', !$(this).is(':checked'));
        }
    });
JS;

$this->registerJs($js);
?>

<div class="panel panel-default">
    <div class="panel-heading">Global ACL Rules</div>
    <div class="panel-body">
        <?= Html::a('Add Rule', null, [
            'class' => 'btn btn-success',
            'id' => 'acl-rule-id',
        ]) ?>
        <div class="well hidden">
            <?= $this->render('item/acl', [
                'model' => $model
            ]) ?>
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => $template,
            'columns' => [
                'id',
                [
                    'header' => 'IP',
                    'value' => 'mask',
                    'filter' => MaskedInput::widget([
                        'name' => Html::getInputName($searchModel, 'mask'),
                        'value' => $searchModel->mask,
                        'clientOptions' => [
                            'alias' => 'ip'
                        ],
                    ]),
                ],
                [
                    'header' => 'Description',
                    'value' => 'description',
                ],
                [
                    'header' => 'Active',
                    'format' => 'raw',
                    'value' => function ($model) {
                        /**
                         * @var $model GlobalAcl
                         */
                        return Html::activeCheckbox($model, 'active', [
                            'class' => 'change-acl-rule-status',
                            'label' => false,
                            'data-url' => Yii::$app->urlManager->createUrl(['settings/acl-rule', 'id' => $model->id]),
                        ]);
                    }
                ],
                'created:datetime',
                'updated:datetime',
            ],
        ]); ?>
    </div>
</div>
