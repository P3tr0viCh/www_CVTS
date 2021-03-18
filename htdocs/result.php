<?php
const DEBUG_SHOW_ERROR = false;
const DEBUG_SHOW_QUERY = false;

$timeStart = microtime(true);

require_once "include/Constants.php";

require_once "include/MySQLConnection.php";

require_once "include/QueryResult.php";
require_once "include/QueryIron.php";
require_once "include/QueryIronControl.php";
require_once "include/QueryVanListWeighs.php";
require_once "include/QueryVanListLastTare.php";
require_once "include/QueryCompare.php";

require_once "include/Strings.php";
require_once "include/ColumnsStrings.php";
require_once "include/ColumnsTitleStrings.php";

require_once "include/Functions.php";
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

use HrefBuilder\Builder;
use JetBrains\PhpStorm\Pure;
use Strings as S;

use database\Columns as C;

/**
 * @param null|string $error
 */
function throwBadRequest($error = null)
{
    if (DEBUG_SHOW_ERROR) {
        if (empty($error)) $error = "UNKNOWN";
        echo '<b><span class="color-text--error">' . 'ERROR: ' . $error . '</span></b>';
        return;
    }

    header("Location: error.php?412");
    exit();
}

if (!isset($_GET[ParamName::SCALE_NUM])) {
    if (!isset($_GET[ParamName::REPORT_TYPE])) {
        if (!isset($_GET[ParamName::RESULT_TYPE])) {
            throwBadRequest('empty SCALE_NUM&&REPORT_TYPE&&RESULT_TYPE');
        }
    }
}

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

$orderByDesc = false;
$from20to20 = false;

$vanList = null;

$filter = new ResultFilter();

