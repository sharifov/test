<?php
/* @var $this \yii\web\View */

use yii\bootstrap4\Modal;

?>

<?php echo Modal::widget([
    'id' => 'modal-lg',
    'title' => '',
    //'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => Modal::SIZE_LARGE
]); ?>

<?php echo Modal::widget([
    'id' => 'modal-sm',
    'title' => '',
    //'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => Modal::SIZE_SMALL
]); ?>

<?php echo Modal::widget([
    'id' => 'modal-df',
    'title' => '',
    //'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
    'size' => Modal::SIZE_DEFAULT
]); ?>

<div id="preloader" class="overlay d-none">
    <div class="preloader">
        <span class="fa fa-spinner fa-pulse fa-3x fa-fw"></span>
        <div class="preloader__text">Loading...</div>
    </div>
</div>