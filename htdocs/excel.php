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

if (isset($HTTP_USER_AGENT) && strstr($HTTP_USER_AGENT, "MSIE")) {
    $attachment = "";
} else {
    $attachment = " attachment;";
}

header("Content-type: text/csv");
header("Content-Disposition:$attachment filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Length: " . strlen($data));

echo $data;