<?php

namespace modules\mail\src\gmail\message;

use Google_Client;
use Google_Service_Gmail_Message;
use Google_Service_Gmail_MessagePart;
use Tightenco\Collect\Support\Collection;
use yii\helpers\ArrayHelper;

/**
 * Class Gmail
 *
 * @property $id
 * @property $internalDate
 * @property $labels
 * @property $size
 * @property $threadId
 * @property Google_Service_Gmail_MessagePart $payload
 * @property $parts
 * @property $allParts
 * @property Google_Client $client
 * @property $raw
 */
class Gmail
{
	public $id;
	public $internalDate;
	public $labels;
	public $size;
	public $threadId;
	public $payload;
	public $parts;

    private $allParts;
    private $client;
    private $raw;

    public function __construct(Google_Client $client, Google_Service_Gmail_Message $message)
	{
        $this->client = $client;
	    $this->id = $message->getId();
	    $this->internalDate = $message->getInternalDate();
	    $this->labels = $message->getLabelIds();
	    $this->size = $message->getSizeEstimate();
	    $this->threadId = $message->getThreadId();
	    $this->payload = $message->getPayload();
	    if ($this->payload) {
	        $this->parts = collect($this->payload->getParts());
        }
	    $this->raw = $message->getRaw();
    }

	public function getInternalDate()
	{
		return $this->internalDate;
	}

	public function getLabels(): array
	{
		return $this->labels;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function getThreadId(): ?string
	{
		return $this->threadId;
	}

	public function getHeaders(): Collection
	{
		return $this->buildHeaders($this->payload->getHeaders());
	}

    public function getMessageId(): ?string
    {
        return $this->getHeader('Message-ID');
	}

    public function getReferences(): ?string
    {
        return $this->getHeader('References');
	}

	public function getSubject(): ?string
	{
		return $this->getHeader('Subject');
	}

	public function getFrom(): array
	{
		$from = $this->getHeader('From');

		preg_match('/<(.*)>/', $from, $matches);

		$name = preg_replace('/ <(.*)>/', '', $from);

		return [
			'name'  => $name,
			'email' => $matches[1] ?? null,
		];
	}

	public function getFromEmail(): ?string
	{
		$from = $this->getHeader('From');

		if (filter_var($from, FILTER_VALIDATE_EMAIL)) {
			return $from;
		}

		preg_match('/<(.*)>/', $from, $matches);

		return $matches[1] ?? null;
	}

	public function getFromName(): ?string
	{
		$from = $this->getHeader('From');

        return preg_replace('/ <(.*)>/', '', $from);
	}

    public function getToWithCc(): array
    {
        return array_unique(array_filter(ArrayHelper::getColumn((array_merge($this->getTo(), $this->getCc())), 'email'), static function ($element) {
            return $element ? true : false;
        }));
	}

	public function getTo(): array
	{
		$allTo = $this->getHeader('To');

		return $this->formatEmailList($allTo);
	}

	public function getCc(): array
	{
		$allCc = $this->getHeader('Cc');

		return $this->formatEmailList($allCc);
	}

	public function getBcc(): array
	{
		$allBcc = $this->getHeader('Bcc');

		return $this->formatEmailList($allBcc);
	}

	/**
	 * @param string $emails email list in RFC 822 format
	 *
	 * @return array
	 */
	public function formatEmailList($emails): array
	{
		$all = [];

		foreach (explode(',', $emails) as $email) {

			$item = [];

			preg_match('/<(.*)>/', $email, $matches);

			$item['email'] = str_replace(' ', '', $matches[1] ?? $email);

			$name = preg_replace('/ <(.*)>/', '', $email);

			if (self::startsWith($name, ' ')) {
				$name = substr($name, 1);
			}

			$item['name'] = str_replace('"', '', $name ?: null);

			$all[] = $item;

		}

		return $all;
	}
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }

	public function getDate(): ?string
	{
        return (new \DateTimeImmutable($this->getHeader('Date')))->format('Y-m-d H:i:s');
	}

	public function getDeliveredTo(): ?string
	{
		return $this->getHeader('Delivered-To');
	}

	public function getRawPlainTextBody(): ?string
	{
		return $this->getPlainTextBody(true);
	}

	public function getPlainTextBody($raw = false): ?string
	{
		$content = $this->getBody();

		return $raw ? $content : $this->getDecodedBody($content);
	}

    public function getContent(): ?string
    {
        if ($html = $this->getHtmlBody()) {
            return $html;
        }
        return $this->getPlainTextBody();
    }

	public function getBody($type = 'text/plain'): ?string
	{
		$parts = $this->getAllParts($this->parts);

        if (!$parts->isEmpty()) {
            foreach ($parts as $part) {
                if ($part->mimeType === $type) {
                    return $part->body->data;
                }
                //if there are no parts in payload, try to get data from body->data
                if ($this->payload->getBody()->getData()) {
                    return $this->payload->getBody()->getData();
                }
            }
        } else {
            return $this->payload->getBody()->getData();
        }

		return null;
	}

