<?php
ob_start();
$timeStart = microtime(true);

require_once "include/Constants.php";
require_once "include/ParamName.php";

require_once "include/MySQLConnection.php";

require_once "include/QueryIron.php";
require_once "include/QueryCoeffs.php";
require_once "include/QueryResult.php";
require_once "include/QueryCompare.php";
require_once "include/QuerySensors.php";
require_once "include/QuerySensorsInfo.php";
require_once "include/QueryControl.php";
require_once "include/QueryVanListWeighs.php";
require_once "include/QueryVanListLastTare.php";

require_once "include/Strings.php";
require_once "include/ColumnsStrings.php";

require_once "include/Functions.php";
require_once "include/CheckUser.php";
require_once "include/CheckBrowser.php";

require_once "include/ScaleInfo.php";
require_once "include/ResultFilter.php";

require_once "include/builders/DateTimeBuilder.php";
require_once "include/builders/href_builder/Builder.php";

require_once "include/echo_html_page.php";
require_once "include/echo_table.php";
require_once "include/echo_form.php";

require_once "include/HtmlHeader.php";
require_once "include/HtmlDrawer.php";

use database\Aliases;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

use builders\DateTimeBuilder;
use builders\href_builder\Builder;
use database\Columns as C;
use Strings as S;

#[NoReturn] function throwBadRequest(?string $error = null, int $errorNum = 412)
{
    if (Constants::DEBUG_SHOW_ERROR) {
        if (empty($error)) $error = "UNKNOWN";
        echo '<b><span class="color-text--error">' . "ERROR $errorNum: $error" . '</span></b>';
        exit();
    }

    ob_end_clean();
    header("Location: error.php?$errorNum", true, 303);
    exit();
}

if (!isset($_GET[ParamName::SCALE_NUM])) {
    if (!isset($_GET[ParamName::REPORT_TYPE])) {
        if (!isset($_GET[ParamName::RESULT_TYPE])) {
            throwBadRequest('empty SCALE_NUM&&REPORT_TYPE&&RESULT_TYPE');
        }
    }
}

ini_set('display_errors', false);

register_shutdown_function(callback: function () {
    $error = error_get_last();

    if ($error) {
        if (str_starts_with($error['message'], 'Allowed memory size')) {
            $errorNum = 530;
        } elseif (str_starts_with($error['message'], 'Maximum execution time')) {
            $errorNum = 531;
        } else {
            $errorNum = 500;
        }

        throwBadRequest(json_encode($error), $errorNum);
    }
});

$newDesign = isNewDesign();

$useBackup = false;

$reportType = getParamGETAsInt(ParamName::REPORT_TYPE, ReportType::TYPE_DEFAULT);

$resultType = null;

$scaleNum = null;

$title = S::TITLE_ERROR;

$excelData = null;

$header = null;
$subHeader = null;
$whereHeader = null;
$navLinks = null;
$menuItems = null;

$resultMessage = null;

$dtStartDay = null;
$dtStartMonth = null;
$dtStartYear = null;
$dtStartHour = null;
$dtStartMinute = null;

$dtEndDay = null;
$dtEndMonth = null;
$dtEndYear = null;
$dtEndHour = null;
$dtEndMinute = null;

$dateTimeStart = null;
$dateTimeEnd = null;

$orderByDesc = true;
$from20to20 = false;

$showDisabled = false;

$vanList = null;

$showTotalSums = false;
$totalSumBrutto = 0.0;
$totalSumTare = 0.0;
$totalSumNetto = 0.0;

$ironControlTotalAvg = 0.0;
$ironControlTotalSum = 0.0;
$ironControlTotalCount = 0;

$filter = new ResultFilter();

$scaleNum = getParamGETAsInt(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES);

if ($scaleNum < 0) {
    if ($scaleNum !== Constants::SCALE_NUM_REPORT_VANLIST and
        $scaleNum !== Constants::SCALE_NUM_REPORT_IRON and
        $scaleNum !== Constants::SCALE_NUM_REPORT_IRON_CONTROL and
        $scaleNum !== Constants::SCALE_NUM_REPORT_SLAG_CONTROL) {
        $scaleNum = Constants::SCALE_NUM_ALL_TRAIN_SCALES;
    }
}

switch ($reportType) {
    case ReportType::TYPE_DEFAULT:

        $useBackup = getParamGETAsBool(ParamName::USE_BACKUP, $useBackup);

        $showDisabled = getCookieAsBool(ParamName::SHOW_DISABLED);

        $filter
            ->setVanNumber(getParamGETAsString(ParamName::VAN_NUMBER))
            ->setCargoType(getParamGETAsString(ParamName::CARGO_TYPE))
            ->setInvoiceNum(getParamGETAsString(ParamName::INVOICE_NUM))
            ->setInvoiceSupplier(getParamGETAsString(ParamName::INVOICE_SUPPLIER))
            ->setInvoiceRecipient(getParamGETAsString(ParamName::INVOICE_RECIPIENT))
            ->setOnlyChark(getParamGETAsBool(ParamName::ONLY_CHARK))
            ->setScalesFilter(getParamGETAsString(ParamName::SCALES))
            ->setFull(getParamGETAsBool(ParamName::ALL_FIELDS, false))
            ->setShowCargoDate(getParamGETAsBool(ParamName::SHOW_CARGO_DATE))
            ->setShowDeltas(getParamGETAsBool(ParamName::SHOW_DELTAS))
            ->setShowDeltasMi3115(getParamGETAsBool(ParamName::SHOW_DELTAS_MI_3115))
            ->setCompareForward(getParamGETAsBool(ParamName::COMPARE_FORWARD))
            ->setCompareByBrutto(getParamGETAsBool(ParamName::COMPARE_BY_BRUTTO));

        $resultType = getParamGETAsInt(ParamName::RESULT_TYPE);

        // TODO: old style form with POST
        if (empty($resultType)) {

            #[Pure] function getResultType(int $type): ?int
            {
                $param = getParamGETAsString(ParamName::RESULT_TYPE . "_" . $type);
                return empty($param) ? null : $type;
            }

            $resultTypes = array(
                ResultType::VAN_DYNAMIC_BRUTTO,
                ResultType::VAN_DYNAMIC_TARE,
                ResultType::VAN_STATIC_BRUTTO,
                ResultType::VAN_STATIC_TARE,

                ResultType::TRAIN_DYNAMIC,
                ResultType::TRAIN_DYNAMIC_ONE,

                ResultType::AUTO_BRUTTO,
                ResultType::AUTO_TARE,

                ResultType::KANAT,

                ResultType::DP,
                ResultType::DP_SUM,

                ResultType::CARGO_LIST_DYNAMIC,
                ResultType::CARGO_LIST_STATIC,
                ResultType::CARGO_LIST_AUTO,

                ResultType::COMPARE_DYNAMIC,
                ResultType::COMPARE_STATIC,

                ResultType::COEFFS,

                ResultType::IRON,
                ResultType::IRON_CONTROL,

                ResultType::VANLIST_WEIGHS,
                ResultType::VANLIST_LAST_TARE);

            foreach ($resultTypes as $result) {
                $resultType = getResultType($result);
                if (!empty($resultType)) {
                    break;
                }
            }
        }

        if (empty($resultType)) {
            throwBadRequest('ReportType::TYPE_DEFAULT - empty resultType');
        }

        $dtStartDay = getParamGETAsInt(ParamName::DATETIME_START_DAY);
        $dtStartMonth = getParamGETAsInt(ParamName::DATETIME_START_MONTH);
        $dtStartYear = getParamGETAsInt(ParamName::DATETIME_START_YEAR);
        $dtStartHour = getParamGETAsInt(ParamName::DATETIME_START_HOUR);
        $dtStartMinute = getParamGETAsInt(ParamName::DATETIME_START_MINUTES);

        $dtEndDay = getParamGETAsInt(ParamName::DATETIME_END_DAY);
        $dtEndMonth = getParamGETAsInt(ParamName::DATETIME_END_MONTH);
        $dtEndYear = getParamGETAsInt(ParamName::DATETIME_END_YEAR);
        $dtEndHour = getParamGETAsInt(ParamName::DATETIME_END_HOUR);
        $dtEndMinute = getParamGETAsInt(ParamName::DATETIME_END_MINUTES);

        $orderByDesc = !getParamGETAsBool(ParamName::ORDER_BY_DATETIME_ASC, !$orderByDesc);

        $filter->setOrderByDesc($orderByDesc);

        $from20to20 = getParamGETAsBool(ParamName::DATETIME_FROM_20_TO_20, $from20to20);

        $vanList = vanListStringToArray(getParamGETAsString(ParamName::VANLIST));

        break;
    case ReportType::CARGO_TYPES:
        $resultType = getParamGETAsInt(ParamName::RESULT_TYPE);

        switch ($resultType) {
            case ResultType::CARGO_LIST_AUTO:
                $resultType = ResultType::AUTO_BRUTTO;
                break;
            case ResultType::CARGO_LIST_DYNAMIC:
                $resultType = ResultType::VAN_DYNAMIC_BRUTTO;
                break;
            case ResultType::CARGO_LIST_STATIC:
                $resultType = ResultType::VAN_STATIC_BRUTTO;
                break;
            default:
                throwBadRequest('ReportType::CARGO_TYPES - wrong resultType');
        }

        $useBackup = getParamGETAsBool(ParamName::USE_BACKUP, $useBackup);

        $filter
            ->setFull(getParamGETAsBool(ParamName::ALL_FIELDS, false))
            ->setCargoType((getParamGETAsString(ParamName::CARGO_TYPE)));

        $dateTimeStart = getParamGETAsInt(ParamName::DATETIME_START);
        $dateTimeEnd = getParamGETAsInt(ParamName::DATETIME_END);

        break;
    case ReportType::TRAINS:
        $resultType = ResultType::TRAIN_DYNAMIC_ONE;

        $useBackup = getParamGETAsBool(ParamName::USE_BACKUP, $useBackup);

        $filter
            ->setFull(getParamGETAsBool(ParamName::ALL_FIELDS, false))
            ->setTrainNum(getParamGETAsInt(ParamName::TRAIN_NUM))
            ->setTrainUnixTime(getParamGETAsInt(ParamName::TRAIN_UNIX_TIME))
            ->setTrainDateTime(getParamGETAsInt(ParamName::TRAIN_DATETIME))
            ->setShowDeltas(getParamGETAsBool(ParamName::SHOW_DELTAS));

        break;
    default:
        throwBadRequest('wrong reportType');
}

