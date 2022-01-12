<?php

namespace src\model\flightQuoteLabelList\service;

/**
 * Class FlightQuoteLabelListDictionary
 */
class FlightQuoteLabelListDictionary
{
    public const LABEL_PUB = 'PUB';
    public const LABEL_SR = 'SR';
    public const LABEL_COMM = 'COMM';
    public const LABEL_TOUR = 'TOUR';
    public const LABEL_SEP = 'SEP';

    public const MANUAL_CREATE_LABELS = [
        self::LABEL_PUB,
        self::LABEL_SR,
        self::LABEL_COMM,
        self::LABEL_TOUR,
    ];
}
