<?php

namespace  webapi\src\forms\flight\flights\trips;

use yii\base\Model;

/**
 * Class SegmentApiForm
 *
 * @property $tripKey
 */
class SegmentApiForm extends Model
{
    public $segmentId; /* TODO::  */

    private int $tripKey;

    public function __construct(int $tripKey, $config = [])
    {
        $this->tripKey = $tripKey;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['bookingId'], 'required'],
        ];
    }

    public function getTripKey(): int
    {
        return $this->tripKey;
    }
}