switch ($resultType) {
    case ResultType::COEFFS:
    case ResultType::SENSORS_ZEROS:
    case ResultType::SENSORS_TEMPS:
    case ResultType::SENSORS_STATUS:
        if (!CheckUser::isPowerUser()) {
            throwBadRequest('report for power users', 403);
        }
        break;
    case ResultType::SENSORS_INFO:
        if (!CheckUser::isPowerUser()) {
            throwBadRequest('report for power users', 403);
        }
        $scaleNum = Constants::SCALE_NUM_REPORT_SENSORS_INFO;
        break;
    case ResultType::IRON_CONTROL:
        $scaleNum = Constants::SCALE_NUM_REPORT_IRON_CONTROL;
        break;
    case ResultType::SLAG_CONTROL:
        $scaleNum = Constants::SCALE_NUM_REPORT_SLAG_CONTROL;
        break;
}

if (isResultTypeCompare($resultType)) {
    $filter->setFull(false);
}

$showTotalSums = match ($resultType) {
    ResultType::DP, ResultType::KANAT => getParamGETAsBool(ParamName::SHOW_TOTAL_SUMS),
    default => false
};

echoStartPage();

$mysqli = MySQLConnection::getInstance($useBackup);

$scaleInfo = null;

if ($mysqli) {
    if ($mysqli->connect_errno) {
        $header = S::ERROR_ERROR;
        $resultMessage = connectionError($mysqli);
    } else {
        $scaleInfo = new ScaleInfo($scaleNum);

        $resultMessage = $scaleInfo->query($mysqli);

        if (!$resultMessage) {
            $header = $scaleInfo->getHeader();

            $excelData = S::EXCEL_BOM . $header . S::EXCEL_EOL;

            $title = $scaleInfo->getPlace();

            $navLinks = array();

            $navLinks[] = new HtmlHeaderNavLink('save', 'save', S::NAV_LINK_SAVE, 'saveToExcel()', true);
            $navLinks[] = new HtmlHeaderNavLink('refresh', 'refresh', S::NAV_LINK_UPDATE, 'reloadData()');
            $navLinks[] = new HtmlHeaderNavLink('back', 'arrow_back', S::NAV_LINK_BACK, 'goBack()');

            $menuItems = array();

            $menuItems[] = new HtmlHeaderMenuItem('copyAll', S::MENU_COPY_ALL, 'copyToClipboard("all")');
            $menuItems[] = new HtmlHeaderMenuItem('copyTable', S::MENU_COPY_TABLE, 'copyToClipboard("table")');
            $menuItems[] = new HtmlHeaderMenuItem('copyTableBody', S::MENU_COPY_TABLE_BODY, 'copyToClipboard("tableBody")');

            if ($resultType == ResultType::IRON) {
                $menuItems[] = new HtmlHeaderMenuItem('copyTableBodyIronPrevDay', S::MENU_COPY_TABLE_BODY_IRON_PREV_DAY, 'copyToClipboard("tableBodyIronPrevDay")');
                $menuItems[] = new HtmlHeaderMenuItem('copyTableBodyIronPrev3Day', S::MENU_COPY_TABLE_BODY_IRON_PREV_3_DAY, 'copyToClipboard("tableBodyIronPrev3Day")');
            }

            switch ($reportType) {
                case ReportType::TRAINS:
                case ReportType::CARGO_TYPES:
                    break;
                case ReportType::IRON:
                default:
                    $dateTimeStart = DateTimeBuilder::getInstance()
                        ->setDay($dtStartDay)
                        ->setMonth($dtStartMonth)
                        ->setYear($dtStartYear)
                        ->setHour($dtStartHour)
                        ->setMinute($dtStartMinute)
                        ->buildStartDate();

                    $dateTimeEnd = DateTimeBuilder::getInstance()
                        ->setDay($dtEndDay)
                        ->setMonth($dtEndMonth)
                        ->setYear($dtEndYear)
                        ->setHour($dtEndHour)
                        ->setMinute($dtEndMinute)
                        ->buildEndDate();
            }

            switch ($resultType) {
                case ResultType::DP:
                case ResultType::KANAT:
                    if ($dateTimeStart == null && $dateTimeEnd == null) {
                        $currDate = getdate();
                        $dateTimeStart = DateTimeBuilder::getInstance()
                            ->setDay($currDate["mday"])
                            ->buildStartDate();
                    }
                    break;
                case ResultType::TRAIN_DYNAMIC:
                case ResultType::DP_SUM:
                case ResultType::IRON:
                    if ($dateTimeStart == null && $dateTimeEnd == null) {
                        $dateTimeStart = DateTimeBuilder::getInstance()
                            ->setDay(1)
                            ->buildStartDate();
                    }
                    break;
                case ResultType::IRON_CONTROL:
                case ResultType::SLAG_CONTROL:
                    if ($dateTimeStart == null && $dateTimeEnd == null) {
                        $prevDate = getdate(date_sub(date_create(), new DateInterval('P1D'))->getTimestamp());

                        $dateTimeStart = DateTimeBuilder::getInstance()
                            ->setDay($prevDate["mday"])
                            ->setMonth($prevDate["mon"])
                            ->setHour(6)
                            ->buildStartDate();

                        $currDate = getdate();

                        $dateTimeEnd = DateTimeBuilder::getInstance()
                            ->setDay($currDate["mday"])
                            ->setHour(5)
                            ->setMinute(59)
                            ->buildEndDate();
                    }
                    break;
                case ResultType::VANLIST_WEIGHS:
                case ResultType::VANLIST_LAST_TARE:
                    if ($dateTimeStart == null && $dateTimeEnd == null && count($vanList) == 0) {
                        $dateTimeStart = DateTimeBuilder::getInstance()
                            ->setDay(1)
                            ->buildStartDate();
                    }
                    break;
                case ResultType::SENSORS_ZEROS:
                case ResultType::SENSORS_TEMPS:
                    if ($dateTimeStart == null && $dateTimeEnd == null) {
                        $prevDate = getdate(date_sub(date_create(), new DateInterval('P3D'))->getTimestamp());

                        $dateTimeStart = DateTimeBuilder::getInstance()
                            ->setDay($prevDate["mday"])
                            ->setMonth($prevDate["mon"])
                            ->buildStartDate();
                    }
                    break;
            }

            $subHeader = getResultHeader($resultType);

            if (!empty($subHeader)) {
                $formatDateTimeF = match ($resultType) {
                    ResultType::IRON, ResultType::VANLIST_WEIGHS, ResultType::VANLIST_LAST_TARE => 'formatDate',
                    default => 'formatDateTime',
                };

                if ($resultType == ResultType::TRAIN_DYNAMIC_ONE) {
                    $subHeader = sprintf(S::HEADER_RESULT_PERIOD_DATE, $subHeader,
                        $formatDateTimeF($filter->getTrainDateTime()));
                } else {
                    if ($dateTimeStart && $dateTimeEnd) {
                        $subHeader = sprintf(S::HEADER_RESULT_PERIOD_FROM_TO, $subHeader,
                            $formatDateTimeF($dateTimeStart), $formatDateTimeF($dateTimeEnd));
                    } else if ($dateTimeStart) {
                        $subHeader = sprintf(S::HEADER_RESULT_PERIOD_FROM, $subHeader,
                            $formatDateTimeF($dateTimeStart));
                    } elseif ($dateTimeEnd) {
                        $subHeader = sprintf(S::HEADER_RESULT_PERIOD_TO, $subHeader,
                            $formatDateTimeF($dateTimeEnd));
                    } else {
                        $subHeader = sprintf(S::HEADER_RESULT_PERIOD_ALL, $subHeader);
                    }
                }

                if ($resultType == ResultType::IRON && $from20to20) {
                    $subHeader .= S::HEADER_RESULT_PERIOD_FROM_20_TO_20;
                }

                $excelData .= $subHeader . S::EXCEL_EOL;
            }

            $filter
                ->setScaleNum($scaleNum)
                ->setDateTimeStart($dateTimeStart)
                ->setDateTimeEnd($dateTimeEnd);

            function formatWhereHeader(string $name, ?string $value, string $html): string
            {
                if ($value) {
                    if ($html) {
                        $name = '<span class="color-text--secondary">' . $name . '</span>';
                    }

                    return $name . ": " . $value;
                } else {
                    return "";
                }
            }

            $whereHeader = match ($resultType) {
                ResultType::AUTO_BRUTTO, ResultType::AUTO_TARE,
                ResultType::VAN_DYNAMIC_BRUTTO, ResultType::VAN_DYNAMIC_TARE,
                ResultType::VAN_STATIC_BRUTTO, ResultType::VAN_STATIC_TARE,
                ResultType::COMPARE_DYNAMIC, ResultType::COMPARE_STATIC =>
                formatWhereHeader($scaleInfo->getType() == ScaleType::AUTO ?
                    S::HEADER_RESULT_SEARCH_AUTO_NUMBER : S::HEADER_RESULT_SEARCH_VAN_NUMBER,
                    $filter->getVanNumber(), $newDesign),
                default => "",
            };

            switch ($resultType) {
                case ResultType::TRAIN_DYNAMIC_ONE:
                case ResultType::VAN_DYNAMIC_BRUTTO:
                case ResultType::VAN_STATIC_BRUTTO:
                case ResultType::AUTO_BRUTTO:
                case ResultType::CARGO_LIST_DYNAMIC:
                case ResultType::CARGO_LIST_STATIC:
                case ResultType::CARGO_LIST_AUTO:
                case ResultType::COMPARE_DYNAMIC:
                case ResultType::COMPARE_STATIC:
                    $whereHeader = concatStrings($whereHeader,
                        formatWhereHeader(S::HEADER_RESULT_SEARCH_CARGO_TYPE, $filter->getCargoType(), $newDesign), "; ");

                    $whereHeader = concatStrings($whereHeader,
                        formatWhereHeader(S::HEADER_RESULT_SEARCH_INVOICE_NUM, $filter->getInvoiceNum(), $newDesign), "; ");

                    $whereHeader = concatStrings($whereHeader,
                        formatWhereHeader(S::HEADER_RESULT_SEARCH_INVOICE_SUPPLIER, $filter->getInvoiceSupplier(), $newDesign), "; ");

                    $whereHeader = concatStrings($whereHeader,
                        formatWhereHeader(S::HEADER_RESULT_SEARCH_INVOICE_RECIPIENT, $filter->getInvoiceRecipient(), $newDesign), "; ");
            }

            $whereHeader = concatStrings($whereHeader,
                formatWhereHeader(S::HEADER_RESULT_SEARCH_SCALES, $filter->getScalesFilter(), $newDesign), "; ");

            if ($whereHeader) {
                $whereHeader = S::HEADER_RESULT_SEARCH . S::SPACE . $whereHeader;
                $excelData .= formatExcelData($whereHeader) . S::EXCEL_EOL;
            }
        } else {
            $header = S::ERROR_ERROR;
        }
    }
} else {
    $header = S::ERROR_ERROR;
    $resultMessage = mysqlConnectionFileError();
}

