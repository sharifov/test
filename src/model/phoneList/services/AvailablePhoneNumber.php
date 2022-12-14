<?php

namespace src\model\phoneList\services;

/**
 * Class AvailablePhoneNumber
 *
 * @property int $projectId
 * @property string $project
 * @property string $phone
 * @property int $phoneListId
 * @property string $type
 * @property int $typeId
 * @property int|null $departmentId
 * @property string|null $department
 */
class AvailablePhoneNumber
{
    public const PERSONAL = 'Personal';
    public const PERSONAL_ID = 0;

    public const GENERAL = 'General';
    public const GENERAL_ID = 1;

    public int $projectId;
    public string $project;
    public int $phoneListId;
    public string $phone;
    public int $typeId;
    public string $type;
    public ?int $departmentId;
    public ?string $department;

    private function __construct(
        int $projectId,
        string $project,
        int $phoneListId,
        string $phone,
        int $typeId,
        string $type,
        ?int $departmentId,
        ?string $department
    ) {
        $this->projectId = $projectId;
        $this->project = $project;
        $this->phoneListId = $phoneListId;
        $this->phone = $phone;
        $this->typeId = $typeId;
        $this->type = $type;
        $this->departmentId = $departmentId;
        $this->department = $department;
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
            $row['department_id'] ? (int)$row['department_id'] : null,
            $row['department'] ?: null,
        );
    }

    public function isGeneralType(): bool
    {
        return $this->typeId === self::GENERAL_ID;
    }

    public function isPersonalType(): bool
    {
        return $this->typeId === self::PERSONAL_ID;
    }

    public function isEqual(string $number): bool
    {
        return $this->phone === $number;
    }

    public function format(): string
    {
        if ($this->isPersonalType()) {
            return $this->phone . ' (' . self::PERSONAL . ')';
        }

        return $this->phone . ' (General' . ($this->department ? ' ' . $this->department : '') . ')';
    }
}
