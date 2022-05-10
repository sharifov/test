<?php
/** @var array $data
 * @var array $slice_data
 * @var string $separator
 * @var bool $is_slice
 *
 */
?>
<div class="js-wrap_show_more">
    <?= $separator . implode(' <br>' . $separator, $is_slice ? $slice_data : $data) ?>

    <?php if ($is_slice) : ?>
        <br><i class="fas fa-eye green js-show-more" style="cursor: pointer;"></i>
        <div style="display: none" class="js-data_show_more">
            <?= $separator . implode(' <br>' . $separator, $data) ?>
        </div>

        <?php
        $jsCode = <<<JS
         if(typeof jsShowMoreWidgetInit == "undefined"){
             $(document).on('click', '.js-show-more', function(){
                let element = $(this).parent().find('.js-data_show_more');
                element.css('display', 'block');
                $(this).remove();
             });
             var jsShowMoreWidgetInit = true;
         }
         JS;
        $this->registerJs($jsCode, \yii\web\View::POS_READY);
        ?>
    <?php endif; ?>
</div>

