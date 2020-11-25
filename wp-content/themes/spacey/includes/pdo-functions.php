<?php
require_once 'pdo-functions-class.php';
//pdo link setup instead of the mysqli based wordpress.
global $link_pdo;
if (empty($link_pdo)) {
    $host = (defined(DB_HOST)) ? 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET : 'mysql:host=35.225.254.95;dbname=nuvane_wrdp1;charset=utf8';
    $link_pdo = (defined(DB_USER)) ? new PDO($host, DB_USER, DB_PASSWORD) : new PDO($host, 'nuvane_wrdp1', 'v[8KQEcOe2EQ');
    $link_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

if (!function_exists('is_iterable')) {
    function is_iterable($var)
    {
        return is_array($var) || $var instanceof \Traversable;
    }
}

if (!function_exists('array_flatten_assoc')) {
    /**
     * Reduce mutli-dimensional array to single dismension, keeps associative keys. Overwrites duplicate keys!
     *
     * @param array $array
     * @return array
     */
    function array_flatten_assoc($array) {
        $return = [];
        array_walk_recursive($array, function($a,$b) use (&$return) { $return[$b] = $a; });
        return $return;
    }
}

if (!function_exists('array_flatten')) {
    /**
     * Reduce mutli-dimensional array to single dismension
     *
     * @param array $array
     * @return array
     */
    function array_flatten($array) {
        $return = [];
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }
}

if (!function_exists('get_caller')) {
    /**
     * get the first parent that is not the stated file or the file this function is in (usually __FILE__ in the caller's context)
     *
     * @param string $except
     * @todo move this into debug_functions eventually.
     * @return array
     */
    function get_caller($except)
    {
        $dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,10);
        if (!is_array($except)) {
            $avoid = array(__FILE__,$except);
        } else {
            $avoid = array_flatten(array_merge($except, (array) __FILE__));
        }

        foreach ($dbt as $value) {
            if (!in_array($value['file'], $avoid)) {
                $caller = !empty($value) ? $value : null;
                break;
            }
        }
        //if we somehow didn't find one it's from an exception, return the last one in the stack.
        $caller = (empty($caller) && !empty($dbt[count($dbt) - 1])) ? $dbt[count($dbt) - 1] : null ;
        
        return $caller;
    }
}
