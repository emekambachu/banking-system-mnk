<?php

use Random\RandomException;

if (! function_exists('generateUniqueRandomString')) {
    /**
     * @throws RandomException
     */
    function generateUniqueRandomString(
        $model,
        string $column,
        int $length,
        string $characters = null,
        string $staticChar = ''
    ): string
    {
        if (!$characters) {
            $characters = '0123456789';
        }

        $charactersLength = strlen($characters);
        $randomString = $staticChar;
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        $check = $model::where($column, $randomString)->first();
        if ($check) {
            return generateUniqueRandomString($model, $column, $length, $characters, $staticChar);
        }

        return $randomString;
    }
}
