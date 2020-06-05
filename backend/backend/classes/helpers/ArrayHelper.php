<?php
namespace backend\classes\helpers;

use yii\helpers\ReplaceArrayValue;
use yii\helpers\UnsetArrayValue;

/**
 * Хелпер для работы с массивами
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * Приводит все элементы массива к типам, описанным в $nested_array_config
     * @param array $array
     * @param array $nested_array_config, например ['id'=> 'integer', 'title'=>'string']
     * @return array
     */
    public static function getArrayWithNeededTypes(array $array, array $nested_array_config): array
    {
        foreach ($array as $key => $item) {
            foreach ($nested_array_config as $nested_key => $type) {
                switch ($type) {
                    case 'integer':
                        $array[$key][$nested_key] = (int)$item[$nested_key];
                        break;
                    case 'string':
                        $array[$key][$nested_key] = (string)$item[$nested_key];
                        break;
                    case 'boolean':
                        $array[$key][$nested_key] = (bool)$item[$nested_key];
                        break;
                    case 'array':
                        $array[$key][$nested_key] = (array)$item[$nested_key];
                        break;
                }
            }
        }

        return $array;
    }

    /**
     * Сортирует двумерный массив по значению поля вложенного массива.
     * Например:
     * $array = [
     *      0 => ['id' => 2, 'title' => 'Петя']
     *      1 => ['id' => 1, 'title' => 'Уася']
     * ]
     * $sorted_array = self::sortTwoDimensionArray($array, 'id', SORT_ASC);
     *
     * в $sorted_array будет:
     * [
     *     0 => ['id' => 1, 'title' => 'Уася']
     *     1 => ['id' => 2, 'title' => 'Петя']
     * ]
     *
     * @param array $array
     * @param $nested_key
     * @param $sort_direction
     * @return array
     */
    public static function sortTwoDimensionArray(array $array, $nested_key, $sort_direction): array
    {
        $temp_array = [];
        foreach($array as $key => $nested_array) {
            $temp_array[$key] = $nested_array[$nested_key];
        }
        natcasesort($temp_array);
        $sorted_keys = array_keys($temp_array);
        unset($temp_array);

        $sorted_array = [];
        foreach ($sorted_keys as $key) {
            $sorted_array[] = $array[$key];
        }
        unset($sorted_keys);

        if ($sort_direction === SORT_DESC) {
            return array_reverse($sorted_array);
        }

        return $sorted_array;
    }

    /**
     * Метод для помощи в заполнении DataProvider методов unit тестов
     * Получает массив данных в виде json, не забыть указать JSON_UNESCAPED_UNICODE при подготовке массива:
     * ArrayHelper::jsonToPhpArray(json_encode($original_row, JSON_UNESCAPED_UNICODE ))
     * Возвращает массив в виде строки
     *
     * @param $json
     * @return null|string
     */
    public static function jsonToPhpArray($json): ?string
    {
        $search = ['{', ':', '}'];
        $replace = ['[', '=>', ']'];
        $php_array = str_replace($search, $replace, $json);
        return preg_replace('/\"(\d+)\"\=\>/', '${1}=>', $php_array);
    }

    /**
     * Преобразует объект или массив объектов в массив
     * @param mixed $data
     * @return array
     */
    public static function objectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = self::objectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    /**
     * Генерирует многомерный массив на основании массива ключей $keys, уровень вложенности соответствует
     * количеству ключей. Например для массива ключей ['k1', 'k2', 'k3']:
     * [
     *	k1 => [
     * 		k2 => [
     * 			k3 => [
     * 				$value
     * 			]
     * 		]
     *  ]
     *]
     * @param array $keys
     * @param mixed $value
     * @return array
     */
    public static function recursiveGenerateArrayFromArrayKeys(array $keys, $value): array
    {
        $array = [];
        $key = null;

        while (!$key) {
            $key = array_shift($keys);
        }

        if (!$keys) {
            $array[$key] = $value;
            return $array;
        } else {
            $array[$key] = static::recursiveGenerateArrayFromArrayKeys($keys, $value);
            return $array;
        }
    }

    /**
     * Рекурсивно объединяет два и более массивов. Отличие от array_merge_recursive()
     * в том, что числовые ключи при совпадении также обединяются
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     */
    public static function mergeRecursive($a, $b): array
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            foreach (array_shift($args) as $k => $v) {
                if ($v instanceof UnsetArrayValue) {
                    unset($res[$k]);
                } elseif ($v instanceof ReplaceArrayValue) {
                    $res[$k] = $v->value;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = static::mergeRecursive($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * Преобразует строку в формате print_r обратно в массив
     * @param string $in - строковое предстваление массива в формате print_r
     * @return array|string
     */
    public static function printRReverse($in)
    {
        $lines = explode("\n", trim($in));
        if (trim($lines[0]) !== 'Array') {
            // bottomed out to something that isn't an array
            return $in;
        }
        // this is an array, lets parse it
        if (preg_match("/(\s{5,})\(/", $lines[1], $match)) {
            // this is a tested array/recursive call to this function
            // take a set of spaces off the beginning
            $spaces = $match[1];
            $spaces_length = strlen($spaces);
            $lines_total = count($lines);
            for ($i = 0; $i < $lines_total; $i++) {
                if (substr($lines[$i], 0, $spaces_length) == $spaces) {
                    $lines[$i] = substr($lines[$i], $spaces_length);
                }
            }
        }
        array_shift($lines); // Array
        array_shift($lines); // (
        array_pop($lines); // )
        $in = implode("\n", $lines);
        // make sure we only match stuff with 4 preceding spaces (stuff for this array and not a nested one)
        preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $pos = array();
        $previous_key = '';
        $in_length = strlen($in);
        // store the following in $pos:
        // array with key = key of the parsed array's item
        // value = array(start position in $in, $end position in $in)
        foreach ($matches as $match) {
            $key = $match[1][0];
            $start = $match[0][1] + strlen($match[0][0]);
            $pos[$key] = array($start, $in_length);
            if ($previous_key !== '') $pos[$previous_key][1] = $match[0][1] - 1;
            $previous_key = $key;
        }
        $ret = array();
        foreach ($pos as $key => $where) {
            // recursively see if the parsed out value is an array too
            $ret[$key] = self::printRReverse(substr($in, $where[0], $where[1] - $where[0]));
        }

        return $ret;
    }

    /**
     * Выводит массив в виде php-массива :)
     * @param array $array
     * @return string
     */
    public static function arrayToPhpArray(array $array): string
    {
        $out = '[';
        $count = count($array);
        $i = 0;
        foreach ($array as $key => $val) {
            $i++;
            $coma = $i === $count ? '' : ',';
            if (is_array($val)) {
                $out .= $i === 1 ? PHP_EOL.$key : $key;
                $out .= ' => ';
                $out .= self::arrayToPhpArray($val).$coma.PHP_EOL;
            } else {
                $key = is_numeric($key) ? $key : "'" . $key . "'";
                $out .= $key;
                $out .= ' => ';
                $out .= "'" . $val . "'".$coma;
            }
        }

        $out .= ']';
        return $out;
    }

    /**
     * Преобразует входящий массив($array) со строковыми ключами в индексированный массив,
     * отсортированный по переданному массиву ключей($sort_keys)
     *
     * Например:
     * $array = [ key1 => value1, key2 => value2, key3 => value3]
     * $sort_keys = [key2, key3, key1]
     * Тогда получим в результате преобразования $array = [value2, value3, value1]
     *
     * @param array $array
     * @param array $sort_keys
     */
    public static function sortArrayInGivenOrder(array &$array, array $sort_keys): void
    {
        foreach ($array as $key => $value) {
            unset($array[$key]);

            if (in_array($key, $sort_keys)) {
                $array[array_search($key, $sort_keys)] = $value;
            }
        }
    }
}