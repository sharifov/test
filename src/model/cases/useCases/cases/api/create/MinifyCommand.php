<?php

namespace src\model\cases\useCases\cases\api\create;

/**
 * Class MinifyCommand
 *
 * @property int $category_id
 * @property int $project_id
 * @property string|null $subject
 * @property string|null $description
 * @property bool $is_automate
 */
class MinifyCommand
{
    public int $category_id;
    public int $project_id;
    public ?string $subject;
    public ?string $description;
    public bool $is_automate;

    public function __construct(
        int $category_id,
        int $project_id,
        ?string $subject,
        ?string $description,
        int $is_automate
    ) {
        $this->category_id = $category_id;
        $this->project_id = $project_id;
        $this->subject = $subject;
        $this->description = $description;
        $this->is_automate = $is_automate;
    }
}
