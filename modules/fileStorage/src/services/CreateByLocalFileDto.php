<?php

namespace modules\fileStorage\src\services;

use dosamigos\arrayquery\conditions\GreaterThan;
use kartik\select2\ThemeDefaultAsset;
use modules\fileStorage\src\entity\fileStorage\FileStorageStatus;
use modules\fileStorage\src\entity\fileStorage\Path;
use modules\fileStorage\src\entity\fileStorage\Uid;
use yii\helpers\FileHelper;

/**
 * Class CreateByLocalFileDto
 *
 * @property string|null $name
 * @property string|null $title
 * @property string|null $path
 * @property int|null $size
 * @property string|null $uid
 * @property string|null $mimeType
 * @property string|null $md5Hash
 * @property bool|null $private
 * @property string|null $createdDt
 * @property int|null $status
 * @property string $pathToLocalFile
 * @property int|null $clientId
 * @property int|null $orderId
 * @property string $projectKey
 */
class CreateByLocalFileDto
{
    public string $pathToLocalFile;
    public ?int $clientId;
    public ?int $orderId;
    public string $projectKey;

    public ?string $name;
    public ?string $title;
    public ?string $path;
    public ?int $size;
    public ?string $uid;
    public ?string $mimeType;
    public ?string $md5Hash;
    public bool $private = false;
    public ?string $createdDt;
    public int $status = FileStorageStatus::UPLOADED;

    /**
     * @param string $pathToLocalFile
     * @param int $clientId
     * @param string $projectKey
     * @param string|null $title
     * @param int|null $orderId
     */
    public function __construct(
        string $pathToLocalFile,
        ?int $clientId,
        string $projectKey,
        string $title = 'Confirmation',
        ?int $orderId = null
    ) {
        $this->pathToLocalFile = $pathToLocalFile;
        $this->clientId = $clientId;
        $this->orderId = $orderId;
        $this->projectKey = $projectKey;
        $this->title = $title;
        $this->setAll();
    }

    private function setAll(): void
    {
        $this->setName()
            ->setUid()
            ->setPatch()
            ->setSize()
            ->setMimeType()
            ->setMd5Hash()
            ->setPrivate()
            ->setCreatedDt();
    }

    private function setMd5Hash(): CreateByLocalFileDto
    {
        $this->md5Hash = md5(file_get_contents($this->pathToLocalFile));
        return $this;
    }

    private function setMimeType(): CreateByLocalFileDto
    {
        $this->mimeType = FileHelper::getMimeType($this->pathToLocalFile);
        return $this;
    }

    private function setUid(): CreateByLocalFileDto
    {
        $this->uid = Uid::next()->getValue();
        return $this;
    }

    private function setSize(): CreateByLocalFileDto
    {
        $this->size = filesize($this->pathToLocalFile);
        return $this;
    }

    private function setName(): CreateByLocalFileDto
    {
        $patchExploded = explode(DIRECTORY_SEPARATOR, $this->pathToLocalFile);
        $this->name = end($patchExploded);
        return $this;
    }

    private function setPrivate(): CreateByLocalFileDto
    {
        return $this;
    }

    private function setCreatedDt(): CreateByLocalFileDto
    {
        $this->createdDt = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        return $this;
    }

    private function setPatch(): CreateByLocalFileDto
    {
        if ($this->clientId) {
            $path = new Path(
                PathGenerator::byClientAndUid(
                    $this->clientId,
                    $this->projectKey,
                    $this->name,
                    $this->uid
                )
            );
        } elseif ($this->orderId) {
            $path = new Path(
                PathGenerator::byOrder(
                    $this->orderId,
                    $this->projectKey,
                    $this->name,
                    $this->uid
                )
            );
        } else {
            throw new \DomainException('FilePatch error. Client or Order must be filled');
        }

        $this->path = $path->getValue();
        return $this;
    }
}