echoHead($newDesign, $title, null, $newDesign ?
    array("/javascript/common.js", "/javascript/result.js") :
    "/javascript/hr.js");

echoStartBody($newDesign, $newDesign ? "showContent()" : null);

(new HtmlHeader($newDesign))
    ->setMainPage(false)
    ->setHeader($header)
    ->setSubHeader($subHeader)
    ->setUseBackup($useBackup)
    ->setNavLinks($navLinks)
    ->setMenuItems($menuItems)
    ->draw();

(new HtmlDrawer($newDesign, $mysqli))
    ->setUseBackup($useBackup)
    ->draw();

echoStartMain($newDesign);

if ($newDesign) {
    echo '<div id="divLoading">' . PHP_EOL;
    echo S::TAB;
    echo '<div class="div-center-outer">' . PHP_EOL;
    echo S::TAB . S::TAB;
    echo '<div class="div-center-middle">' . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB;
    echo '<div class="div-center-inner">' . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB . S::TAB;
    echo '<h1 class="text-align--center color-text--secondary">' . S::HEADER_LOADING . '</h1>' . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB;
    echo '</div>' . PHP_EOL;
    echo S::TAB . S::TAB;
    echo '</div>' . PHP_EOL;
    echo S::TAB;
    echo '</div>' . PHP_EOL;
    echo '</div> <!-- id="divLoading" -->' . PHP_EOL . PHP_EOL;
}

