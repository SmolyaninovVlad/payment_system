<?php
require_once '../classes/api.calss.php';

if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}
try {
    $API = new API($_REQUEST['route'], $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
} catch (Exception $error) {
    echo json_encode(Array('error' => $error->getMessage()));
}
?>