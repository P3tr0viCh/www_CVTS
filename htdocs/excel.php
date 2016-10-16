<?php
require_once "include/Constants.php";
require_once "include/Functions.php";

$data = getPOSTParam(ParamName::EXCEL_DATA);
if ($data == null) {
    header("Location: " . "/index.php");
    exit();
}

$data = gzinflate(base64_decode($data));

$filename = getPOSTParam(ParamName::EXCEL_FILENAME);

header("content-type: application/octet-stream");

if (isset($HTTP_USER_AGENT) && strstr($HTTP_USER_AGENT, "MSIE")) {
    $attachment = "";
} else {
    $attachment = " attachment;";
}

header("content-disposition:$attachment filename=$filename");
header("pragma: no-cache");
header("expires: 0");
header("content-length: " . strlen($data));

echo $data;