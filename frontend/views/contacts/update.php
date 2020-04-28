<?php

use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var common\models\Client $model */

$this->title = 'Update Contact: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="contact-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">

        <div class="col-md-4">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>

        <div class="col-md-4">
            <?php echo $this->render('partial/_contact_manage_phone', [
                    'clientPhones' => $model->clientPhones,
                    'client' => $model,
                ]);
            ?>
        </div>
    </div>
</div>

<?php
$jsCode = <<<JS
    $(document).on('click', '.showModalButton', function() {
        let id = $(this).data('modal_id');
        let url = $(this).data('content-url');
        
        $('#modal-' + id + '-label').html($(this).attr('title'));
        $('#modal-' + id).modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

        $.post(url, function(data) {
            $('#modal-' + id).find('.modal-body').html(data);
        });
       return false;
    });    
JS;

$this->registerJs($jsCode);

