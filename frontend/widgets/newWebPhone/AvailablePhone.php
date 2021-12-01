<?php

namespace frontend\widgets\newWebPhone;

/**
 * Class AvailablePhone
 *
 * @property string $number
 * @property int $projectId
 * @property int|null $departmentId
 * @property string $title
 */
class AvailablePhone
{
    public string $number;
    public int $projectId;
    public ?int $departmentId;
    public ?string $title;

    public function __construct(string $number, int $projectId, ?int $departmentId, ?string $title)
    {
        $this->number = $number;
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
        $this->title = $title;
    }
}
