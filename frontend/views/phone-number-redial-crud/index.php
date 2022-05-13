<?php

use common\components\grid\DateTimeColumn;
use src\model\phoneNumberRedial\abac\PhoneNumberRedialAbacObject;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\phoneNumberRedial\entity\PhoneNumberRedialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$abac = Yii::$app->abac;
$this->title = 'Phone Number Redials';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-number-redial-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Number Redial', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Create Multiple Phone Number Redial', ['create-multiple'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-PhoneNumberRedial22']); ?>

    <?= $this->render('_search', ['model' => $searchModel]); ?>
        <div class="btn-group">
            <?php echo Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-default', 'id' => 'btn-check-all']); ?>

            <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
                <?php if ($abac->can(null, PhoneNumberRedialAbacObject::OBJ_PHONE_NUMBER_REDIAL, PhoneNumberRedialAbacObject::ACTION_MULTIPLE_DELETE)) : ?>
                    <?= \yii\helpers\Html::a('<i class="fa fa-remove text-danger"></i>  Delete Selected', null, ['id' => 'btn-act-delete-selected', 'class' => 'dropdown-item btn-multiple-update' ])?>
                <?php endif ?>
                <div class="dropdown-divider"></div>
                <?= \yii\helpers\Html::a('<i class="fa fa-info text-info"></i> Show Checked IDs', null, ['class' => 'dropdown-item btn-show-checked-ids'])?>
            </div>
        </div>
    <?= GridView::widget([
        'id' => 'pnr-list-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'cssClass' => 'multiple-checkbox'
            ],
            'pnr_id',
            'pnr_project_id:projectName',
            'pnr_phone_pattern',
            [
                'attribute' => 'pnr_pl_id',
                'value' => static function (\src\model\phoneNumberRedial\entity\PhoneNumberRedial $model): string {
                    return Html::encode($model->phoneList->pl_phone_number);
                }
            ],
            'pnr_name',
            'pnr_enabled:booleanByLabel',
            'pnr_priority',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pnr_created_dt'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pnr_updated_dt'
            ],
            [
                'attribute' => 'pnr_updated_user_id',
                'filter' => \src\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'pnr_updated_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '150px'
                ]
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, \src\model\phoneNumberRedial\entity\PhoneNumberRedial $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'pnr_id' => $model->pnr_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?php
$storageName = Inflector::variablize($this->title);
$selectAllUrl = Url::to(['/phone-number-redial-crud/select-all']);
$deleteSelectedUrl = Url::to(array_merge(['/phone-number-redial-crud/delete-selected'], Yii::$app->getRequest()->getQueryParams()));
$pjaxContainer = '#pjax-PhoneNumberRedial22' ;

$script = <<< JS

    let selectAllUrl = '$selectAllUrl';
    let deleteSelectedUrl = '$deleteSelectedUrl';
    let storageName = '$storageName';
    let pjaxContainer = '$pjaxContainer';
    
    let btn = $('#btn-check-all');      
    let loadingInnerText = '<span class="fa fa-spinner fa-spin"></span> Loading ...';    
    let checkAllInnerText = btn.html();
        
    function refreshSelectedState() {
        if (sessionStorage.getItem(storageName)) {
            let data = jQuery.parseJSON(sessionStorage.getItem(storageName));
            let cnt = Object.keys(data).length;
            
            if (cnt > 0) {
                $.each(data, function(key, value) {
                    $("input[name='selection[]'][value='" + value + "']").prop('checked', true);
                });
                btnUncheckAll(btn, cnt);                
            } else {
                btnCheckAll(btn);
                $('.select-on-check-all').prop('checked', false);
            }
        } else {
            btnCheckAll(btn);
            $('.select-on-check-all').prop('checked', false);
        }
    }
    
    function btnUncheckAll(btn, cnt) {
        btn.removeClass('btn-default').
            addClass(['btn-warning', 'checked']).
            html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')'); 
    }
    
    function btnCheckAll(btn) {
        btn.removeClass(['btn-warning', 'checked']).
            addClass('btn-default').
            html(checkAllInnerText);
    }
    
    function notifyAlert(text, type = 'success') {
        createNotifyByObject({
            title: type,
            type: type,
            text: text,
            hide: true
        });  
    }
    
    $(document).on('click', '#btn-check-all',  function (e) {
        let btn = $(this);
        
        if (btn.hasClass('checked')) {
            btnCheckAll(btn);
            $('.select-on-check-all').prop('checked', false);
            $("input[name='selection[]']:checked").prop('checked', false);
            sessionStorage.removeItem(storageName);
            
        } else {    
            btn.html(loadingInnerText).prop('disabled', true);
                let queryParams = ''
                if (window.location.href.indexOf('?') > 0) {
                    queryParams = window.location.href.slice(window.location.href.indexOf('?'))
                }                
            $.ajax({
                url: selectAllUrl + queryParams,
                type: 'POST',
                dataType: 'json'    
            })
            .done(function(dataResponse) {
                
                let cnt = Object.keys(dataResponse).length;
                if (dataResponse) {
                    sessionStorage.setItem(storageName, JSON.stringify(dataResponse));
                    btnUncheckAll(btn, cnt);                        
                    $('.select-on-check-all').prop('checked', true); 
                    $("input[name='selection[]']").prop('checked', true);
                } else {
                    btn.html(checkAllInnerText);
                }
            })
            .fail(function(error) {
                console.error(error);
                alert('Request Error');
                btn.html('<span class="fa fa-error text-danger"></span> Error ...');                
            })
            .always(function() {
                btn.prop('disabled', false);
            }); 
        }
    });
    
    $(document).on('click', '#btn-act-delete-selected', function() {
        
        if (!sessionStorage.getItem(storageName)) {
            notifyAlert('Please select items', 'error');
            return false; 
        }          
        
        let data = jQuery.parseJSON(sessionStorage.getItem(storageName));
        let cnt = Object.keys(data).length;
                              
        if(!confirm('Are you sure you want to delete (' + cnt + ') ?')) {
            return false;
        }
                    
        $.ajax({
            url: deleteSelectedUrl,
            type: 'POST',
            dataType: 'json',
            data: {selection : data}
        })
        .done(function(dataResponse) {
            if (dataResponse) {
                sessionStorage.removeItem(storageName);
                $.pjax.reload({container: pjaxContainer});
                notifyAlert('Items (' + cnt + ') deleted successfully');
            }                
        })
        .fail(function(error) {
            console.error(error);
            alert('Request Error');
        })
        .always(function() {});
        
    });
    
    $(document).on('change', '.select-on-check-all', function(e) {
        let checked = $('#pnr-list-grid').yiiGridView('getSelectedRows');
        let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
        let data = [];        
        if (sessionStorage.getItem(storageName)) {
            data = JSON.parse(sessionStorage.getItem(storageName));
        }
                        
        if (checked) {
            $.each(checked, function(key, value) {
            
                let searchValue = parseInt(value, 10);
                if (isNaN(parseInt(value, 10))) {                    
                    if (typeof value === 'string' || value instanceof String) {
                        searchValue = value; 
                    } else {
                        searchValue = JSON.stringify(value);
                    }
                }
                
                let keyForAdd = data.indexOf(searchValue);                
                if (keyForAdd === -1) {
                    data.push(searchValue);
                }
            });
        }         
        if (unchecked) {
            $.each(unchecked, function(key, value) { 
                
                let searchValue = parseInt(value, 10);
                if (isNaN(parseInt(value, 10))) {                    
                    if (typeof value === 'string' || value instanceof String) {
                        searchValue = value; 
                    } else {
                        searchValue = JSON.stringify(value);
                    }
                }
                           
                let keyForDelete = data.indexOf(searchValue);                
                if (keyForDelete !== -1) {
                    data.splice(keyForDelete, 1);
                }                
            });
        }
        
        if (data.length) {
            sessionStorage.setItem(storageName, JSON.stringify(data));
        } else {
            sessionStorage.removeItem(storageName);
        }
        refreshSelectedState();
    });        
    
    $(document).ready(function() {        
        refreshSelectedState();
    });
        
    $(pjaxContainer).on('pjax:end', function() { 
       refreshSelectedState();
    });
    
    $('body').on('click', '.btn-show-checked-ids', function(e) {
       let data = [];
       if (sessionStorage.getItem(storageName)) {
            data = jQuery.parseJSON( sessionStorage.getItem(storageName));
            let arrIds = [];
            if (data) {
                arrIds = Object.values(data);                 
            }
            alert('Phone Number Redials IDs (' + arrIds.length + ' items): ' + arrIds.join(', '));
       } else {
           alert('No Any records Selected');
       }
    });
JS;

$this->registerJs($script);

?>