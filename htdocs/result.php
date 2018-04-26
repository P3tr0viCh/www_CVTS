<?php
const DEBUG_SHOW_QUERY = false;

$timeStart = microtime(true);

require_once "include/Constants.php";

if (!isset($_GET[ParamName::SCALE_NUM])) {
    if (!isset($_GET[ParamName::REPORT_TYPE])) {
        header("Location: " . "/index.php");
        exit();
    }
}

require_once "include/MySQLConnection.php";

require_once "include/QueryResult.php";
require_once "include/QueryCompare.php";

require_once "include/Strings.php";
require_once "include/Database.php";
require_once "include/ColumnsStrings.php";

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

use Strings as S;

$newDesign = isNewDesign();

$reportType = getParamGETAsInt(ParamName::REPORT_TYPE, ReportType::TYPE_DEFAULT);

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

$filter = new ResultFilter();

switch ($reportType) {
    case ReportType::TYPE_DEFAULT:
        $scaleNum = getParamGETAsInt(ParamName::SCALE_NUM);
        $scaleNum = $scaleNum == null ? Constants::SCALE_NUM_ALL_TRAIN_SCALES : (int)$scaleNum;
        $useBackup = getParamGETAsBool(ParamName::USE_BACKUP);

        $filter
            ->setVanNumber(getParamGETAsString(ParamName::VAN_NUMBER))
            ->setCargoType(getParamGETAsString(ParamName::CARGO_TYPE))
            ->setInvoiceNum(getParamGETAsString(ParamName::INVOICE_NUM))
            ->setInvoiceSupplier(getParamGETAsString(ParamName::INVOICE_SUPPLIER))
            ->setInvoiceRecipient(getParamGETAsString(ParamName::INVOICE_RECIPIENT))
            ->setOnlyChark(getParamGETAsBool(ParamName::ONLY_CHARK))
            ->setScalesFilter(getParamGETAsString(ParamName::SCALES))
            ->setFull(getParamGETAsBool(ParamName::ALL_FIELDS))
            ->setShowCargoDate(getParamGETAsBool(ParamName::SHOW_CARGO_DATE))
            ->setShowDeltas(getParamGETAsBool(ParamName::SHOW_DELTAS))
            ->setOrderByDateTime(getParamGETAsBool(ParamName::ORDER_BY_DATETIME))
            ->setCompareForward(getParamGETAsBool(ParamName::COMPARE_FORWARD))
            ->setCompareByBrutto(getParamGETAsBool(ParamName::COMPARE_BY_BRUTTO));

        $resultType = getParamGETAsString(ParamName::RESULT_TYPE);

        if (empty($resultType)) {

            function getResultType($type)
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

                ResultType::COEFFS);

            foreach ($resultTypes as $result) {
                $resultType = getResultType($result);
                if (!empty($resultType)) {
                    break;
                }
            }
        }

        if (empty($resultType)) {
            throw new InvalidArgumentException("Empty resultType");
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
                header("Location: " . "/index.php");
                die();
        }

        $scaleNum = getParamGETAsInt(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES);
        $useBackup = getParamGETAsBool(ParamName::USE_BACKUP, false);

        $filter
            ->setFull(getParamGETAsBool(ParamName::ALL_FIELDS, false))
            ->setCargoType((getParamGETAsString(ParamName::CARGO_TYPE)));

        $dateTimeStart = getParamGETAsInt(ParamName::DATETIME_START);
        $dateTimeEnd = getParamGETAsInt(ParamName::DATETIME_END);

        break;
    case ReportType::TRAINS:
        $resultType = ResultType::TRAIN_DYNAMIC_ONE;

        $scaleNum = getParamGETAsInt(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES);
        $useBackup = getParamGETAsBool(ParamName::USE_BACKUP, false);

        $filter
            ->setFull(getParamGETAsBool(ParamName::ALL_FIELDS, false))
            ->setTrainNum(getParamGETAsInt(ParamName::TRAIN_NUM))
            ->setTrainUnixTime(getParamGETAsInt(ParamName::TRAIN_UNIX_TIME))
            ->setTrainDateTime(getParamGETAsInt(ParamName::TRAIN_DATETIME))
            ->setShowDeltas(getParamGETAsBool(ParamName::SHOW_DELTAS));

        break;
    default:
        header("Location: " . "/index.php");
        die();
}