echoStartContent($newDesign);

if (!$resultMessage) {
    if ($whereHeader) {
        echo "<h6 data-header>" . $whereHeader . "</h6>" . PHP_EOL;
    }

    if (isResultTypeCompare($resultType)) {
        $compareHeader = sprintf(S::HEADER_RESULT_SEARCH_COMPARE,
            $filter->isCompareByBrutto() ?
                S::HEADER_RESULT_SEARCH_COMPARE_BY_BRUTTO : S::HEADER_RESULT_SEARCH_COMPARE_BY_NETTO,
            $filter->isCompareForward() ?
                S::HEADER_RESULT_SEARCH_COMPARE_FORWARD : S::HEADER_RESULT_SEARCH_COMPARE_BACKWARD);

        echo "<h6 data-header>" . $compareHeader . "</h6>" . PHP_EOL;

        $excelData .= formatExcelData($compareHeader) . S::EXCEL_EOL;
    } else {
        $compareHeader = null;
    }

    if ($whereHeader || $compareHeader) {
        echo PHP_EOL;
    }

    $queryResult = null;

    try {
        $queryResult = match ($resultType) {
            ResultType::IRON => (new QueryIron())
                ->setDateStart($dateTimeStart)
                ->setDateEnd($dateTimeEnd)
                ->setOrderByDesc($orderByDesc)
                ->setFrom20to20($from20to20),
            ResultType::IRON_CONTROL, ResultType::SLAG_CONTROL => (new QueryControl())
                ->setResultType($resultType)
                ->setDateStart($dateTimeStart)
                ->setDateEnd($dateTimeEnd),
            ResultType::VANLIST_WEIGHS => (new QueryVanListWeighs())
                ->setDateStart($dateTimeStart)
                ->setDateEnd($dateTimeEnd)
                ->setVanList($vanList),
            ResultType::VANLIST_LAST_TARE => (new QueryVanListLastTare())
                ->setDateStart($dateTimeStart)
                ->setDateEnd($dateTimeEnd)
                ->setVanList($vanList),
            ResultType::COEFFS => (new QueryCoeffs())
                ->setScaleNum($scaleNum)
                ->setDateTimeStart($dateTimeStart)
                ->setDateTimeEnd($dateTimeEnd)
                ->setShowDisabled($showDisabled),
            ResultType::SENSORS_ZEROS,
            ResultType::SENSORS_TEMPS,
            ResultType::SENSORS_STATUS => (new QuerySensors())
                ->setScaleNum($scaleNum)
                ->setDateTimeStart($dateTimeStart)
                ->setDateTimeEnd($dateTimeEnd)
                ->setResultType($resultType)
                ->setSensorsMCount($scaleInfo->getSensorsMCount())
                ->setSensorsTCount($scaleInfo->getSensorsTCount())
                ->setShowDisabled($showDisabled),
            ResultType::SENSORS_INFO => (new QuerySensorsInfo())
                ->setShowDisabled($showDisabled),
            default => (new QueryResult())
                ->setScaleType($scaleInfo->getType())
                ->setResultType($resultType)
                ->setFilter($filter),
        };
    } catch (Exception $e) {
        throwBadRequest($e->getMessage());
    }

    $query = $queryResult->getQuery();

    if (Constants::DEBUG_SHOW_QUERY) {
        echo "Query: " . latin1ToUtf8($query) . "<br>" . PHP_EOL;
    }

    $result = $mysqli->query($query);

    $numRows = -1;

    if ($result) {
        $numRows = $result->num_rows;

        if ($numRows > 0) {
            if ($numRows <= Constants::RESULT_MAX_ROWS) {
                $fieldsInfo = getFieldsInfo($result, $newDesign, $filter->isFull(), $scaleInfo, $resultType);

                if ($newDesign) {
                    $tableClass = 'mdl-data-table mdl-shadow--4dp';

                    if ($resultType == ResultType::TRAIN_DYNAMIC || $resultType == ResultType::TRAIN_DYNAMIC_ONE ||
                        $resultType == ResultType::VAN_DYNAMIC_BRUTTO || $resultType == ResultType::VAN_STATIC_BRUTTO ||
                        $resultType == ResultType::AUTO_BRUTTO ||
                        isResultTypeCompare($resultType)
                    ) {
                        $tableClass .= ' width--100-percents';
                    } else {
                        $tableClass .= ' center';
                    }
                } else {
                    $tableClass = 'text-align--center';
                    if (isResultTypeCompare($resultType)) {
                        $tableClass .= ' width--100-percents';
                    }
                }

                echoTableStart($tableClass);

// ------------- Начало заголовка таблицы ------------------------------------------------------------------------------
                echoTableHeadStart();

                if (isResultTypeCompare($resultType)) {
                    echoTableTRStart();

                    if ($scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                        $compareHeader1 = ColumnsStrings::COMPARE_ALL_SCALES;
                        $compareHeader1ColSpan = 10;
                    } else {
                        $compareHeader1 = sprintf(ColumnsStrings::COMPARE_SCALE_NUM, $scaleNum);
                        $compareHeader1ColSpan = 9;
                    }
                    $compareHeader2 = ColumnsStrings::COMPARE_COMPARE_VALUES;
                    $compareHeader2ColSpan = 4;

                    echoTableTH($compareHeader1, 'compare width--70-percents', $compareHeader1ColSpan);
                    echoTableTH($compareHeader2, 'compare', $compareHeader2ColSpan);

                    echoTableTREnd();

                    $excelData .= formatExcelData($compareHeader1);
                    $excelData .= str_repeat(S::EXCEL_SEPARATOR, $compareHeader1ColSpan);

                    $excelData .= formatExcelData($compareHeader2);
                    $excelData .= str_repeat(S::EXCEL_SEPARATOR, $compareHeader2ColSpan);

                    $excelData .= S::EXCEL_EOL;
                }

                echoTableTRStart();
                echoTableTH(ColumnsStrings::COMPARE_NUM);

                $excelData .= formatExcelData(ColumnsStrings::COMPARE_NUM);

                for ($i = 0; $i < $result->field_count; $i++) {
                    switch ($resultType) {
                        case ResultType::COEFFS:
                        case ResultType::SENSORS_ZEROS:
                        case ResultType::SENSORS_TEMPS:
                        case ResultType::SENSORS_STATUS:
                        case ResultType::SENSORS_INFO:
                            switch ($fieldsInfo[$i]->name) {
                                case C::SCALE_NUM:
                                case C::SCALE_PLACE:
                                    continue 3;
                            }
                            break;
                    }

                    if ($fieldsInfo[$i]->visible) {
                        $class = null;

                        if (isResultTypeCompare($resultType)) {
                            if ($fieldsInfo[$i]->name == C::SIDE_DIFFERENCE ||
                                $fieldsInfo[$i]->name == C::CARRIAGE_DIFFERENCE
                            ) {
                                $class = "compare width--15-percents";
                            }
                        }

                        $class = match ($fieldsInfo[$i]->name) {
                            C::SENSOR_M1, C::SENSOR_M2, C::SENSOR_M3, C::SENSOR_M4,
                            C::SENSOR_M5, C::SENSOR_M6, C::SENSOR_M7, C::SENSOR_M8,
                            C::SENSOR_M9, C::SENSOR_M10, C::SENSOR_M11, C::SENSOR_M12,
                            C::SENSOR_M13, C::SENSOR_M14, C::SENSOR_M15, C::SENSOR_M16,
                            C::SENSOR_T1, C::SENSOR_T2, C::SENSOR_T3, C::SENSOR_T4,
                            C::SENSOR_T5, C::SENSOR_T6, C::SENSOR_T7, C::SENSOR_T8 => match ($resultType) {
                                ResultType::SENSORS_ZEROS => "width--sensors-zeros",
                                ResultType::SENSORS_TEMPS => "width--sensors-temps",
                                ResultType::SENSORS_STATUS => "width--sensors-status",
                                ResultType::SENSORS_INFO => match ($fieldsInfo[$i]->name) {
                                    C::SENSOR_M1, C::SENSOR_M2, C::SENSOR_M3, C::SENSOR_M4,
                                    C::SENSOR_M5, C::SENSOR_M6, C::SENSOR_M7, C::SENSOR_M8,
                                    C::SENSOR_M9, C::SENSOR_M10, C::SENSOR_M11, C::SENSOR_M12,
                                    C::SENSOR_M13, C::SENSOR_M14, C::SENSOR_M15, C::SENSOR_M16 => "width--sensors-zeros",
                                    C::SENSOR_T1, C::SENSOR_T2, C::SENSOR_T3, C::SENSOR_T4,
                                    C::SENSOR_T5, C::SENSOR_T6, C::SENSOR_T7, C::SENSOR_T8 => "width--sensors-temps",
                                    default => ""
                                },
                                default => ""
                            },
                            default => null
                        };

                        $cell = columnName($fieldsInfo[$i]->name, $scaleInfo->getType(), $resultType);

                        $title = columnTitle($fieldsInfo[$i]->name);

                        if (Constants::DEBUG_SHOW_QUERY) {
                            if ($title) {
                                $title .= PHP_EOL;
                            }
                            $title .= $fieldsInfo[$i]->name;
                        }

                        echoTableTH($cell, $class, null, $title);

                        $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);
                    }
                }

                if (isResultTypeCompare($resultType)) {
                    $cell = columnName(C::SCALE_NUM, $scaleInfo->getType());
                    echoTableTH($cell);
                    $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);

                    $cell = columnName($filter->isCompareByBrutto() ?
                        C::BRUTTO :
                        C::NETTO,
                        $scaleInfo->getType());
                    echoTableTH($cell);
                    $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);

                    $cell = columnName(C::DATETIME, $scaleInfo->getType());
                    echoTableTH($cell);
                    $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);

                    $cell = columnName(C::COMPARE, $scaleInfo->getType());
                    echoTableTH($cell, null, null, columnTitle(C::COMPARE));
                    $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);
                }

                $excelData .= S::EXCEL_EOL;

                echoTableTREnd();
                echoTableHeadEnd();
