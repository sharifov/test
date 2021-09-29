<?php

use common\components\grid\DateTimeColumn;
use sales\helpers\phone\MaskPhoneHelper;
use sales\model\call\abac\CallAbacObject;
use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use sales\model\contactPhoneData\service\ContactPhoneDataHelper;
use sales\model\contactPhoneList\entity\ContactPhoneList;
use sales\model\contactPhoneList\service\ContactPhoneListService;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\contactPhoneList\entity\ContactPhoneListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contact Phone Lists';
$this->params['breadcrumbs'][] = $this->title;
$view = $this;
?>
<div class="contact-phone-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php echo $this->render('_search', ['model' => $searchModel]);?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-contact-phone-list', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            [
                'class' => ActionColumn::class,
                'template' => '{view} {key-data-dropdown}' ,
                'visibleButtons' => [
                    'key-data-dropdown' => ContactPhoneDataHelper::accessAbacToKeyDataDropdown(),
                ],
                'buttons' => [
                    'key-data-dropdown' => static function ($url, ContactPhoneList $model) use ($view) {
                        return $view->render('partial/_dropdown', ['model' => $model]);
                    },
                ],
            ],
            'cpl_id',
            [
                'attribute' => 'cpl_phone_number',
                'value' => static function (ContactPhoneList $model) {
                    return MaskPhoneHelper::masking($model->cpl_phone_number);
                }
            ],
            [
                'label' => 'Phone Data',
                'value' => static function (ContactPhoneList $model) {
                    $result = '';
                    if ($model->contactPhoneData) {
                        foreach ($model->contactPhoneData as $contactPhoneData) {
                            $result .= '<p style="margin-bottom: 6px;">' .
                                ContactPhoneDataHelper::getLabel($contactPhoneData->cpd_key) . ' : ' .
                                ContactPhoneDataHelper::getLabelValue($contactPhoneData->cpd_key, $contactPhoneData->cpd_value) . '</p>';
                        }
                    }
                    return $result;
                },
                'format' => 'raw',
            ],
            'cpl_uid',
            'cpl_title',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cpl_created_dt',
                'format' => 'byUserDateTime'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$urlToggleData = Url::to(['/contact-phone-list/toggle-data']);

$js = <<<JS
    $(document).on('click', '.js-toggle-data', function (e) { 
        e.preventDefault();
        
        let modelId = $(this).data('model-id');
        let btn = $(this);
        let bxBtn = $('#dropdownMenuButton_' + modelId);
        let icoBtn = $('#dropdownIco_' + modelId);
        
        icoBtn.removeClass('fa-check-square').addClass('fa-cog').addClass('fa-spin');
        btn.prop('disabled', true);
        bxBtn.prop('disabled', true);

        $.ajax({
            url: '{$urlToggleData}',
            type: 'POST',
            data: {modelId: $(this).data('model-id'), key: $(this).data('key')},
            dataType: 'json'
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {
                pjaxReload({container: '#pjax-contact-phone-list'});
                createNotify('Success', dataResponse.message, 'success');
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
            btn.prop('disabled', false);
            bxBtn.prop('disabled', false);
            icoBtn.removeClass('fa-cog').addClass('fa-spin').addClass('fa-check-square');
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log({
                jqXHR : jqXHR,
                textStatus : textStatus,
                errorThrown : errorThrown
            });
        })
        .always(function(jqXHR, textStatus, errorThrown) {
            setTimeout(function () {
                btn.prop('disabled', false);
                bxBtn.prop('disabled', false);
                icoBtn.removeClass('fa-cog').addClass('fa-spin').addClass('fa-check-square');
            }, 3000);
        });
    });
JS;

$this->registerJs($js, $this::POS_END);