if (isResultTypeCompare($resultType)) {
    $filter->setFull(false);
}

echoStartPage();

$title = S::TITLE_ERROR;

$resultMessage = null;

$excelData = null;

$header = null;
$subHeader = null;
$whereHeader = null;
$navLinks = null;
$menuItems = null;

$mysqli = MySQLConnection::getInstance($useBackup);

if ($mysqli) {
    if ($mysqli->connect_errno) {
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

            switch ($reportType) {
                case ReportType::TRAINS:
                    break;
                case ReportType::CARGO_TYPES:
                    break;
                default:
                    /** @var DateTimeBuilder $dateTimeBuilder */
                    $dateTimeBuilder = DateTimeBuilder::getInstance();

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

            $subHeader = getResultHeader($resultType);

            if ($resultType == ResultType::TRAIN_DYNAMIC_ONE) {
                $subHeader = sprintf(S::HEADER_RESULT_PERIOD_DATE, $subHeader,
                    formatDateTime($filter->getTrainDateTime()));
            } else {
                if ($dateTimeStart && $dateTimeEnd) {
                    $subHeader = sprintf(S::HEADER_RESULT_PERIOD_FROM_TO, $subHeader,
                        formatDateTime($dateTimeStart), formatDateTime($dateTimeEnd));
                } else if ($dateTimeStart) {
                    $subHeader = sprintf(S::HEADER_RESULT_PERIOD_FROM, $subHeader,
                        formatDateTime($dateTimeStart));
                } elseif ($dateTimeEnd) {
                    $subHeader = sprintf(S::HEADER_RESULT_PERIOD_TO, $subHeader,
                        formatDateTime($dateTimeEnd));
                } else {
                    $subHeader = sprintf(S::HEADER_RESULT_PERIOD_ALL, $subHeader);
                }
            }

            $excelData .= $subHeader . S::EXCEL_EOL;

            $filter
                ->setScaleNum($scaleNum)
                ->setDateTimeStart($dateTimeStart)
                ->setDateTimeEnd($dateTimeEnd);

            /**
             * @param string $name
             * @param string $value
             * @param string $html
             * @return string
             */
            function formatWhereHeader($name, $value, $html)
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

            switch ($resultType) {
                case ResultType::AUTO_BRUTTO:
                case ResultType::AUTO_TARE:
                case ResultType::VAN_DYNAMIC_BRUTTO:
                case ResultType::VAN_DYNAMIC_TARE:
                case ResultType::VAN_STATIC_BRUTTO:
                case ResultType::VAN_STATIC_TARE:
                case ResultType::COMPARE_DYNAMIC:
                case ResultType::COMPARE_STATIC:
                    $whereHeader = formatWhereHeader($scaleInfo->getType() == ScaleType::AUTO ?
                        S::HEADER_RESULT_SEARCH_AUTO_NUMBER :
                        S::HEADER_RESULT_SEARCH_VAN_NUMBER,
                        $filter->getVanNumber(), $newDesign);
            }

            if ($resultType == ResultType::TRAIN_DYNAMIC_ONE ||
                $resultType == ResultType::VAN_DYNAMIC_BRUTTO ||
                $resultType == ResultType::VAN_STATIC_BRUTTO ||
                $resultType == ResultType::AUTO_BRUTTO ||
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
                $whereHeader = S::HEADER_RESULT_SEARCH . " " . $whereHeader;
                $excelData .= formatExcelData($whereHeader) . S::EXCEL_EOL;
            }
        }
    }
} else {
    $resultMessage = mysqlConnectionFileError();
}

