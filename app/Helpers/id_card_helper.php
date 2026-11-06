<?php

if (!function_exists('id_card_url')) {
    /**
     * Generates a secure URL for an ID card image.
     * 
     * @param string|null $path The path stored in the database.
     * @return string
     */
    function id_card_url($path)
    {
        if (empty($path)) {
            return base_url('public/img/no-image.png');
        }

        if (strpos($path, 'http') === 0) {
            return $path;
        }

        $filename = basename($path);

        return base_url("secure-file/id-card/" . $filename);
    }
}

if (!function_exists('id_card_normalize')) {
    /**
     * Strip every non-digit so dashed/spaced IDs compare equal.
     * '1-5099-67388-00-9' -> '1509967388009'
     */
    function id_card_normalize(?string $idCard): string
    {
        return preg_replace('/\D+/', '', (string) $idCard);
    }
}

if (!function_exists('id_card_is_valid')) {
    /**
     * Valid Thai national ID: exactly 13 digits, passing the mod-11 check digit,
     * and not a single repeated digit (placeholder like 1111111111111 / 0000000000000).
     */
    function id_card_is_valid(?string $idCard): bool
    {
        $d = id_card_normalize($idCard);
        if (strlen($d) !== 13) {
            return false;
        }
        if (preg_match('/^(\d)\1{12}$/', $d)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $d[$i] * (13 - $i);
        }
        $check = (11 - ($sum % 11)) % 10;
        return $check === (int) $d[12];
    }
}
