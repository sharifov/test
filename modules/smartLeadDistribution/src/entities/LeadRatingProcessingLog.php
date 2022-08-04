<?php

namespace modules\smartLeadDistribution\src\entities;

use modules\smartLeadDistribution\src\objects\LeadRatingObjectInterface;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @property-read integer $points
 * @property-read string $object
 * @property-read string $objectName
 * @property-read string $attribute
 * @property-read string $attributeName
 * @property-read string $value
 */
class LeadRatingProcessingLog extends Model
{
    private LeadRatingParameter $leadRatingParameter;
    private LeadRatingObjectInterface $parameterObject;
    private $dto;

    public function __construct(LeadRatingParameter $leadRatingParameter, $dto, $config = [])
    {
        $this->leadRatingParameter = $leadRatingParameter;
        $this->dto = $dto;
        $this->parameterObject = SmartLeadDistributionService::getByName(
            $this->leadRatingParameter->lrp_object
        );

        parent::__construct($config);
    }

    public function getPoints(): int
    {
        return $this->leadRatingParameter->lrp_point;
    }

    public function getObject(): string
    {
        return $this->leadRatingParameter->lrp_object;
    }

    public function getObjectName(): string
    {
        return SmartLeadDistribution::OBJ_LIST[
            $this->leadRatingParameter->lrp_object
        ];
    }

    public function getAttribute(): string
    {
        return $this->leadRatingParameter->lrp_attribute;
    }

    public function getAttributeName(): string
    {
        return $this->parameterObject::getDataForField(
            $this->leadRatingParameter->lrp_attribute
        )['label'];
    }

    public function getValue(): string
    {
        return (string) ArrayHelper::getValue(
            [$this->parameterObject::OBJ => $this->dto],
            $this->attribute,
            ''
        );
    }
}
