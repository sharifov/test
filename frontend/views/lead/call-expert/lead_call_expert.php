<?php

/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $modelLeadCallExpert LeadCallExpert
 * @var $user \common\models\Employee
 */

use common\models\LeadCallExpert;
use modules\lead\src\abac\LeadExpertCallObject;
use modules\lead\src\abac\services\AbacLeadExpertCallService;
use modules\product\src\entities\product\Product;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$user = Yii::$app->user->identity;
$expertCallAbacDto = (new AbacLeadExpertCallService($lead, $user))->getLeadExpertCallDto();
/** @abac $expertCallAbacDto, LeadExpertCallObject::ACT_CALL, LeadExpertCallObject::ACTION_ACCESS, access new expert call */
$abacActNewExpertCall = \Yii::$app->abac->can($expertCallAbacDto, LeadExpertCallObject::ACT_CALL, LeadExpertCallObject::ACTION_ACCESS);
?>
<?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-call-expert', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="x_panel">
    <div class="x_title">

        <?php
        /** @var LeadCallExpert $lastModel */
        $lastModel = null;
        $label = '';
        if ($dataProvider->count > 0) {
            //$lastKey = array_key_last($dataProvider->models); php 7.3
            $lastKey = array_keys($dataProvider->models)[count($dataProvider->models) - 1];
            if (isset($dataProvider->models[$lastKey])) {
                $lastModel = $dataProvider->models[$lastKey];
            }

            if ($lastModel) {
                if ($lastModel->lce_status_id === LeadCallExpert::STATUS_PENDING) {
                    $label = 'warning';
                } else if ($lastModel->lce_status_id === LeadCallExpert::STATUS_DONE) {
                    $label = 'success';
                } else if ($lastModel->lce_status_id === LeadCallExpert::STATUS_PROCESSING) {
                    $label = 'info';
                }
            }
        }
        ?>&nbsp;

        <h2><i class="fa fa-bell-o <?=$label?>"></i> BO Expert (<?=$dataProvider->count?>)

            <?php
            if ($lastModel) {
                echo ' : ' . $lastModel->getStatusLabel() . '';
            }
            ?>

            <?php if ($user->userParams && $user->userParams->up_call_expert_limit > 0) :?>
                [limit: <?=$user->callExpertCount?> /  <?= $user->userParams->up_call_expert_limit?>]
            <?php endif;?>

        </h2>

        <ul class="nav navbar-right panel_toolbox">
            <li>

            </li>
            <li>
                    <?php if (!$lastModel || $lastModel->lce_status_id === LeadCallExpert::STATUS_DONE) :?>
                        <?php if ($user->isEnableCallExpert() && $abacActNewExpertCall) : ?>
                            <?=Html::a('<i class="fa fa-plus-circle success"></i> New Call', null, ['id' => 'btn-call-expert-form'])?>
                        <?php endif; ?>
                    <?php endif; ?>
            </li>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
            </li>

            <?php /*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-comment"></i></a>


                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>

    <div class="x_content" style="display: <?=Yii::$app->request->isPjax ? 'block' : 'none';?>">

                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,

                    'options' => [
                        'tag' => 'div',
                        'class' => 'list-wrapper',
                        'id' => 'list-wrapper',
                    ],
                    'emptyText' => '<div class="text-center">Not found expert messages</div><br>',
                    'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render('_list_item', ['model' => $model]);
                    },

                    'itemOptions' => [
                        //'class' => 'item',
                        'tag' => false,
                    ],

                    /*'pager' => [
                        'firstPageLabel' => 'first',
                        'lastPageLabel' => 'last',
                        'nextPageLabel' => 'next',
                        'prevPageLabel' => 'previous',
                        'maxButtonCount' => 3,
                    ],*/

                ]) ?>


        <?php $form = ActiveForm::begin([
            //'action' => ['index'],
            'id' => 'call-expert-form',
            'method' => 'post',
            'options' => [
                'data-pjax' => 1,
            ],
        ]);

        echo $form->errorSummary($modelLeadCallExpert);
