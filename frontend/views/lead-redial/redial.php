<?php

use common\models\Lead;
use frontend\widgets\LeadRedialWidget;

/** @var Lead $lead */

echo LeadRedialWidget::widget(['lead' => $lead]);
