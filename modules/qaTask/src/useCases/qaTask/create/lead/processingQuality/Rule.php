<?php

namespace modules\qaTask\src\useCases\qaTask\create\lead\processingQuality;

use Webmozart\Assert\Assert;

/**
 * Class Rule
 *
 * @property int $calls_per_frame
 * @property int $out_min_duration
 * @property int $in_min_rec_duration
 * @property bool $include_in_calls
 * @property int $hour_offset
 * @property int $hour_frame_1
 * @property int $hour_frame_2
 * @property int $hour_frame_3
 */
class Rule
{
    public $calls_per_frame;
    public $out_min_duration;
    public $in_min_rec_duration;
    public $include_in_calls;
    public $hour_offset;
    public $hour_frame_1;
    public $hour_frame_2;
    public $hour_frame_3;

    public function __construct($params)
    {
        try {
            Assert::isArray($params);
            Assert::keyExists($params, 'calls_per_frame');
            Assert::keyExists($params, 'out_min_duration');
            Assert::keyExists($params, 'in_min_rec_duration');
            Assert::keyExists($params, 'include_in_calls');
            Assert::keyExists($params, 'hour_offset');
            Assert::keyExists($params, 'hour_frame_1');
            Assert::keyExists($params, 'hour_frame_2');
            Assert::keyExists($params, 'hour_frame_3');
        } catch (\Throwable $e) {
            throw new \DomainException($e->getMessage());
        }

        $this->calls_per_frame = (int)$params['calls_per_frame'];
        $this->out_min_duration = (int)$params['out_min_duration'];
        $this->in_min_rec_duration = (int)$params['in_min_rec_duration'];
        $this->include_in_calls = (bool)$params['include_in_calls'];
        $this->hour_offset = (int)$params['hour_offset'];
        $this->hour_frame_1 = (int)$params['hour_frame_1'];
        $this->hour_frame_2 = (int)$params['hour_frame_2'];
        $this->hour_frame_3 = (int)$params['hour_frame_3'];
    }
}