// ------------- Конец заголовка таблицы -------------------------------------------------------------------------------

                echoTableBodyStart();

                $rowIndex = 0;
                $numColor = false;

                $hrefBuilder = Builder::getInstance()
                    ->setUrl("result.php")
                    ->setParam(ParamName::NEW_DESIGN, $newDesign)
                    ->setParam(ParamName::ALL_FIELDS, $filter->isFull())
                    ->setParam($filter->isShowDeltas() ? ParamName::SHOW_DELTAS : null, true)
                    ->setParam($useBackup ? ParamName::USE_BACKUP : null, true);

                if ($resultType == ResultType::TRAIN_DYNAMIC) {
                    $hrefBuilder->setParam(ParamName::REPORT_TYPE, ReportType::TRAINS);
                } elseif (isResultTypeCargoList($resultType)) {
                    $hrefBuilder
                        ->setParam(ParamName::REPORT_TYPE, ReportType::CARGO_TYPES)
                        ->setParam(ParamName::RESULT_TYPE, $resultType)
                        ->setParam(ParamName::SCALE_NUM, $scaleNum);

                    if ($dateTimeStart) {
                        $hrefBuilder->setParam(ParamName::DATETIME_START, $dateTimeStart);
                    }
                    if ($dateTimeEnd) {
                        $hrefBuilder->setParam(ParamName::DATETIME_END, $dateTimeEnd);
                    }
                }

                $href = null;

                if (isResultTypeCompare($resultType)) {
                    $queryCompare = new QueryCompare();

                    $queryCompare
                        ->setCompareForward($filter->isCompareForward() ?: false)
                        ->setCompareByBrutto($filter->isCompareByBrutto() ?: false);

                    if ($scaleNum != Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                        $queryCompare->setScaleNum($scaleNum);
                    }
                } else {
                    $queryCompare = null;
                }

                $scaleNumGroup = 0;

