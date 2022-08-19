<?php

/* @var $fareRules array */
?>

<?php if (count($fareRules) == 0) : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                Fare rules not identified, please check manually.
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-md-12 x_panel">
            <?php if (count($fareRules)) : ?>
                <?php foreach ($fareRules as $rule) : ?>
                    <?php
                    $categoryTitle = $rule['categoryTitle'] ?? '';
                    $fareBasisCode = $rule['fareBasisCode'] ?? '';
                    $fullText = $rule['fullText'] ?? '';
                    $ruleDetails = $rule['details'] ?? [];
                    ?>
                    <div class="category" style="border: 1px solid;padding: 5px;margin-bottom: 10px;">
                        <div class="x_title">
                            <h2><b><?= $categoryTitle . ' for Fare Basis: ' . $fareBasisCode ?></b></h2>
                            <a class="collapse-link panel_toolbox"><i class="fa fa-chevron-down"></i></a>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" style="float: none;display: none">
                            <?php if (isset($ruleDetails)) : ?>
                                <div class="row">
                                    <?php foreach ($ruleDetails as $penalty => $details) : ?>
                                        <div class="col-md-6">
                                            <h6><b><?= $penalty ?></b></h6>
                                            <?php foreach ($details as $detail) : ?>
                                                <div>
                                                    <b><?= isset($detail['for']) ? 'For: ' . $detail['for'] : 'For: ' ?></b>
                                                </div>
                                                <div>
                                                    <b><?= isset($detail['for']) ? 'Title: ' . $detail['title'] : 'Title: ' ?></b>
                                                </div>
                                                <div>
                                                    <b><?= isset($detail['for']) ? 'Value: ' . $detail['value'] : 'Value: ' ?></b>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="clearfix"></div>
                            <pre><?= $fullText ?></pre>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="alert alert-warning" role="alert">
                    Fare rules not identified, please check manually.
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>



