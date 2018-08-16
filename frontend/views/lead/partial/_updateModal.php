<?php
/**
 * @var $this \yii\web\View
 */

use yii\bootstrap\Html;

$js = <<<JS
    $('.modal-btn-success').click(function (e) {
        e.preventDefault();
        window.location.reload();
    });
JS;

$this->registerJs($js);

?>

<div class="modal-close-sale__content">
    <div class="row">
        <div class="col-sm-12">
            Updated some leads info. Please reload page!
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 text-center">
            <div class="action-btns-wrapper">
                <?= Html::button('Cancel', [
                    'class' => 'btn btn-danger',
                    'data-dismiss' => 'modal'
                ]) ?>
                <?= Html::a('Ok', '#', [
                    'class' => 'btn btn-success modal-btn-success',
                ]) ?>
            </div>
        </div>
    </div>
</div>
