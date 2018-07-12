<?php

if (! function_exists('diacritics_to_normal')) {
    /**
     * Заменяет диакретические символы в строке, на латинские аналоги
     *
     * @param  string $str
     * @return string
     */
    function diacritics_to_normal($str)
    {
        $search =  ['ã', 'â', 'á', 'à', 'č', 'đ', 'è', 'é', 'ê', 'ì', 'í', 'î', 'ň', 'ñ', 'ò', 'ó', 'ø', 'ť', 'ù', 'ú', 'ů', 'ý', 'Ž'];
        $replace = ['a', 'a', 'a', 'a', 'c', 'd', 'e', 'e', 'e', 'i', 'i', 'i', 'n', 'n', 'o', 'o', 'o', 't', 'u', 'u', 'u', 'y', 'Z'];

        return str_replace($search, $replace, $str);
    }
}
