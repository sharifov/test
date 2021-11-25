<?php

namespace sales\model\phone;

/**
 * Class AvailablePhone
 *
 * @property int $projectId
 * @property string $project
 * @property string $phone
 * @property int $phoneListId
 * @property string $type
 * @property int $typeId
 * @property int|null $departmentId
 */
class AvailablePhone
{
    public int $projectId;
    public string $project;
    public int $phoneListId;
    public string $phone;
    public int $typeId;
    public string $type;
    public ?int $departmentId;

    private function __construct(
        int $projectId,
        string $project,
        int $phoneListId,
        string $phone,
        int $typeId,
        string $type,
        ?int $departmentId
    ) {
        $this->projectId = $projectId;
        $this->project = $project;
        $this->phoneListId = $phoneListId;
        $this->phone = $phone;
        $this->typeId = $typeId;
        $this->type = $type;
        $this->departmentId = $departmentId;
    }

    public static function createFromRow(array $row): self
    {
        return new self(
            (int)$row['project_id'],
            $row['project'],
            (int)$row['phone_list_id'],
            $row['phone'],
            (int)$row['type_id'],
            $row['type'],
            $row['department_id'] ? (int)$row['department_id'] : null
        );
    }

    public function isGeneralType(): bool
    {
        return $this->typeId === AvailablePhoneList::GENERAL_ID;
    }

    public function isEqual(string $number): bool
    {
        return $this->phone === $number;
    }
}
