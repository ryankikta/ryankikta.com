<?php
require_once 'pdo-functions.php';

use RyanKikta\PDO_Parameter;
use RyanKikta\PDO_DB;

/**
 * creates a log that a given file was executed
 *
 * @param string $source
 * @param mixed $notes
 * @return void
 */
function debug_filehit($source, $notes = '')
{
    global $link_pdo;

    try {
        $source_info = pathinfo($source);
        $source_basename = strtolower($source_info['basename']);

        if (debug_hit_exists($source_basename)) {
            return false; //avoid hammering database with inserts, once in the last hour is enough.
        }

        $dbo = new PDO_DB($link_pdo);

        $servername = isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];

        //reversed due to optional field
        if (is_scalar($notes)) {
            $notes = trim((string) $notes);
        } elseif (is_array($notes)) {
            $notes = @json_encode($notes);
        } elseif (is_resource($notes)) {
            $notes = 'Unreadable object';
        }

        $dbo->Query('
            INSERT INTO `cfs_hitcount` 
            (`source`, `source_fullpath`,`server`,`notes`)
            VALUES
            (:source, :source_full, :server, :notes)
            ');

        $dbo->params = [
            new PDO_Parameter('source', $source_basename, PDO::PARAM_STR),
            new PDO_Parameter('source_full', $source, PDO::PARAM_STR),
            new PDO_Parameter('server', $servername, PDO::PARAM_STR),
            new PDO_Parameter('notes', $notes, PDO::PARAM_STR)
        ];

        $dbo->Call();

        return true;
    } catch (\Exception $e) {
        $message = $e->getMessage();
        error_log("Unable to register file hit for source {$source} due to error: {$message}");
    }
}

function debug_hit_exists($source)
{
    global $link_pdo;

    $result = false;
    try {
        static $dbo; //this call may happen a lot and we can afford a static for an exists

        if (empty($dbo)) {
            $dbo = new PDO_DB($link_pdo);
            $dbo->Query('SELECT EXISTS(SELECT 1 FROM `cfs_hitcount` WHERE `source` = :source  AND `call_time` >= (now() - interval 1 hour))');
        }

        $dbo->params = [new PDO_Parameter('source', strtolower($source))];
        $result = $dbo->Exact();

        $exists = !is_null($result) && $result == true;
        if (!empty($_GET['debug']) && $_GET['debug'] === basename(__FILE__, '.php')) {
            debug_line("result was {$exists}<br>");
        }
        return $exists;
    } catch (\Exception $e) {
        $message = $e->getMessage();
        error_log("Unable to check for file hit for source {$source} due to error: {$message}");
    }
    return true; //should this somehow fail, fail closed
}

function debug_is_self($source)
{
    $self = strcasecmp(strtok(basename($_SERVER['REQUEST_URI']), '?'), basename($source));
    return $self === 0;
}

function is_debug_call()
{
    $debug_flag = !empty($_GET['debug']) ? $_GET['debug'] : $_POST['debug'];
    $debug_flag = filter_var($debug_flag, FILTER_VALIDATE_BOOLEAN, array('options' => array('default' => false)));
    return $debug_flag;
}

function validation_debug($data, $user_id, $debug_level = 0)
{
    if (!is_debug_call()) {
        return '';
    }

    $debug_level_request = !empty($_GET['api_debug_level']) ? $_GET['api_debug_level'] : $_POST['api_debug_level'];
    $debug_level_request = intval($debug_level_request);

    if ($debug_level_request >= $debug_level || get_user_meta($user_id, 'api_debug_level', true) >= $debug_level) {
        return $data;
    }
    return '';
}

function debug_line($message, $numbreaks = 1, $endline = true)
{
    $breaks = str_repeat('<br>', $numbreaks);
    $end = $endline ? PHP_EOL : '';
    echo $message . $breaks . $end;
}
