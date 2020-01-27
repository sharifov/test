<?php

namespace sales\yii\i18n;

use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferStatus;
use modules\offer\src\entities\offer\OfferStatusAction;
use modules\offer\src\helpers\formatters\OfferFormatter;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productOption\ProductOptionPriceType;
use modules\product\src\entities\productQuote\ProductQuote;
use common\models\Project;
use common\models\Quote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatusAction;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use modules\product\src\helpers\formatters\ProductFormatter;
use modules\product\src\helpers\formatters\ProductQuoteFormatter;
use yii\bootstrap4\Html;

class Formatter extends \yii\i18n\Formatter
{
    public function asProductQuoteStatusAction($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteStatusAction::asFormat($value);
    }

    public function asOfferStatusAction($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return OfferStatusAction::asFormat($value);
    }

    public function asOffer(?Offer $offer): string
    {
        if ($offer === null) {
            return $this->nullDisplay;
        }

        return OfferFormatter::asOffer($offer);
    }

    public function asOfferStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return OfferStatus::asFormat($value);
    }

    public function asProductQuoteOptionStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteOptionStatus::asFormat($value);
    }

    public function asProductOptionPriceType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductOptionPriceType::asFormat($value);
    }

    public function asProductQuoteStatus($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteStatus::asFormat($value);
    }

    public function asProductQuote(?ProductQuote $productQuote): string
    {
        if ($productQuote === null) {
            return $this->nullDisplay;
        }

        return ProductQuoteFormatter::asProductQuote($productQuote);
    }

    public function asProduct(?Product $product): string
    {
        if ($product === null) {
            return $this->nullDisplay;
        }

        return ProductFormatter::asProduct($product);
    }

    public function asLead(?Lead $lead): string
    {
        if ($lead === null) {
            return $this->nullDisplay;
        }

        return \modules\lead\src\helpers\formatters\lead\Formatter::asLead($lead);
    }

    public function asProductType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return \modules\product\src\helpers\formatters\ProductTypeFormatter::asProductType($value);
    }

    public function asQuoteType($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        switch ($value) {
            case Quote::TYPE_BASE:
                $class = 'label label-info';
                break;
            case Quote::TYPE_ORIGINAL:
                $class = 'label label-success';
                break;
            case Quote::TYPE_ALTERNATIVE:
                $class = 'label label-warning';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', Quote::getTypeName($value), ['class' => $class]);
    }

    /**
     * @param $dateTime
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function asByUserDateTime($dateTime): string
    {
        if (!$dateTime) {
            return $this->nullDisplay;
        }
        return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' . $this->asDatetime(strtotime($dateTime), 'php:d-M-Y [H:i]');
    }

    /**
     * @param Employee|int|string|null $value
     * @return string
     */
    public function asUserName($value): string
    {
        if (!$value) {
            return $this->nullDisplay;
        }

        if (is_string($value)) {
            $name = $value;
        } elseif ($value instanceof Employee) {
            $name = $value->username;
        } elseif (is_int($value)) {
            if ($entity = Employee::findOne($value)) {
                $name = $entity->username;
            } else {
                return 'not found';
            }
        } else {
            throw new \InvalidArgumentException('user must be Employee|int|string|null');
        }

        return Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($name);
    }

    /**
     * @param Department|int|string|null $value
     * @return string
     */
    public function asDepartmentName($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        if (is_string($value)) {
            $name = $value;
        } elseif ($value instanceof Department) {
            $name = $value->dep_name;
        } elseif (is_int($value)) {
            if ($entity = Department::findOne($value)) {
                $name = $entity->dep_name;
            } else {
                return 'not found';
            }
        } else {
            throw new \InvalidArgumentException('value must be Department|int|string|null');
        }

        return Html::encode($name);
    }

    /**
     * @param Project|int|string|null $value
     * @return string
     */
    public function asProjectName($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        if (is_string($value)) {
            $name = $value;
        } elseif ($value instanceof Project) {
            $name = $value->name;
        } elseif (is_int($value)) {
            if ($entity = Project::findOne($value)) {
                $name = $entity->name;
            } else {
                return 'not found';
            }
        } else {
            throw new \InvalidArgumentException('value must be Project|int|string|null');
        }

        return Html::tag('span', Html::encode($name), ['class' => 'badge']);
    }

    /**
     * @param $value
     * @return string
     */
    public function asBooleanByLabel($value): string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        if ($value) {
            return Html::tag('span', 'Yes', ['class' => 'badge badge-success']);
        }
        return Html::tag('span', 'No', ['class' => 'badge badge-danger']);
    }
}