switch ($reportType) {
    case ReportType::TYPE_DEFAULT:
        $scaleNum = getParamGETAsInt(ParamName::SCALE_NUM);
        $scaleNum = $scaleNum == null ? Constants::SCALE_NUM_ALL_TRAIN_SCALES : (int)$scaleNum;

        $useBackup = getParamGETAsBool(ParamName::USE_BACKUP, $useBackup);

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

        $resultType = getParamGETAsString(ParamName::RESULT_TYPE);

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

        $orderByDesc = getParamGETAsBool(ParamName::ORDER_BY_DESC, $orderByDesc);

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

        $scaleNum = getParamGETAsInt(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES);

        $useBackup = getParamGETAsBool(ParamName::USE_BACKUP, $useBackup);

        $filter
            ->setFull(getParamGETAsBool(ParamName::ALL_FIELDS, false))
            ->setCargoType((getParamGETAsString(ParamName::CARGO_TYPE)));

        $dateTimeStart = getParamGETAsInt(ParamName::DATETIME_START);
        $dateTimeEnd = getParamGETAsInt(ParamName::DATETIME_END);

        break;
    case ReportType::TRAINS:
        $resultType = ResultType::TRAIN_DYNAMIC_ONE;

        $scaleNum = getParamGETAsInt(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES);

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

if ($resultType == ResultType::IRON_CONTROL) {
    $scaleNum = Constants::SCALE_NUM_REPORT_IRON_CONTROL;
}

if (isResultTypeCompare($resultType)) {
    $filter->setFull(false);
}

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

            $dateTimeBuilder = DateTimeBuilder::getInstance();

            switch ($reportType) {
                case ReportType::TRAINS:
                case ReportType::CARGO_TYPES:
                    break;
                case ReportType::IRON:
                default:

                    $dateTimeStart = $dateTimeBuilder
                        ->setDay($dtStartDay)
                        ->setMonth($dtStartMonth)
                        ->setYear($dtStartYear)
                        ->setHour($dtStartHour)
                        ->setMinute($dtStartMinute)
                        ->buildStartDate();

                    $dateTimeEnd = $dateTimeBuilder
                        ->setDay($dtEndDay)
                        ->setMonth($dtEndMonth)
                        ->setYear($dtEndYear)
                        ->setHour($dtEndHour)
                        ->setMinute($dtEndMinute)
                        ->buildEndDate();
            }

            switch ($resultType) {
                case ResultType::TRAIN_DYNAMIC:
                case ResultType::IRON:
                    if ($dateTimeStart == null && $dateTimeEnd == null) {
                        $dateTimeStart = $dateTimeBuilder
                            ->setDay(1)
                            ->buildStartDate();
                    }
                    break;
                case ResultType::IRON_CONTROL:
                    if ($dateTimeStart == null && $dateTimeEnd == null) {
                        $prevDate = getdate(date_sub(new DateTime(), new DateInterval('P1D'))->getTimestamp());

                        $dateTimeStart = $dateTimeBuilder
                            ->setDay($prevDate["mday"])
                            ->setHour(6)
                            ->buildStartDate();

                        $currDate = getdate();

                        $dateTimeEnd = $dateTimeBuilder
                            ->setDay($currDate["mday"])
                            ->setHour(5)
                            ->setMinute(59)
                            ->buildEndDate();
                    }
                    break;

                case ResultType::VANLIST_WEIGHS:
                case ResultType::VANLIST_LAST_TARE:
                    if ($dateTimeStart == null && $dateTimeEnd == null && count($vanList) == 0) {
                        $dateTimeStart = $dateTimeBuilder
                            ->setDay(1)
                            ->buildStartDate();
                    }
                    break;
            }

            $subHeader = getResultHeader($resultType);

            if (empty($subHeader)) {
                throwBadRequest('empty subHeader');
            }

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

            if ($resultType == ResultType::TRAIN_DYNAMIC_ONE ||
                $resultType == ResultType::VAN_DYNAMIC_BRUTTO ||
                $resultType == ResultType::VAN_STATIC_BRUTTO ||
                $resultType == ResultType::AUTO_BRUTTO ||
                isResultTypeCargoList($resultType) ||
                isResultTypeCompare($resultType)
            ) {
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
        /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
        switch ($resultType) {
            case ResultType::IRON:
                $queryResult = (new QueryIron())
                    ->setDateStart($dateTimeStart)
                    ->setDateEnd($dateTimeEnd)
                    ->setOrderByDesc($orderByDesc)
                    ->setFrom20to20($from20to20);
                break;
            case ResultType::IRON_CONTROL:
                $queryResult = (new QueryIronControl())
                    ->setDateStart($dateTimeStart)
                    ->setDateEnd($dateTimeEnd);
                break;

            case ResultType::VANLIST_WEIGHS:
                $queryResult = (new QueryVanListWeighs())
                    ->setDateStart($dateTimeStart)
                    ->setDateEnd($dateTimeEnd)
                    ->setVanList($vanList);
                break;
            case ResultType::VANLIST_LAST_TARE:
                $queryResult = (new QueryVanListLastTare())
                    ->setDateStart($dateTimeStart)
                    ->setDateEnd($dateTimeEnd)
                    ->setVanList($vanList);
                break;
            default:
                $queryResult = (new QueryResult())
                    ->setScaleType($scaleInfo->getType())
                    ->setResultType($resultType)
                    ->setFilter($filter);
        }
    } catch (Exception $e) {
        throwBadRequest($e->getMessage());
    }

    $query = $queryResult->getQuery();

    if (DEBUG_SHOW_QUERY) {
        echo "Query: " . latin1ToUtf8($query) . "<br>" . PHP_EOL;
    }

    $result = $mysqli->query($query);

    $numRows = -1;

    if ($result) {
        $numRows = $result->num_rows;

        if ($numRows > 0) {
            if ($numRows <= Constants::RESULT_MAX_ROWS) {
                /** @var FieldInfo[] $fieldsInfo */
                $fieldsInfo = array();

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
                echoTableHeadStart();

                if (isResultTypeCompare($resultType)) {
                    echoTableTRStart();

                    if ($scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                        $compareHeader1 = ColumnsStrings::COMPARE_ALL_SCALES;
                        $compareHeader1ColSpan = 10;
                        $compareHeader2 = ColumnsStrings::COMPARE_COMPARE_VALUES;
                        $compareHeader2ColSpan = 4;
                    } else {
                        $compareHeader1 = sprintf(ColumnsStrings::COMPARE_SCALE_NUM, $scaleNum);
                        $compareHeader1ColSpan = 9;
                        $compareHeader2 = ColumnsStrings::COMPARE_OTHER_SCALES;
                        $compareHeader2ColSpan = 4;
                    }

                    echoTableTH($compareHeader1, 'compare width--70-percents', $compareHeader1ColSpan);
                    echoTableTH($compareHeader2, 'compare', $compareHeader2ColSpan);

                    echoTableTREnd();

                    $excelData .= formatExcelData($compareHeader1);
                    for ($i = 0; $i < $compareHeader1ColSpan; $i++) {
                        $excelData .= S::EXCEL_SEPARATOR;
                    }

                    $excelData .= formatExcelData($compareHeader2);
                    for ($i = 0; $i < $compareHeader2ColSpan; $i++) {
                        $excelData .= S::EXCEL_SEPARATOR;
                    }

                    $excelData .= S::EXCEL_EOL;
                }

                echoTableTRStart();
                echoTableTH(ColumnsStrings::COMPARE_NUM);

                $excelData .= formatExcelData(ColumnsStrings::COMPARE_NUM);

                for ($i = 0; $i < $result->field_count; $i++) {
                    if ($fieldsInfo[$i]->visible) {
                        $class = null;
                        if (isResultTypeCompare($resultType)) {
                            if ($fieldsInfo[$i]->name == C::SIDE_DIFFERENCE ||
                                $fieldsInfo[$i]->name == C::CARRIAGE_DIFFERENCE
                            ) {
                                $class = "compare width--15-percents";
                            }
                        }

                        $cell = columnName($fieldsInfo[$i]->name, $scaleInfo->getType(), $resultType);

                        $title = columnTitle($fieldsInfo[$i]->name);

                        if (DEBUG_SHOW_QUERY) {
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
                    echoTableTH($cell);
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


                $ironControlTotalAvg = 0.0;
                $ironControlTotalSum = 0.0;
                $ironControlTotalCount = 0;

// ------------- Перебор строк -----------------------------------------------------------------------------------------
                while ($row = $result->fetch_array()) {
                    $rowColorClass = getRowColorClass($numColor);

                    if ($resultType == ResultType::TRAIN_DYNAMIC) {
                        $href = $hrefBuilder
                            ->setParam(ParamName::SCALE_NUM,
                                $scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES ?
                                    $row[C::SCALE_NUM] :
                                    $scaleNum)
                            ->setParam(ParamName::TRAIN_NUM, $row[C::TRAIN_NUM])
                            ->setParam(ParamName::TRAIN_UNIX_TIME, $row[C::UNIX_TIME])
                            ->setParam(ParamName::TRAIN_DATETIME, strtotime($row[C::DATETIME]))
                            ->build();
                    } elseif (isResultTypeCargoList($resultType)) {
                        $href = $hrefBuilder
                            ->setParam(ParamName::CARGO_TYPE, latin1ToUtf8($row[C::CARGO_TYPE]))
                            ->build();
                    }

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

                        $field = latin1ToUtf8($row[$fieldNum]);

                        $field = formatFieldValue($fieldsInfo[$fieldNum]->name, $field,
                            $filter->isFull());

                        $excelData .= S::EXCEL_SEPARATOR . formatExcelData($field);

                        if (($fieldsInfo[$fieldNum]->name == C::BRUTTO) &&
                            isResultTypeCompare($resultType)
                        ) {
                            $field = "<b>" . $field . "</b>";
                        }

                        $class = $fieldsInfo[$fieldNum]->leftAlign ?
                            ($newDesign ?
                                'mdl-data-table__cell--non-numeric' :
                                'text-align--left') :
                            null;

                        $cellColor = null;

                        if ($resultType == ResultType::IRON_CONTROL) {
                            if ($fieldsInfo[$fieldNum]->name == C::IRON_CONTROL_DIFF_DYN_STA) {
                                $value = $row[C::IRON_CONTROL_DIFF_DYN_STA];

                                if ($value != "") {
                                    $ironControlTotalCount++;

                                    $ironControlTotalSum += $value;
                                }

                                $cellColor = getCellWarningColor($value,
                                    Constants::IRON_CONTROL_DIFF_DYN_STA_WARNING_YELLOW, Constants::IRON_CONTROL_DIFF_DYN_STA_WARNING_RED);
                            }

                            if ($fieldsInfo[$fieldNum]->name == C::IRON_CONTROL_DIFF_SIDE) {
                                $cellColor = getCellWarningColor($row[C::IRON_CONTROL_DIFF_SIDE],
                                    Constants::IRON_CONTROL_DIFF_SIDE_WARNING_YELLOW, Constants::IRON_CONTROL_DIFF_SIDE_WARNING_RED);
                            }

                            if ($fieldsInfo[$fieldNum]->name == C::IRON_CONTROL_DIFF_CARRIAGE) {
                                $cellColor = getCellWarningColor($row[C::IRON_CONTROL_DIFF_CARRIAGE],
                                    Constants::IRON_CONTROL_DIFF_CARRIAGE_WARNING_YELLOW, Constants::IRON_CONTROL_DIFF_CARRIAGE_WARNING_RED);
                            }
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

                        echoTableTD($field, $class, $showHref ? $href : null);
                    }

                    if (isResultTypeCompare($resultType)) {
                        if ($scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                            $queryCompare->setScaleNum($row[C::SCALE_NUM]);
                        }
                        $queryCompare
                            ->setVanNumber($row[C::VAN_NUMBER])
                            ->setDateTime((int)$row[C::UNIX_TIME]);

                        $queryCompareStr = $queryCompare->getQuery();

//                    echo $queryCompareStr . "<br>";

                        $resultCompare = $mysqli->query($queryCompareStr);

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
                                    Constants::COMPARE_VALUE_WARNING_YELLOW, Constants::COMPARE_VALUE_WARNING_RED);
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
                    }

                    echoTableTREnd();

                    $excelData .= S::EXCEL_EOL;

                    $numColor = !$numColor;
                } // while

// ----------------- Итого ---------------------------------------------------------------------------------------------

// ----------------- Контрольная провеска чугуна -----------------------------------------------------------------------
                if ($resultType == ResultType::IRON_CONTROL) {
// --------------------------------------------------------------------

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

                    echoTableTD(S::TEXT_AVG, $newDesign ? 'mdl-data-table__cell--right' : 'text-align--right', null, $colSpanTotal);

                    if ($ironControlTotalCount > 0) {
                        $ironControlTotalAvg = $ironControlTotalSum / $ironControlTotalCount;
                    }

                    $field = formatFieldValue(C::IRON_CONTROL_DIFF_DYN_STA, $ironControlTotalAvg . "", $filter->isFull());

                    $class = getCellWarningColor($ironControlTotalAvg,
                        Constants::IRON_CONTROL_AVG_VALUE_WARNING_YELLOW, Constants::IRON_CONTROL_AVG_VALUE_WARNING_RED);

                    echoTableTD($field, $class);

                    echoTableTD("", null, null, $colSpanTotalValue);

                    echoTableTREnd();

// ---------------------------------------------------------------------------------------------------------------------
                    $numColor = !$numColor;

                    echoTableTRStart(getRowColorClass($numColor));

                    echoTableTD(S::TEXT_SUM, $newDesign ? 'mdl-data-table__cell--right' : 'text-align--right', null, $colSpanTotal);

                    $field = formatFieldValue(C::IRON_CONTROL_DIFF_DYN_STA, $ironControlTotalSum . "", $filter->isFull());

                    $class = getCellWarningColor($ironControlTotalSum,
                        Constants::IRON_CONTROL_SUM_VALUE_WARNING_YELLOW, Constants::IRON_CONTROL_SUM_VALUE_WARNING_RED);

                    echoTableTD($field, $class);

                    echoTableTD("", null, null, $colSpanTotalValue);

                    echoTableTREnd();
                }
// ----------------- Контрольная провеска чугуна -----------------------------------------------------------------------

                echoTableBodyEnd();
                echoTableEnd();
// ------------- Конец таблицы -----------------------------------------------------------------------------------------

                echoFormStart('formExcel', 'excel.php', null, null, false, true);

                $fileName = match ($scaleNum) {
                    Constants::SCALE_NUM_ALL_TRAIN_SCALES => "AT",
                    Constants::SCALE_NUM_REPORT_IRON => "IR",
                    Constants::SCALE_NUM_REPORT_IRON_CONTROL => "IC",
                    Constants::SCALE_NUM_REPORT_VANLIST => "VL_T",
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