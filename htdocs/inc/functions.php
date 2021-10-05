<?php
/**
 * Global functions
 *
 * Author: Jonathan Barda / SIB - 2020
 */

/**
 * array_unique_recursive
 *
 * Recursive version of array_unique()
 *
 * @see https://www.php.net/manual/en/function.array-unique.php#116302
 * @author Jonathan Barda / SIB - 2020
 */
if (!function_exists('array_unique_recursive')) {
    function array_unique_recursive(array $array, $key)
    {
        $temp_array = [];
        $key_array = [];
        $sorted_array = [];

        $i = 0;
        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }

        foreach ($temp_array as $temp) {
            $sorted_array[] = $temp;
        }

        return $sorted_array;
    }
}

if (!function_exists('dd')) {
    function dd($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        exit();
    }
}

if (!function_exists('highlight_word')) {
    function highlight_word($searched_word, $content)
    {
        if (!$searched_word) {
            return $content;
        }
        return  preg_replace('/(' . $searched_word . ')/i', '<mark>$1</mark>', $content);
    }
}