// ------------- Перебор строк -----------------------------------------------------------------------------------------
                while ($row = $result->fetch_array()) {
                    switch ($resultType) {
                        case ResultType::TRAIN_DYNAMIC:
                            $href = $hrefBuilder
                                ->setParam(ParamName::SCALE_NUM,
                                    $scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES ?
                                        $row[C::SCALE_NUM] :
                                        $scaleNum)
                                ->setParam(ParamName::TRAIN_NUM, $row[C::TRAIN_NUM])
                                ->setParam(ParamName::TRAIN_UNIX_TIME, $row[C::UNIX_TIME])
                                ->setParam(ParamName::TRAIN_DATETIME, strtotime($row[C::DATETIME]))
                                ->build();
                            break;
                        case ResultType::CARGO_LIST_DYNAMIC:
                        case ResultType::CARGO_LIST_STATIC:
                        case ResultType::CARGO_LIST_AUTO:
                            $href = $hrefBuilder
                                ->setParam(ParamName::CARGO_TYPE, latin1ToUtf8($row[C::CARGO_TYPE]))
                                ->build();
                            break;
                        case ResultType::COEFFS:
                        case ResultType::SENSORS_ZEROS:
                        case ResultType::SENSORS_TEMPS:
                        case ResultType::SENSORS_STATUS:
                        case ResultType::SENSORS_INFO:
                            if (($scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES ||
                                    $scaleNum == Constants::SCALE_NUM_REPORT_SENSORS_INFO)
                                && $scaleNumGroup != $row[C::SCALE_NUM]) {
                                $scaleNumGroup = $row[C::SCALE_NUM];

                                $rowIndex = 0;
                                $numColor = false;

                                $field = sprintf(Strings::SCALE_INFO_HEADER, latin1ToUtf8($row[C::SCALE_PLACE]), $row[C::SCALE_NUM]);

                                echoTableTRStart();
                                echoTableTD($field, 'row-sub_header', null, $result->field_count - 1);
                                echoTableTREnd();

                                $excelData .= formatExcelData($field);
                                $excelData .= S::EXCEL_EOL;
                            }
                            break;
                    }

                    $rowColorClass = getRowColorClass($numColor);

                    if ($newDesign && $href) {
                        $class = "rowclick $rowColorClass";
                        $onClick = "location.href=\"$href\"";
                    } else {
                        $class = $rowColorClass;
                        $onClick = null;
                    }

                    echoTableTRStart($class, $onClick);

                    $rowIndex++;

                    $field = $rowIndex;

                    $excelData .= formatExcelData($field);

                    echoTableTD($field, null, $newDesign ? null : $href);

// ------------- Перебор полей -----------------------------------------------------------------------------------------
                    for ($fieldNum = 0; $fieldNum < $result->field_count; $fieldNum++) {
                        if (!$fieldsInfo[$fieldNum]->visible) {
                            continue;
                        }

                        switch ($resultType) {
                            case ResultType::COEFFS:
                            case ResultType::SENSORS_ZEROS:
                            case ResultType::SENSORS_TEMPS:
                            case ResultType::SENSORS_STATUS:
                            case ResultType::SENSORS_INFO:
                                if ($scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES ||
                                    $scaleNum == Constants::SCALE_NUM_REPORT_SENSORS_INFO) {
                                    switch ($fieldsInfo[$fieldNum]->name) {
                                        case C::SCALE_NUM:
                                        case C::SCALE_PLACE:
                                            continue 3;
                                    }
                                }
                                break;
                        }

                        $field = latin1ToUtf8($row[$fieldNum]);

                        $field = match ($resultType) {
                            ResultType::SENSORS_STATUS => match ($fieldsInfo[$fieldNum]->name) {
                                C::SENSOR_M1, C::SENSOR_M2, C::SENSOR_M3, C::SENSOR_M4,
                                C::SENSOR_M5, C::SENSOR_M6, C::SENSOR_M7, C::SENSOR_M8,
                                C::SENSOR_M9, C::SENSOR_M10, C::SENSOR_M11, C::SENSOR_M12,
                                C::SENSOR_M13, C::SENSOR_M14, C::SENSOR_M15, C::SENSOR_M16,
                                C::SENSOR_T1, C::SENSOR_T2, C::SENSOR_T3, C::SENSOR_T4,
                                C::SENSOR_T5, C::SENSOR_T6, C::SENSOR_T7, C::SENSOR_T8
                                => is_null($row[$fieldNum]) ? "" : ($row[$fieldNum] > 0 ? Strings::TEXT_ON : Strings::TEXT_OFF),
                                default => formatFieldValue($fieldsInfo[$fieldNum]->name, $field, $filter->isFull()),
                            },
                            ResultType::SENSORS_INFO => match ($fieldsInfo[$fieldNum]->name) {
                                C::SENSOR_M1, C::SENSOR_M2, C::SENSOR_M3, C::SENSOR_M4,
                                C::SENSOR_M5, C::SENSOR_M6, C::SENSOR_M7, C::SENSOR_M8,
                                C::SENSOR_M9, C::SENSOR_M10, C::SENSOR_M11, C::SENSOR_M12,
                                C::SENSOR_M13, C::SENSOR_M14, C::SENSOR_M15, C::SENSOR_M16,
                                C::SENSOR_T1, C::SENSOR_T2, C::SENSOR_T3, C::SENSOR_T4,
                                C::SENSOR_T5, C::SENSOR_T6, C::SENSOR_T7, C::SENSOR_T8
                                => $rowIndex == 1 ?
                                    (is_null($row[$fieldNum]) ? "" : ($row[$fieldNum] > 0 ? Strings::TEXT_ON : Strings::TEXT_OFF)) :
                                    ($rowIndex == 2 ? formatFieldValue($fieldsInfo[$fieldNum]->name, $field, $filter->isFull()) :
                                        ($row[$fieldNum] == Aliases::NU ? "" : formatFieldValue($fieldsInfo[$fieldNum]->name, $field, $filter->isFull()))),
                                default => formatFieldValue($fieldsInfo[$fieldNum]->name, $field, $filter->isFull()),
                            },
                            default => formatFieldValue($fieldsInfo[$fieldNum]->name, $field, $filter->isFull()),
                        };

                        $title = match ($resultType) {
                            ResultType::SENSORS_ZEROS, ResultType::SENSORS_TEMPS, ResultType::SENSORS_STATUS,
                            ResultType::SENSORS_INFO =>
                            match ($fieldsInfo[$fieldNum]->name) {
                                C::SENSOR_M1, C::SENSOR_M2, C::SENSOR_M3, C::SENSOR_M4,
                                C::SENSOR_M5, C::SENSOR_M6, C::SENSOR_M7, C::SENSOR_M8,
                                C::SENSOR_M9, C::SENSOR_M10, C::SENSOR_M11, C::SENSOR_M12,
                                C::SENSOR_M13, C::SENSOR_M14, C::SENSOR_M15, C::SENSOR_M16,
                                C::SENSOR_T1, C::SENSOR_T2, C::SENSOR_T3, C::SENSOR_T4,
                                C::SENSOR_T5, C::SENSOR_T6, C::SENSOR_T7, C::SENSOR_T8
                                => empty($field) ? null : columnName($fieldsInfo[$fieldNum]->name),
                                default => null
                            },
                            default => null
                        };

                        $excelData .= S::EXCEL_SEPARATOR . formatExcelData($field);

                        if (($fieldsInfo[$fieldNum]->name == C::BRUTTO) &&
                            isResultTypeCompare($resultType)
                        ) {
                            $field = "<b>" . $field . "</b>";
                        }

                        $class = $fieldsInfo[$fieldNum]->leftAlign ?
                            ($newDesign ? 'mdl-data-table__cell--non-numeric' : 'text-align--left') :
                            null;

                        if ($showTotalSums) {
                            if ($fieldsInfo[$fieldNum]->name == C::BRUTTO) $totalSumBrutto += $row[C::BRUTTO];
                            if ($fieldsInfo[$fieldNum]->name == C::TARE) $totalSumTare += $row[C::TARE];
                            if ($fieldsInfo[$fieldNum]->name == C::NETTO) $totalSumNetto += $row[C::NETTO];
                        }

                        $cellColor = null;

                        switch ($resultType) {
                            case ResultType::IRON_CONTROL:
                            case ResultType::SLAG_CONTROL:
                                switch ($fieldsInfo[$fieldNum]->name) {
                                    case C::IRON_CONTROL_DIFF_DYN_STA:
                                        $value = $row[C::IRON_CONTROL_DIFF_DYN_STA];

                                        if ($value != "") {
                                            $ironControlTotalCount++;

                                            $ironControlTotalSum += $value;
                                        }

                                        $cellColor = getCellWarningColor($value,
                                            Thresholds::IRON_CONTROL_DIFF_DYN_STA_WARNING_YELLOW,
                                            Thresholds::IRON_CONTROL_DIFF_DYN_STA_WARNING_RED);

                                        break;
                                    case C::IRON_CONTROL_DIFF_SIDE:
                                        $cellColor = getCellWarningColor($row[C::IRON_CONTROL_DIFF_SIDE],
                                            Thresholds::IRON_CONTROL_DIFF_SIDE_WARNING_YELLOW,
                                            Thresholds::IRON_CONTROL_DIFF_SIDE_WARNING_RED);
                                        break;
                                    case C::IRON_CONTROL_DIFF_CARRIAGE:
                                        $cellColor = getCellWarningColor($row[C::IRON_CONTROL_DIFF_CARRIAGE],
                                            Thresholds::IRON_CONTROL_DIFF_CARRIAGE_WARNING_YELLOW,
                                            Thresholds::IRON_CONTROL_DIFF_CARRIAGE_WARNING_RED);
                                        break;

                                    case C::SLAG_CONTROL_DIFF_DYN_STA:
                                        $value = $row[C::SLAG_CONTROL_DIFF_DYN_STA];

                                        if ($value != "") {
                                            $ironControlTotalCount++;

                                            $ironControlTotalSum += $value;
                                        }

                                        $cellColor = getCellWarningColor($value,
                                            Thresholds::SLAG_CONTROL_DIFF_DYN_STA_WARNING_YELLOW,
                                            Thresholds::SLAG_CONTROL_DIFF_DYN_STA_WARNING_RED);

                                        break;
                                    case C::SLAG_CONTROL_DIFF_SIDE:
                                        $cellColor = getCellWarningColor($row[C::SLAG_CONTROL_DIFF_SIDE],
                                            Thresholds::SLAG_CONTROL_DIFF_SIDE_WARNING_YELLOW,
                                            Thresholds::SLAG_CONTROL_DIFF_SIDE_WARNING_RED);
                                        break;
                                    case C::SLAG_CONTROL_DIFF_CARRIAGE:
                                        $cellColor = getCellWarningColor($row[C::SLAG_CONTROL_DIFF_CARRIAGE],
                                            Thresholds::SLAG_CONTROL_DIFF_CARRIAGE_WARNING_YELLOW,
                                            Thresholds::SLAG_CONTROL_DIFF_CARRIAGE_WARNING_RED);
                                        break;
                                }
                                break;
                            case ResultType::SENSORS_STATUS:
                            case ResultType::SENSORS_INFO:
                                switch ($fieldsInfo[$fieldNum]->name) {
                                    case C::DATETIME_SENSORS_INFO:
                                        if ($rowIndex == 2) {
                                            $diff = (new DateTime())->diff(
                                                (new DateTime())->setTimestamp(
                                                    mysqlDateTimeToUnixTime($row[C::DATETIME_SENSORS_INFO])), true);

                                            $cellColor = getCellWarningColor($diff->days * 24 + $diff->h,
                                                Thresholds::SENSORS_INFO_DATETIME_CURRENT_WARNING_YELLOW,
                                                Thresholds::SENSORS_INFO_DATETIME_CURRENT_WARNING_RED);
                                        }
                                        break;
                                    default:
                                        $cellColor = match ($field) {
                                            Strings::TEXT_ON => 'color--sensors-status-on',
                                            Strings::TEXT_OFF => 'color--sensors-status-off',
                                            default => null
                                        };
                                }
                                break;
                        }

                        $class = concatStrings($class, $cellColor, Strings::SPACE);

                        $showHref = false;
                        if (!$newDesign && $href) {
                            if ($resultType == ResultType::TRAIN_DYNAMIC) {
                                $showHref =
                                    $fieldsInfo[$fieldNum]->name == C::SCALE_NUM ||
                                    $fieldsInfo[$fieldNum]->name == C::DATETIME;
                            } elseif (isResultTypeCargoList($resultType)) {
                                $showHref = $fieldsInfo[$fieldNum]->name == C::CARGO_TYPE;
                            }
                        }

                        echoTableTD($field, $class, $showHref ? $href : null, null, $title);
                    }

                    if (isResultTypeCompare($resultType)) {
                        if (!empty($row[C::VAN_NUMBER])) {
                            if ($scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                                $queryCompare->setScaleNum($row[C::SCALE_NUM]);
                            }
                            $queryCompare
                                ->setVanNumber($row[C::VAN_NUMBER])
                                ->setDateTime(mysqlDateTimeToUnixTime($row[C::DATETIME]));

                            $queryCompareStr = $queryCompare->getQuery();

                            if (Constants::DEBUG_SHOW_QUERY) {
                                echo "Query: " . latin1ToUtf8($queryCompareStr) . "<br>" . PHP_EOL;
                            }

                            $resultCompare = $mysqli->query($queryCompareStr);

                            if ($resultCompare) {
                                if ($resultCompare->num_rows > 0) {
                                    $fieldsCompareInfo = array();

                                    $fieldsCompareInfo = getFieldsInfo($resultCompare, $newDesign,
                                        false, $scaleInfo, $resultType);

                                    $rowCompare = $resultCompare->fetch_array();

                                    for ($i = 0; $i < $resultCompare->field_count; $i++) {
                                        $fieldCompare = formatFieldValue($fieldsCompareInfo[$i]->name,
                                            $rowCompare[$i], $filter->isFull());

                                        if ($fieldsCompareInfo[$i]->name == C::BRUTTO) {
                                            $fieldCompare = "<b>" . $fieldCompare . "</b>";
                                        }

                                        $excelData .= S::EXCEL_SEPARATOR . formatExcelData($fieldCompare);

                                        $class = $fieldsInfo[$i]->leftAlign ?
                                            ($newDesign ?
                                                'mdl-data-table__cell--non-numeric' :
                                                'text-align--left') :
                                            null;

                                        echoTableTD($fieldCompare, $class);
                                    }

                                    $compareColumn = $filter->isCompareByBrutto() ?
                                        C::BRUTTO :
                                        C::NETTO;

                                    $value = $row[$compareColumn];
                                    $valueCompare = $rowCompare[$compareColumn];

                                    $fieldCompare = $value - $valueCompare;

                                    if ($valueCompare == 0.0) {
                                        if (abs($value) < 1) {
                                            $class = null;
                                        } else {
                                            $class = 'color--gray';
                                        }
                                    } else {
                                        $class = getCellWarningColor($fieldCompare,
                                            Thresholds::COMPARE_VALUE_WARNING_YELLOW, Thresholds::COMPARE_VALUE_WARNING_RED);
                                    }

                                    $fieldCompare = formatFieldValue(C::COMPARE, $fieldCompare, $filter->isFull());

                                    echoTableTD($fieldCompare, $class);

                                    $excelData .= S::EXCEL_SEPARATOR . formatExcelData($fieldCompare);
                                } else {
                                    for ($i = 0; $i < 4; $i++) {
                                        echoTableTD("");
                                        $excelData .= S::EXCEL_SEPARATOR;
                                    }
                                }
                            } else {
                                $error = queryError($mysqli);

                                echoTableTD($error->getError(), $class);
                                echoTableTD($error->getErrorDetails(), $class);
                                echoTableTD("");
                                echoTableTD("");
                            }
                        } else {
                            for ($i = 0; $i < 4; $i++) {
                                echoTableTD("");
                                $excelData .= S::EXCEL_SEPARATOR;
                            }
                        }
                    }

                    echoTableTREnd();

                    $excelData .= S::EXCEL_EOL;

                    $numColor = !$numColor;
                } // while

