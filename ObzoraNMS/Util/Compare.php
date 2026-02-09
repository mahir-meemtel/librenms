<?php
namespace ObzoraNMS\Util;

use Illuminate\Support\Str;

class Compare
{
    /**
     * Perform comparison of two items based on give comparison method
     * Valid comparisons: =, !=, ==, !==, >=, <=, >, <, contains, starts, ends, regex
     * contains, starts, ends: $a haystack, $b needle(s)
     * regex: $a subject, $b regex
     *
     * @param  mixed  $a
     * @param  mixed  $b
     * @param  string  $comparison  =, !=, ==, !== >=, <=, >, <, contains, starts, ends, regex
     * @return bool
     */
    public static function values($a, $b, $comparison = '=')
    {
        $numeric_comparisons = ['=', '!=', '==', '!==', '>=', '<=', '>', '<'];

        if (in_array($comparison, $numeric_comparisons)) {
            // handle PHP8 change to implicit casting
            if (is_numeric($a) || is_numeric($b)) {
                $a = Number::cast($a);
                $b = is_array($b) ? $b : Number::cast($b);
            }
        }

        switch ($comparison) {
            case '=':
                return $a == $b;
            case '!=':
                return $a != $b;
            case '==':
                return $a === $b;
            case '!==':
                return $a !== $b;
            case '>=':
                return $a >= $b;
            case '<=':
                return $a <= $b;
            case '>':
                return $a > $b;
            case '<':
                return $a < $b;
            case 'contains':
                return Str::contains($a, $b);
            case 'not_contains':
                return ! Str::contains($a, $b);
            case 'starts':
                return Str::startsWith($a, $b);
            case 'not_starts':
                return ! Str::startsWith($a, $b);
            case 'ends':
                return Str::endsWith($a, $b);
            case 'not_ends':
                return ! Str::endsWith($a, $b);
            case 'regex':
                return Str::isMatch($b, $a);
            case 'not_regex':
                return ! Str::isMatch($b, $a);
            case 'in_array':
                return in_array($a, $b);
            case 'not_in_array':
                return ! in_array($a, $b);
            case 'exists':
                return isset($a) == $b;
            default:
                return false;
        }
    }
}