	public function hasAttachments(): bool
	{
		$parts = $this->getAllParts($this->parts);
		$has = false;

		/** @var Google_Service_Gmail_MessagePart $part */
		foreach ($parts as $part) {
			if (!empty($part->body->attachmentId) && $part->getFilename() !== null && strlen($part->getFilename()) > 0) {
				$has = true;
				break;
			}
		}

		return $has;
	}

	public function countAttachments(): int
	{
		$numberOfAttachments = 0;

		foreach ($this->getAllParts($this->parts) as $part) {
			if (!empty($part->body->attachmentId)) {
				$numberOfAttachments++;
			}
		}

		return $numberOfAttachments;
	}

	/**
	 * @return string base64 version of the body
	 */
	public function getRawHtmlBody(): ?string
	{
		return $this->getHtmlBody(true);
	}

	public function getHtmlBody($raw = false): ?string
	{
		$content = $this->getBody('text/html');

		return $raw ? $content : $this->getDecodedBody($content);
	}

	public function getAttachmentsWithData(): Collection
	{
		return $this->getAttachments(true);
	}

	/**
	 * Returns a collection of attachments
	 *
	 * @param bool $preload Preload only the attachment's 'data'.
	 * But does not load the other attachment info like filename, mimetype, etc..
	 *
	 * @return Collection
	 * @throws \Exception
	 */
	public function getAttachments($preload = false): Collection
	{
		$attachments = new Collection();

        foreach ($this->getAllParts($this->parts) as $part) {
			if (!empty($part->body->attachmentId)) {
				$attachment = (new Attachment($this->client, $part->body->attachmentId, $part));

				if ($preload) {
					$attachment = $attachment->getData();
				}

				$attachments->push($attachment);
			}
		}

		return $attachments;
	}

	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * checks if message has at least one part without iterating through all parts
	 *
	 * @return bool
	 */
	public function hasParts(): bool
	{
		return (bool)$this->iterateParts($this->parts, $returnOnFirstFound = true);
	}

	/**
	 * Gets all the headers from an email and returns a collections
	 *
	 * @param $emailHeaders
	 * @return Collection
	 */
	private function buildHeaders($emailHeaders): Collection
	{
		$headers = [];

		foreach ($emailHeaders as $header) {
			/** @var \Google_Service_Gmail_MessagePartHeader $header */

			$head = new \stdClass();

			$head->key = $header->getName();
			$head->value = $header->getValue();

			$headers[] = $head;
		}

		return collect($headers);
	}

    private function getDecodedBody($body): ?string
    {
        $decodedMessage = base64_decode(strtr($body, '-_', '+/'));
        if (!$decodedMessage) {
            return null;
        }
        return $decodedMessage;
    }

    /**
     * Find all Parts of a message. Necessary to reset the $allParts
     *
     * @param  collection  $partsContainer  . F.e. collect([$message->payload])
     *
     * @return Collection of all 'parts' flattened
     */
    private function getAllParts($partsContainer): Collection
    {
        $this->iterateParts($partsContainer);

        return collect($this->allParts);
    }

    /**
     * Recursive Method. Iterates through a collection, finding all 'parts'.
     *
     * @param  collection  $partsContainer
     * @param  bool  $returnOnFirstFound
     *
     * @return Collection|boolean
     */
    private function iterateParts($partsContainer, $returnOnFirstFound = false)
    {
        $parts = [];

        $plucked = $partsContainer->flatten()->filter();

        if ($plucked->count()) {
            $parts = $plucked;
        } else {
            if ($partsContainer->count()) {
                $parts = $partsContainer;
            }
        }

        if ($parts) {
            /** @var Google_Service_Gmail_MessagePart $part */
            foreach ($parts as $part) {
                if ($part) {
                    if ($returnOnFirstFound) {
                        return true;
                    }

                    $this->allParts[$part->getPartId()] = $part;
                    $this->iterateParts(collect($part->getParts()));
                }
            }
        }
    }

    /**
     * Gets a single header from an existing email by name.
     *
     * @param $headerName
     *
     * @param  string  $regex  if this is set, value will be evaluated with the give regular expression.
     *
     * @return null|string
     */
    public function getHeader($headerName, $regex = null): ?string
    {
        $headers = $this->getHeaders();

        $value = null;

        foreach ($headers as $header) {
            if (strtolower($header->key) === strtolower($headerName)) {
                $value = $header->value;
                if ($regex !== null) {
                    preg_match_all($regex, $header->value, $value);
                }
                break;
            }
        }

        if (is_array($value)) {
            return $value[1] ?? null;
        }

        return $value;
    }

    public function toArray(): array
    {
        $headers = [];
        foreach ($this->getHeaders() as $header) {
            $headers[$header->key] = $header->value;
        }

        return [
            'headers' => $headers,
            'textPlain' => $this->getPlainTextBody(),
            'html' => $this->getHtmlBody(),
        ];
    }

    public function getRaw($raw = false): ?string
    {
        $content = $this->raw;
        return $raw ? $content : $this->getDecodedBody($content);
    }
}
