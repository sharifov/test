<?php

namespace modules\mail\src\gmail\message;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_MessagePart;

class Attachment
{
	public $body;
	public $id;
	public $filename;
	public $mimeType;
	public $size;
	public $headerDetails;
	private $headers;
	/**
	 * @var Google_Service_Gmail
	 */
	private $service;
	private $messageId;

	public function __construct(Google_Client $client, $singleMessageId, Google_Service_Gmail_MessagePart $part)
	{
		$this->service = new Google_Service_Gmail($client);

		$body = $part->getBody();
		$this->id = $body->getAttachmentId();
		$this->size = $body->getSize();
		$this->filename = $part->getFilename();
		$this->mimeType = $part->getMimeType();
		$this->messageId = $singleMessageId;
		$headers = $part->getHeaders();
		$this->headerDetails = $this->getHeaderDetails($headers);
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getFileName(): ?string
	{
		return $this->filename;
	}

	public function getMimeType(): ?string
	{
		return $this->mimeType;
	}

	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @param  string  $path
	 * @param  string|null  $filename
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function saveAttachmentTo($path = null, $filename = null): string
	{
		$data = $this->getDecodedBody($this->getData());

		if (!$data) {
			throw new \Exception('Could not get the attachment.');
		}

		$filename = $filename ?: $this->filename;

		$filePathAndName = $path . $filename;

        file_put_contents($filePathAndName, $data);

		return $filePathAndName;
	}

	/**
	 * @throws \Exception
	 */
	public function getData()
	{
		$attachment = $this->service->users_messages_attachments->get('me', $this->messageId, $this->id);

		return $attachment->getData();
	}

	/**
	 * Returns attachment headers
	 * Contains Content-ID and X-Attachment-Id for embedded images
     *
     * @param $headers
	 *
	 * @return array
	 */
	public function getHeaderDetails($headers): array
	{
		$headerDetails = [];

		foreach ($headers as $header) {
			$headerDetails[$header->name] = $header->value;
		}

		return $headerDetails;
	}

    private function getDecodedBody($body): ?string
    {
        $decodedMessage = base64_decode(strtr($body, '-_', '+/'));
        if (!$decodedMessage) {
            return null;
        }
        return $decodedMessage;
    }
}
