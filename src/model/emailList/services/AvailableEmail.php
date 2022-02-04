<?php

namespace src\model\emailList\services;

/**
 * Class AvailableEmail
 *
 * @property int $projectId
 * @property string $project
 * @property string $email
 * @property int $emailListId
 * @property string $type
 * @property int $typeId
 * @property int|null $departmentId
 * @property string|null $department
 */
class AvailableEmail
{
    public const PERSONAL = 'Personal';
    public const PERSONAL_ID = 0;

    public const GENERAL = 'General';
    public const GENERAL_ID = 1;

    public int $projectId;
    public string $project;
    public int $emailListId;
    public string $email;
    public int $typeId;
    public string $type;
    public ?int $departmentId;
    public ?string $department;

    private function __construct(
        int $projectId,
        string $project,
        int $emailListId,
        string $email,
        int $typeId,
        string $type,
        ?int $departmentId,
        ?string $department
    ) {
        $this->projectId = $projectId;
        $this->project = $project;
        $this->emailListId = $emailListId;
        $this->email = $email;
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
            (int)$row['email_list_id'],
            $row['email'],
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

    public function isEqual(string $email): bool
    {
        return $this->email === $email;
    }

    public function format(): string
    {
        if ($this->isPersonalType()) {
            return $this->email . ' (' . self::PERSONAL . ')';
        }

        return $this->email . ' (General' . ($this->department ? ' ' . $this->department : '') . ')';
    }
}