echoHead($newDesign, $title, null, $newDesign ?
    array("/javascript/common.js", "/javascript/result.js") :
    "/javascript/hr.js");

echoStartBody($newDesign, $newDesign ? "showContent()" : null);

(new HtmlHeader($newDesign))
    ->setMainPage(false)
    ->setHeader($header)
    ->setSubHeader($subHeader . ($useBackup ? (" (" . S::HEADER_PAGE_MAIN_BACKUP . ")") : null))
    ->setSubHeaderAddClass($useBackup ? "color-text--error" : null)
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

    $queryResult = new QueryResult();
    $queryResult->setScaleType($scaleInfo->getType());
    $queryResult->setResultType($resultType);
    $queryResult->setFilter($filter);

    $query = $queryResult->getQuery();

    if (DEBUG_SHOW_QUERY) {
        echo "Query: " . latin1ToUtf8($query) . "<br>" . PHP_EOL;
    }

    $result = $mysqli->query($query);

    if ($result) {
        $numRows = $result->num_rows;

        /** @var FieldInfo[] $fieldsInfo */
        $fieldsInfo = array();

        if ($numRows > 0) {
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
                        if ($fieldsInfo[$i]->name == Database\Columns::SIDE_DIFFERENCE ||
                            $fieldsInfo[$i]->name == Database\Columns::CARRIAGE_DIFFERENCE
                        ) {
                            $class = "compare width--15-percents";
                        }
                    }

                    $cell = columnName($fieldsInfo[$i]->name, $scaleInfo->getType(), $resultType);

                    echoTableTH($cell, $class);

                    $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);
                }
            }

            if (isResultTypeCompare($resultType)) {
                $cell = columnName(Database\Columns::SCALE_NUM, $scaleInfo->getType());
                echoTableTH($cell);
                $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);

                $cell = columnName($filter->isCompareByBrutto() ?
                    Database\Columns::BRUTTO :
                    Database\Columns::NETTO,
                    $scaleInfo->getType());
                echoTableTH($cell);
                $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);

                $cell = columnName(Database\Columns::DATETIME, $scaleInfo->getType());
                echoTableTH($cell);
                $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);

                $cell = columnName(Database\Columns::COMPARE, $scaleInfo->getType());
                echoTableTH($cell);
                $excelData .= S::EXCEL_SEPARATOR . formatExcelData($cell);
            }

            echoTableTREnd();
            echoTableHeadEnd();

            $excelData .= S::EXCEL_EOL;

            echoTableBodyStart();

            $rowIndex = 0;
            $numColor = false;

            $hrefBuilder = \HrefBuilder\Builder::getInstance()
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
                    ->setCompareForward($filter->isCompareForward())
                    ->setCompareByBrutto($filter->isCompareByBrutto());

                if ($scaleNum != Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                    $queryCompare->setScaleNum($scaleNum);
                }

            } else {
                $queryCompare = null;
            }

            while ($row = $result->fetch_array()) {
                $rowColorClass = getRowColorClass($numColor);

                if ($resultType == ResultType::TRAIN_DYNAMIC) {
                    $href = $hrefBuilder
                        ->setParam(ParamName::SCALE_NUM,
                            $scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES ?
                                $row[Database\Columns::SCALE_NUM] :
                                $scaleNum)
                        ->setParam(ParamName::TRAIN_NUM, $row[Database\Columns::TRAIN_NUM])
                        ->setParam(ParamName::TRAIN_UNIX_TIME, $row[Database\Columns::UNIX_TIME])
                        ->setParam(ParamName::TRAIN_DATETIME, strtotime($row[Database\Columns::DATETIME]))
                        ->build();
                } elseif (isResultTypeCargoList($resultType)) {
                    $href = $hrefBuilder
                        ->setParam(ParamName::CARGO_TYPE, latin1ToUtf8($row[Database\Columns::CARGO_TYPE]))
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

                for ($fieldNum = 0; $fieldNum < $result->field_count; $fieldNum++) {
                    if (!$fieldsInfo[$fieldNum]->visible) {
                        continue;
                    }

                    $field = latin1ToUtf8($row[$fieldNum]);

                    $field = formatFieldValue($fieldsInfo[$fieldNum]->name, $field,
                        $filter->isFull());

                    if (($fieldsInfo[$fieldNum]->name == Database\Columns::BRUTTO) &&
                        isResultTypeCompare($resultType)
                    ) {
                        $field = "<b>" . $field . "</b>";
                    }

                    $excelData .= S::EXCEL_SEPARATOR . formatExcelData($field);

                    $class = $fieldsInfo[$fieldNum]->leftAlign ?
                        ($newDesign ?
                            'mdl-data-table__cell--non-numeric' :
                            'text-align--left') :
                        null;

                    $showHref = false;
                    if (!$newDesign && $href) {
                        if ($resultType == ResultType::TRAIN_DYNAMIC) {
                            $showHref =
                                $fieldsInfo[$fieldNum]->name == Database\Columns::SCALE_NUM ||
                                $fieldsInfo[$fieldNum]->name == Database\Columns::DATETIME;
                        } elseif (isResultTypeCargoList($resultType)) {
                            $showHref = $fieldsInfo[$fieldNum]->name == Database\Columns::CARGO_TYPE;
                        }
                    }

                    echoTableTD($field, $class, $showHref ? $href : null);
                }

                if (isResultTypeCompare($resultType)) {
                    if ($scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
                        $queryCompare->setScaleNum($row[Database\Columns::SCALE_NUM]);
                    }
                    $queryCompare
                        ->setVanNumber($row[Database\Columns::VAN_NUMBER])
                        ->setDateTime((int)$row[Database\Columns::UNIX_TIME]);

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

                            if ($fieldsCompareInfo[$i]->name == Database\Columns::BRUTTO) {
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
                            Database\Columns::BRUTTO :
                            Database\Columns::NETTO;

                        $value = $row[$compareColumn];
                        $valueCompare = $rowCompare[$compareColumn];

                        $fieldCompare = $value - $valueCompare;

                        if ($valueCompare == 0.0) {
                            if (abs($value) < 1) {
                                $class = null;
                            } else {
                                $class = 'color--gray';
                            }
                        } elseif (abs($fieldCompare) < 1) {
                            $class = null;
                        } elseif (abs($fieldCompare) < 2) {
                            $class = 'color--yellow';
                        } else {
                            $class = 'color--red';
                        }

                        $fieldCompare = formatFieldValue(Database\Columns::COMPARE, $fieldCompare, $filter->isFull());

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

            echoTableBodyEnd();
            echoTableEnd();

            echoFormStart('formExcel', 'excel.php', null, null, false, true);

            echoHidden(ParamName::EXCEL_FILENAME, date("Y.m.d H-i-s") . ".csv");

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
                echo '<script type="text/javascript">hasData = true;</script>' . PHP_EOL;
            }
        } else {
            if ($newDesign) {
                echo '<script type="text/javascript">hasData = false;</script>' . PHP_EOL;
            }

            $resultMessage = new ResultMessage(S::TEXT_ZERO_RESULT, null);
        }
    } else {
        $resultMessage = queryError($mysqli);
    }
}

if ($resultMessage) {
    echoErrorPage($resultMessage->getError(), $resultMessage->getErrorDetails());
}

echoEndContent();

echoEndMain($newDesign);

echoEndBody($newDesign, $newDesign ? null : "updateHRWidthOnEndBody();");

echoEndPage();

echo PHP_EOL . PHP_EOL;
echo "<!-- execution time == " . number_format(microtime(true) - $timeStart, 5) . " sec -->" . PHP_EOL;