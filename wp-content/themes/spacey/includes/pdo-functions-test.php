<?php

use RyanKikta\PDO_DB;
use RyanKikta\PDO_Parameter;

use function RyanKikta\PDO_Call;
use function RyanKikta\PDO_Var;

require_once 'pdo-functions.php';
require_once 'debug-functions.php';
try {
    debug_filehit(__FILE__);
} catch (\Exception $th) {
    debug_line(json_encode($th));
}

global $link_pdo;

$value_string = "oogaboogafoxjumping the lazy-dog /s ; select 'fail'";
debug_line('Test Data (string):' . $value_string);
$value_string_array = array_fill(0,10,$value_string);

$value_int = 428008135;
$value_int_array = array_fill(0,10,$value_int);
$value_bad_array = array(1,2,3,4,5,'bad type',null, '');

/**
 * outputs state of parameter for testing
 *
 * @param RyanKikta\PDO_Parameter $param
 * @param string $test
 * @return void
 */
function parameter_state(&$param, $test) {
    debug_line('Parameter Test: ' . $param->tag);
    debug_line('Parameter Type: ' . $param->type);
    
    debug_line("Compound? " . $param->is_compound);
    debug_line("Value: " . is_null($param->value) . json_encode($param->value) ,2);
    
    if ($param->is_compound) {
        debug_line("As in list: " . $param->in_string(),2);
    }
}

debug_line("<hr><br>PARAMETER TESTING",2);

try {
    $param_test_string = new PDO_Parameter('teststr', $value_string);
    parameter_state($param_test_string, 'string test');
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

try {
    $param_test_string_bad = new PDO_Parameter('teststrbad', $value_string, \PDO::PARAM_INT);
    parameter_state($param_test_string_bad, 'bad string test');
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

try {
    $param_test_string_null = new PDO_Parameter('teststrnull', NULL);
    parameter_state($param_test_string_null, 'null string test');
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

try {
    $param_test_string_array = new PDO_Parameter('teststrarray',$value_string_array);
    parameter_state($param_test_string_array, 'string array test');
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

try {
    $param_test_int_array = new PDO_Parameter('teststrarray',$value_int_array);
    parameter_state($param_test_int_array, 'int array in string test');
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

try {
    $param_test_int = new PDO_Parameter('testint', $value_int, \PDO::PARAM_INT);
    parameter_state($param_test_int, 'int test');
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

try {
    $param_test_int_array = new PDO_Parameter('testintarray', $value_int_array, \PDO::PARAM_INT);
    parameter_state($param_test_int_array, 'int array test');
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

try {
    $param_test_bad_array = new PDO_Parameter('testintarray', $value_bad_array, \PDO::PARAM_INT);
    parameter_state($param_test_bad_array, 'int bad array test');
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

debug_line("<hr><br>ESTABLISH PDO OBJECT",2);
try {
    $dbo = new PDO_DB($link_pdo);
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

debug_line("<hr><br>QUERY TESTING",2);
try {
    $dbo->Query("
        SELECT COUNT(o.`order_id`) as `ocount` FROM wp_rmproductmanagement_orders o
    ");
    debug_line("Query: " . $dbo->Query());

    $result = $dbo->Exact();
    debug_line("Number of Orders (integer): {$result}");
    
    $result = $dbo->Exact("ocount");
    debug_line("Number of Orders (Assoc): {$result}");
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}

try {
    $dbo->Query("
        SELECT COUNT(o.`order_id`) as `ocount` FROM wp_rmproductmanagement_orders o WHERE o.order_time >= unix_timestamp(:odat)
        ");

    debug_line("Query: " . $dbo->Query());

    $today = date("Y-m-d");
    $dbo->params = [];
    $dbo->params[] = new PDO_Parameter("odat",$today);
    debug_line("Parameter used: " . $today);

    $result = $dbo->Exact();
    debug_line("Number of Orders for today parameter (integer): {$result}");
    
    $result = $dbo->Exact("ocount");
    debug_line("Number of Orders for today parameter (Assoc): {$result}");
} catch (\Exception $th) {
    debug_line('FAILED:' . $th->getMessage(),2);
}