// ----------------- Итого ---------------------------------------------------------------------------------------------

                if ($showTotalSums) {
                    $colSpanTotal = match ($resultType) {
                        ResultType::DP => $filter->isFull() ? 9 : 4,
                        ResultType::KANAT => $filter->isFull() ? 4 : 2,
                        default => throw new InvalidArgumentException("Unhandled resultType for sums ($resultType)"),
                    };
                    $colSpanTotalEnd = match ($resultType) {
                        ResultType::DP => $filter->isFull() ? 2 : 0,
                        ResultType::KANAT => 0,
                        default => throw new InvalidArgumentException("Unhandled resultType for sums ($resultType)"),
                    };

                    echoTableTRStart(getRowColorClass($numColor));

                    echoTableTD("<b>" . S::TEXT_TOTAL . "<b>", $newDesign ? 'mdl-data-table__cell--right' : 'text-align--right', null, $colSpanTotal);

                    $excelData .= formatExcelData(S::TEXT_TOTAL);
                    $excelData .= str_repeat(S::EXCEL_SEPARATOR, $colSpanTotal - 1);

                    switch ($resultType) {
                        case ResultType::DP:
                            $field = formatFieldValue(C::NETTO, $totalSumNetto, $filter->isFull());
                            echoTableTD($field);
                            $excelData .= S::EXCEL_SEPARATOR . formatExcelData($field);
                            break;
                        case ResultType::KANAT:
                            $field = formatFieldValue(C::BRUTTO, $totalSumBrutto, $filter->isFull());
                            echoTableTD($field);
                            $excelData .= S::EXCEL_SEPARATOR . formatExcelData($field);
                            $field = formatFieldValue(C::TARE, $totalSumTare, $filter->isFull());
                            echoTableTD($field);
                            $excelData .= S::EXCEL_SEPARATOR . formatExcelData($field);
                            $field = formatFieldValue(C::NETTO, $totalSumNetto, $filter->isFull());
                            echoTableTD($field);
                            $excelData .= S::EXCEL_SEPARATOR . formatExcelData($field);
                            break;
                        default:
                            throw new InvalidArgumentException("Unhandled resultType for sums ($resultType)");
                    }

                    if ($colSpanTotalEnd > 0) {
                        echoTableTD("", null, null, $colSpanTotalEnd);
                    }

                    echoTableTREnd();

                    $excelData .= S::EXCEL_EOL;
                }