?>

        <div class="row" style="display: <?=$modelLeadCallExpert->hasErrors() ? 'block' : 'none'?>" id="div-call-expert-form">

            <?php $products = (new Product())->getByLeadAndType($lead->id) ?>
            <?php if ($products) :?>
                <div class="col-sm-3">
                    <?= $form->field($modelLeadCallExpert, 'lce_product_id')->dropDownList(
                        ArrayHelper::map($products, 'pr_id', 'pr_name'),
                        ['prompt' => 'Select product', 'id' => 'lce_product_id']
                    ) ?>
                </div>
            <?php endif ?>

            <div class="col-md-12">
                <?= $form->field($modelLeadCallExpert, 'lce_request_text')->textarea(['rows' => 8, 'id' => 'lce_request_text'])->label('Request Message') ?>
            </div>

            <div class="col-md-12">
                <div class="form-group text-center">
                    <?= Html::submitButton('<i class="fa fa-plus"></i> Create call Expert', ['class' => 'btn btn-success', 'id' => 'btn-submit-call-expert']) ?>
                    <?= Html::button(
                        '<i class="fa fa-copy"></i>',
                        [
                            'title' => 'Past from Lead Notes',
                            'class' => 'btn-note-from-client btn btn-primary',
                        ]
                    ) ?>
                    <?= Html::button(
                        '<i class="fas fa-copy"></i>',
                        [
                            'title' => 'Past from product description',
                            'class' => 'btn-product-description btn btn-primary d-none',
                        ]
                    ) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<?php yii\widgets\Pjax::end() ?>


<?php
$js = <<<JS
    $(document).on('change', '#lce_product_id', function() {
        let productId = $(this).val();
        
        if (isNaN(parseInt(productId, 10))) {
            $('.btn-product-description').addClass('d-none');
        } else {
            $('.btn-product-description').removeClass('d-none'); 
        }       
    }); 
    
    $(document).on('click', '.btn-product-description', function() {
        let productId = $('#lce_product_id').val();
        let productDescriptionElement = $('#product_description_' + productId);
        
        if (productDescriptionElement.length == 0) {
            createNotifyByObject({title: 'Error', text: 'Description is empty.', type: 'error'});
            return false;
        }
        
        let requestText = $('#lce_request_text').val();
        let description = productDescriptionElement.data('content');
        let textNew = description;
        
        if (requestText.length > 0) {
            textNew = requestText + '\\n' +  description;
        } 
        
        $('#lce_request_text').val(textNew);
    });   
    
    $(document).on('click', '.btn-note-from-client', function() {
        let noteFromClient = $('#lead-notes_for_experts').text();
        let requestText = $('#lce_request_text').val();
        let textNew = noteFromClient;
        
        if (requestText.length > 0) {
            textNew = requestText + '\\n' +  noteFromClient;
        } 
        
        $('#lce_request_text').val(textNew);
    });    
JS;

$this->registerJs($js);
?>

<?php
$this->registerJs(
    '
        $(document).on("change",".ch_task", function() {
            $.pjax.reload({container: containerId, push: false, replace: false, timeout: 5000, data: {date: taskDate, task_id: taskId, lead_id: taskLeadId, user_id: taskUserId}});
        });


        $(document).on("click",".link2quote", function() {
            var uid = $(this).data("uid");

            $([document.documentElement, document.body]).animate({
                scrollTop: $("#quote-" + uid).offset().top
            }, 500);

            for(i = 0; i < 4; i ++) {
                $("#quote-" + uid).fadeTo(300, 0.2).fadeTo(300, 1.0);
            }

        });


        $(document).on("click","#btn-call-expert-form", function() {
            $("#div-call-expert-form").show();
            $("#pjax-lead-call-expert .x_content").show();

            $([document.documentElement, document.body]).animate({
                scrollTop: $("#call-expert-form").offset().top
            }, 1000);

            return false;
        });


        $("#pjax-lead-call-expert").on("pjax:start", function () {
            //$("#pjax-container").fadeOut("fast");
            $("#btn-submit-call-expert").attr("disabled", true).prop("disabled", true).addClass("disabled");
            $("#btn-submit-call-expert i").attr("class", "fa fa-spinner fa-pulse fa-fw")

        });

        $("#pjax-lead-call-expert").on("pjax:end", function () {
            //$("#pjax-container").fadeIn("fast");
            //alert("end");

            $("#btn-submit-call-expert").attr("disabled", false).prop("disabled", false).removeClass("disabled");
            $("#btn-submit-call-expert i").attr("class", "fa fa-plus");

        });
    '
);
?>