<?php
use ObzoraNMS\Util\Debug;

$init_modules = ['web', 'auth'];
require realpath(__DIR__ . '/..') . '/includes/init.php';

if (! Auth::check()) {
    exit('Unauthorized');
}

Debug::set($_REQUEST['debug']);

/**
 * Levenshtein Sort
 *
 * @param  string  $base  Comparison basis
 * @param  array  $obj  Object to sort
 * @return array
 */
function levsortos($base, $obj, $keys)
{
    $ret = [];
    foreach ($obj as $elem) {
        $lev = false;
        foreach ($keys as $key) {
            $levnew = levenshtein(strtolower($base), strtolower($elem[$key]), 1, 10, 10);
            if ($lev === false || $levnew < $lev) {
                $lev = $levnew;
            }
        }
        while (isset($ret["$lev"])) {
            $lev += 0.1;
        }

        $ret["$lev"] = $elem;
    }

    ksort($ret);

    return $ret;
}

header('Content-type: application/json');
if (isset($_GET['term'])) {
    $_GET['term'] = strip_tags($_GET['term']);
    $sortos = levsortos($_GET['term'], \App\Facades\ObzoraConfig::get('os'), ['text', 'os']);
    $sortos = array_slice($sortos, 0, 20);
    foreach ($sortos as $lev => $os) {
        $ret[$lev] = array_intersect_key($os, ['os' => true, 'text' => true]);
    }
}
if (! isset($ret)) {
    $ret = [['Error: No suggestions found.']];
}

exit(json_encode($ret));
