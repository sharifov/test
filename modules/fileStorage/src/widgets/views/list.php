<?php

use modules\fileStorage\src\services\url\UrlGenerator;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var array $files */
/** @var string $uploadWidget */
/** @var UrlGenerator $urlGenerator */

$countFiles = count($files);
?>

<div class="x_panel">
    <div class="x_title">
        <h2 class="file-storage-list-counter" data-count="<?= $countFiles ?>">Files (<?= $countFiles ?>)</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: none;">
        <table class="table table-bordered file-storage-list">
            <tr>
                <td style="width: 40px"></td>
                <td>Filename</td>
                <td>Title</td>
            </tr>
            <?php foreach ($files as $file) : ?>
                <tr>
                    <td><?= Html::a('<i class="fa fa-download"> </i>', $urlGenerator->generate($file['path'])) ?></td>
                    <td><?= Html::encode($file['name']) ?></td>
                    <td><?= Html::encode($file['title']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?= $uploadWidget ?>
    </div>
</div>

<?php

$js = <<<JS
function addFileToFileStorageList(data) {
    $('.file-storage-list tr:first').after('<tr><td><a href="' + data.url + '"><i class="fa fa-download"> </i></a></td><td>' + data.name + '</td><td>' + data.title + '</td></tr>');
    let counter = $('.file-storage-list-counter');
    let count = parseInt(counter.attr('data-count'));
    count++;
    counter.attr('data-count', count).html('Files (' +  count + ')');
}
JS;

$this->registerJs($js, View::POS_END);
