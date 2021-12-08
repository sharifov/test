<?php

namespace sales\model\voip\phoneDevice\device;

/**
 * Class RandomStringGenerator
 *
 * @property string $alphabet
 * @property int $alphabetLength
 */
class RandomStringGenerator
{
    private string $alphabet;
    private int $alphabetLength;

    public function __construct()
    {
        $this->setAlphabet(
            implode(range('a', 'z'))
            . implode(range('A', 'Z'))
        );
    }

    private function setAlphabet($alphabet): void
    {
        $this->alphabet = $alphabet;
        $this->alphabetLength = strlen($alphabet);
    }

    public function generate($length): string
    {
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $randomKey = $this->getRandomInteger(0, $this->alphabetLength);
            $token .= $this->alphabet[$randomKey];
        }

        return $token;
    }

    private function getRandomInteger($min, $max): int
    {
        $range = ($max - $min);

        if ($range < 0) {
            // Not so random...
            return $min;
        }

        $log = log($range, 2);

        // Length in bytes.
        $bytes = (int)($log / 8) + 1;

        // Length in bits.
        $bits = (int)$log + 1;

        // Set all lower bits to 1.
        $filter = (int)(1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));

            // Discard irrelevant bits.
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);

        return ($min + $rnd);
    }
}
