<?php

namespace sales\helpers\payment;

class CreditCardHelper
{

    /**
     * Replaces all but the last for digits with x's in the given credit card number
     * @param string $cc The credit card number to mask
     * @param string $mask The credit card mask symbol
     * @param int $endCount
     * @param int $startCount
     * @return string The masked credit card number
     */
    public static function maskCreditCard(string $cc, string $mask = '*', int $endCount = 4, int $startCount = 4): string
    {
        // Get the cc Length
        $cc_length = strlen($cc);
        // Replace all characters of credit card except the last four and dashes
        for ($i = 0; $i < $cc_length - $endCount; $i++) {
            if ($cc[$i] === '-') {
                continue;
            }

            if ($startCount && $i < $startCount) {
                continue;
            }
            $cc[$i] = $mask;
        }
        // Return the masked Credit Card #
        return $cc;
    }

    /**
     * Add dashes to a credit card number.
     * @param string $cc The credit card number to format.
     * @param string $separator The credit card separator.
     * @return string The credit card with dashes.
     */
    public static function formatCreditCard(string $cc, string $separator = '-'): string
    {
        // Clean out extra data that might be in the cc
        $cc = str_replace(['-', ' '], '', $cc);
        // Get the CC Length
        $cc_length = strlen($cc);
        // Initialize the new credit card to contian the last four digits
        $newCreditCard = substr($cc, -4);
        // Walk backwards through the credit card number and add a dash after every fourth digit
        for ($i = $cc_length - 5; $i >= 0; $i--) {
            // If on the fourth character add a dash
            if ((($i + 1) - $cc_length) % 4 === 0) {
                $newCreditCard = $separator . $newCreditCard;
            }
            // Add the current character to the new credit card
            $newCreditCard = $cc[$i] . $newCreditCard;
        }
        // Return the formatted credit card number
        return $newCreditCard;
    }

}