// ----------------- Контрольная провеска чугуна|шлака -----------------------------------------------------------------
                if ($resultType == ResultType::IRON_CONTROL || $resultType == ResultType::SLAG_CONTROL) {
// ---------------------------------------------------------------------------------------------------------------------
                    $colSpanTotal = 8;
                    $colSpanTotalValue = 5;

                    echoTableTRStart(getRowColorClass($numColor));

                    echoTableTD("<b>" . S::TEXT_TOTAL . "<b>", $newDesign ? 'mdl-data-table__cell--right' : 'text-align--right', null, $colSpanTotal);
                    echoTableTD("");
                    echoTableTD("", null, null, $colSpanTotalValue);

                    echoTableTREnd();

// ---------------------------------------------------------------------------------------------------------------------
                    $numColor = !$numColor;

                    echoTableTRStart(getRowColorClass($numColor));

                    echoTableTD(S::TEXT_AVG, $newDesign ? 'mdl-data-table__cell--right' : 'text-align--right', null, $colSpanTotal,
                        columnTitle(C::AVG));

                    if ($ironControlTotalCount > 0) {
                        $ironControlTotalAvg = $ironControlTotalSum / $ironControlTotalCount;
                    }

                    switch ($resultType) {
                        case ResultType::IRON_CONTROL:
                            $field = formatFieldValue(C::IRON_CONTROL_DIFF_DYN_STA, $ironControlTotalAvg . "", $filter->isFull());

                            $class = getCellWarningColor($ironControlTotalAvg,
                                Thresholds::IRON_CONTROL_AVG_VALUE_WARNING_YELLOW, Thresholds::IRON_CONTROL_AVG_VALUE_WARNING_RED);
                            break;
                        case ResultType::SLAG_CONTROL:
                            $field = formatFieldValue(C::SLAG_CONTROL_DIFF_DYN_STA, $ironControlTotalAvg . "", $filter->isFull());

                            $class = getCellWarningColor($ironControlTotalAvg,
                                Thresholds::SLAG_CONTROL_AVG_VALUE_WARNING_YELLOW, Thresholds::SLAG_CONTROL_AVG_VALUE_WARNING_RED);
                            break;
                        default:
                            $field = 'ERROR';
                            $class = null;
                    }

                    echoTableTD($field, $class);

                    echoTableTD("", null, null, $colSpanTotalValue);

                    echoTableTREnd();

// ---------------------------------------------------------------------------------------------------------------------
                    $numColor = !$numColor;

                    echoTableTRStart(getRowColorClass($numColor));

                    echoTableTD(S::TEXT_SUM, $newDesign ? 'mdl-data-table__cell--right' : 'text-align--right', null, $colSpanTotal,
                        columnTitle(C::SUM));

                    switch ($resultType) {
                        case ResultType::IRON_CONTROL:
                            $field = formatFieldValue(C::IRON_CONTROL_DIFF_DYN_STA, $ironControlTotalSum . "", $filter->isFull());

                            $class = getCellWarningColor($ironControlTotalSum,
                                Thresholds::IRON_CONTROL_SUM_VALUE_WARNING_YELLOW, Thresholds::IRON_CONTROL_SUM_VALUE_WARNING_RED);
                            break;
                        case ResultType::SLAG_CONTROL:
                            $field = formatFieldValue(C::SLAG_CONTROL_DIFF_DYN_STA, $ironControlTotalSum . "", $filter->isFull());

                            $class = getCellWarningColor($ironControlTotalSum,
                                Thresholds::SLAG_CONTROL_SUM_VALUE_WARNING_YELLOW, Thresholds::SLAG_CONTROL_SUM_VALUE_WARNING_RED);
                            break;
                        default:
                            $field = 'ERROR';
                            $class = null;
                    }

                    echoTableTD($field, $class);

                    echoTableTD("", null, null, $colSpanTotalValue);

                    echoTableTREnd();
                }
// ----------------- Контрольная провеска чугуна|шлака -----------------------------------------------------------------

                echoTableBodyEnd();
                echoTableEnd();
// ------------- Конец таблицы -----------------------------------------------------------------------------------------

                echoFormStart('formExcel', 'excel.php', null, null, false, true);

                $fileName = match ($scaleNum) {
                    Constants::SCALE_NUM_ALL_TRAIN_SCALES => "AT",
                    Constants::SCALE_NUM_REPORT_IRON => "IR",
                    Constants::SCALE_NUM_REPORT_IRON_CONTROL => "IC",
                    Constants::SCALE_NUM_REPORT_VANLIST => "VL",
                    Constants::SCALE_NUM_REPORT_SENSORS_INFO => "SI",
                    default => "SN-" . $scaleNum,
                };
                $fileName .= "_" . date("Y.m.d_H-i-s") . ".csv";
                echoHidden(ParamName::EXCEL_FILENAME, $fileName);

                $rawLength = strlen($excelData);

                $zipTime = microtime(true);

                $excelData = base64_encode(gzdeflate($excelData));

                $zipLength = strlen($excelData);

                $zipTime = (microtime(true) - $zipTime);

                echoHidden(ParamName::EXCEL_DATA, $excelData);

                if (!$newDesign) {
                    echo S::TAB;
                    $buttonClass = "input-button submit position--top-right";
                    $name = "submit_excel";
                    $text = S::NAV_LINK_SAVE_OLD;
                    echo "<input type='submit' class='$buttonClass' name='$name' value='$text'>";
                }

                echo PHP_EOL . S::TAB;
                echo "<!-- data: raw == $rawLength bytes, gz+base64 == $zipLength bytes" .
                    ", compression == " . number_format($zipLength / $rawLength * 100, 1) . "%" .
                    ", execution time == " . number_format($zipTime, 5) . " sec -->" . PHP_EOL;

                echoFormEnd();

                if ($newDesign) {
                    echo '<script type="text/javascript">numRows = ' . $numRows . ';</script>' . PHP_EOL;
                }
            } else {
                $resultMessage = new ResultMessage(S::ERROR_RESULT_MAX_ROWS, sprintf(S::ERROR_RESULT_MAX_ROWS_DETAILS, $numRows));
            }
        } else {
            $resultMessage = new ResultMessage(S::TEXT_ZERO_RESULT);
        }
    } else {
        $resultMessage = queryError($mysqli);
    }
}

if ($resultMessage) {
    if ($newDesign) {
        echo '<script type="text/javascript">numRows = -1;</script>' . PHP_EOL;
    }

    echoErrorPage($resultMessage->getError(), $resultMessage->getErrorDetails());
}

echoEndContent();

echoEndMain($newDesign);

echoEndBody($newDesign, $newDesign ? null : "updateHRWidthOnEndBody();");

echoEndPage();

echo PHP_EOL . PHP_EOL;
echo "<!-- execution time == " . number_format(microtime(true) - $timeStart, 5) . " sec -->" . PHP_EOL;
ob_end_flush();