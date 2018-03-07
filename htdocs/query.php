<?php
require_once "include/MySQLConnection.php";

require_once "include/Constants.php";
require_once "include/Strings.php";

require_once "include/Functions.php";
require_once "include/CheckUser.php";
require_once "include/CheckBrowser.php";

require_once "include/ScaleInfo.php";

require_once "include/echo_html_page.php";
require_once "include/echo_drawer.php";
require_once "include/echo_form.php";
require_once "include/echo_footer.php";

require_once "include/HtmlHeader.php";

use Strings as S;

$newDesign = isNewDesign();
$useBackup = getParamGETAsBool(ParamName::USE_BACKUP, false);

$scales = getParamGETAsInt(ParamName::SCALE_NUM, Constants::SCALE_NUM_ALL_TRAIN_SCALES);

echoStartPage();

if ($scales < 0) {
    $scales = Constants::SCALE_NUM_ALL_TRAIN_SCALES;
}

$title = S::TITLE_ERROR;

$scaleInfo = null;
$resultMessage = null;

$header = null;
$navLinks = null;

$mysqli = MySQLConnection::getInstance($useBackup);

if ($mysqli) {
    if ($mysqli->connect_errno) {
        $resultMessage = connectionError($mysqli);
    } else {
        $scaleInfo = new ScaleInfo($scales);

        $resultMessage = $scaleInfo->query($mysqli);

        if (!$resultMessage) {
            $header = $scaleInfo->getHeader();

            $title = $scaleInfo->getPlace();

            $navLinks = array();

            $navLinks[] = new HtmlHeaderNavLink('clear', 'clear', S::NAV_LINK_CLEAR, 'formReset()');
            $navLinks[] = new HtmlHeaderNavLink('back', 'arrow_back', S::NAV_LINK_BACK, 'goBack()');
        }
    }
} else {
    $resultMessage = mysqlConnectionFileError();
}

echoHead($newDesign, $title, null, array("/javascript/footer.js", "/javascript/common.js", "/javascript/query.js"));

echoStartBody($newDesign);

(new HtmlHeader($newDesign))
    ->setMainPage(false)
    ->setHeader($header)
    ->setSubHeader($useBackup ? S::HEADER_PAGE_MAIN_BACKUP : null)
    ->setSubHeaderAddClass($useBackup ? "color-text--error" : null)
    ->setNavLinks($navLinks)
    ->draw();

echoDrawer($newDesign, $mysqli);

echoStartMain($newDesign);

echoStartContent();

if (!$resultMessage) {
    echoFormStart("formResult", "result.php");

    echoHidden(ParamName::SCALE_NUM, (int)$scales);
    echoHidden(ParamName::NEW_DESIGN, (bool)$newDesign);
    echoHidden(ParamName::USE_BACKUP, (bool)$useBackup);
    echo PHP_EOL;

    if ($newDesign) {
        echo S::TAB;
        echo '<div class="mdl-grid mdl-cell--stretch">' . PHP_EOL;
        echo S::TAB;
        echo '<div class="mdl-cell mdl-cell--4-col mdl-cell--stretch">' . PHP_EOL;
    } else {
        echo S::TAB;
        echo '<table class="query">' . PHP_EOL;

        echo S::TAB;
        echo '<tr>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<th class="query">';
        echo S::HEADER_INFO;
        echo '</th>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<th class="query">';
        echo S::HEADER_PERIOD;
        echo '</th>' . PHP_EOL;
        echo S::TAB;
        echo '</tr>' . PHP_EOL;

        echo S::TAB;
        echo '<tr>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<td class="query" rowspan="6">' . PHP_EOL;
    }

    switch ($scaleInfo->getType()) {
        case ScaleType::DEFAULT_TYPE:
            if (($scaleInfo->getClass() == ScaleClass::CLASS_DYNAMIC_AND_STATIC) ||
                ($scaleInfo->getClass() == ScaleClass::CLASS_DYNAMIC)
            ) {
                echo S::TAB . S::TAB;
                echo '<h5>' . S::HEADER_DYNAMIC . '</h5>' . PHP_EOL . PHP_EOL;

                echoButton($newDesign, S::BUTTON_TRAINS, ParamName::RESULT_TYPE, ResultType::TRAIN_DYNAMIC);
                echoButton($newDesign, S::BUTTON_VANS_BRUTTO, ParamName::RESULT_TYPE, ResultType::VAN_DYNAMIC_BRUTTO);
                echoButton($newDesign, S::BUTTON_VANS_TARE, ParamName::RESULT_TYPE, ResultType::VAN_DYNAMIC_TARE);
                echoButton($newDesign, S::BUTTON_CARGOS, ParamName::RESULT_TYPE, ResultType::CARGO_LIST_DYNAMIC);
                echoButton($newDesign, S::BUTTON_COMPARE, ParamName::RESULT_TYPE, ResultType::COMPARE_DYNAMIC);
            }

            if ($scaleInfo->getClass() == ScaleClass::CLASS_DYNAMIC_AND_STATIC) {
                echo S::TAB . S::TAB;
                echo '<br>' . PHP_EOL . PHP_EOL;
            }

            if (($scaleInfo->getClass() == ScaleClass::CLASS_DYNAMIC_AND_STATIC) ||
                ($scaleInfo->getClass() == ScaleClass::CLASS_STATIC)
            ) {
                echo S::TAB . S::TAB;
                echo '<h5>' . S::HEADER_STATIC . '</h5>' . PHP_EOL . PHP_EOL;

                echoButton($newDesign, S::BUTTON_VANS_BRUTTO, ParamName::RESULT_TYPE, ResultType::VAN_STATIC_BRUTTO);
                echoButton($newDesign, S::BUTTON_VANS_TARE, ParamName::RESULT_TYPE, ResultType::VAN_STATIC_TARE);
                echoButton($newDesign, S::BUTTON_CARGOS, ParamName::RESULT_TYPE, ResultType::CARGO_LIST_STATIC);
                echoButton($newDesign, S::BUTTON_COMPARE, ParamName::RESULT_TYPE, ResultType::COMPARE_STATIC);
            }

            if (($scaleInfo->getClass() == ScaleClass::CLASS_DYNAMIC_AND_STATIC) ||
                ($scaleInfo->getClass() == ScaleClass::CLASS_DYNAMIC)
            ) {
                if (CheckUser::isPowerUser()) {
                    echo S::TAB . S::TAB;
                    echo '<br>' . PHP_EOL . PHP_EOL;
                    echo S::TAB . S::TAB;
                    echo '<h5>' . S::HEADER_SERVICE . '</h5>' . PHP_EOL . PHP_EOL;

                    echoButton($newDesign, S::BUTTON_COEFFS, ParamName::RESULT_TYPE, ResultType::COEFFS);
                }
            }
            break;
        case ScaleType::WMR:
            echo S::TAB . S::TAB;
            echo '<h5>' . S::HEADER_DYNAMIC . '</h5>' . PHP_EOL . PHP_EOL;

            echoButton($newDesign, S::BUTTON_VANS_BRUTTO, ParamName::RESULT_TYPE, ResultType::VAN_DYNAMIC_BRUTTO);
            echoButton($newDesign, S::BUTTON_VANS_TARE, ParamName::RESULT_TYPE, ResultType::VAN_DYNAMIC_TARE);
            echoButton($newDesign, S::BUTTON_CARGOS, ParamName::RESULT_TYPE, ResultType::CARGO_LIST_DYNAMIC);
            echoButton($newDesign, S::BUTTON_COMPARE, ParamName::RESULT_TYPE, ResultType::COMPARE_DYNAMIC);

            break;
        case ScaleType::AUTO:
            echo S::TAB . S::TAB;
            echo '<h5>' . S::HEADER_STATIC . '</h5>' . PHP_EOL . PHP_EOL;

            echoButton($newDesign, S::BUTTON_BRUTTO, ParamName::RESULT_TYPE, ResultType::AUTO_BRUTTO);
            echoButton($newDesign, S::BUTTON_TARE, ParamName::RESULT_TYPE, ResultType::AUTO_TARE);
            echoButton($newDesign, S::BUTTON_CARGOS, ParamName::RESULT_TYPE, ResultType::CARGO_LIST_AUTO);

            break;
        case ScaleType::KANAT:
            echo S::TAB . S::TAB;
            echo '<h5>' . S::HEADER_RESULTS . '</h5>' . PHP_EOL . PHP_EOL;

            echoButton($newDesign, S::BUTTON_VIEW, ParamName::RESULT_TYPE, ResultType::KANAT);

            break;
        case ScaleType::DP:
            echo S::TAB . S::TAB;
            echo '<h5>' . S::HEADER_RESULTS . '</h5>' . PHP_EOL . PHP_EOL;

            echoButton($newDesign, S::BUTTON_VIEW, ParamName::RESULT_TYPE, ResultType::DP);
            echoButton($newDesign, S::BUTTON_SUM_FOR_PERIOD, ParamName::RESULT_TYPE, ResultType::DP_SUM);
    }

    if ($newDesign) {
        echo S::TAB;
        echo '</div> <!-- mdl-cell -->' . PHP_EOL;
    } else {
        echo S::TAB . S::TAB;
        echo '</td>' . PHP_EOL . PHP_EOL;
    }

    /**
     * Колонка "Период".
     */
    if ($newDesign) {
        echo PHP_EOL . S::TAB;
        echo '<div class="mdl-cell mdl-cell--4-col mdl-cell--stretch">' . PHP_EOL;

        echo S::TAB . S::TAB;
        echo '<h5>' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '<span class="material-icons color--grey material-icons--for-header">event</span>';

        echo S::HEADER_PERIOD;

        echo '<span id="menu-dates" class="mdl-button for-header mdl-js-button mdl-button--fab mdl-button--icon">' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB . S::TAB;
        echo '<span class="material-icons">more_vert</span>' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '</span>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '</h5>' . PHP_EOL . PHP_EOL;

        echo S::TAB . S::TAB;
        /** @noinspection HtmlUnknownAttribute */
        echo '<ul class="mdl-menu mdl-menu--bottom-left mdl-js-menu mdl-js-ripple-effect" for="menu-dates">' . PHP_EOL;

        echo S::TAB . S::TAB . S::TAB;
        echo '<li class="mdl-menu__item" onclick="setDates(\'startCurrentDay\')">';
        echo S::MENU_DATES_CURRENT_DAY;
        echo '</li>' . PHP_EOL;

        echo S::TAB . S::TAB . S::TAB;
        echo '<li class="mdl-menu__item" onclick="setDates(\'startCurrentMonth\')">';
        echo S::MENU_DATES_CURRENT_MONTH;
        echo '</li>' . PHP_EOL;

        echo S::TAB . S::TAB . S::TAB;
        echo '<li class="mdl-menu__item" onclick="setDates(\'startCurrentWeek\')">';
        echo S::MENU_DATES_CURRENT_WEEK;
        echo '</li>' . PHP_EOL;

        echo S::TAB . S::TAB . S::TAB;
        echo '<li class="mdl-menu__item" onclick="setDates(\'prevDay\')">';
        echo S::MENU_DATES_PREV_DAY;
        echo '</li>' . PHP_EOL;

        echo S::TAB . S::TAB . S::TAB;
        echo '<li class="mdl-menu__item" onclick="setDates(\'from5to5\')">';
        echo S::MENU_DATES_FROM_5_TO_5;
        echo '</li>' . PHP_EOL;

        echo S::TAB . S::TAB . S::TAB;
        echo '<li class="mdl-menu__item" onclick="setDates(\'from20to20\')">';
        echo S::MENU_DATES_FROM_20_TO_20;
        echo '</li>' . PHP_EOL;

        echo S::TAB . S::TAB . S::TAB;
        echo '<li class="mdl-menu__item" onclick="setDates(\'\')">';
        echo S::MENU_DATES_CLEAR;
        echo '</li>' . PHP_EOL;

        echo S::TAB . S::TAB;
        echo '</ul>' . PHP_EOL;
    } else {
        echo S::TAB . S::TAB;
        echo '<td class="query">';
    }

    echo PHP_EOL;
    echo S::TAB . S::TAB;
    echo '<h6>' . S::HEADER_PERIOD_START . '</h6>';
    echo PHP_EOL;

    echoInput($newDesign, ParamName::DATETIME_START_DAY, S::INPUT_DAY, S::INPUT_DAY_HELP, S::INPUT_DAY_PATTERN, 2, 2, true, false);
    if (!$newDesign) {
        echo S::TAB . S::TAB;
        echo '<span class="text-input__field"> . </span>' . PHP_EOL;
    }

    echoInput($newDesign, ParamName::DATETIME_START_MONTH, S::INPUT_MONTH, S::INPUT_MONTH_HELP, S::INPUT_MONTH_PATTERN, 2, 2, true, false);
    if (!$newDesign) {
        echo S::TAB . S::TAB;
        echo '<span class="text-input__field"> . </span>' . PHP_EOL;
    }

    echoInput($newDesign, ParamName::DATETIME_START_YEAR, S::INPUT_YEAR, S::INPUT_YEAR_HELP, S::INPUT_YEAR_PATTERN, 4, 4, true, false);

    echo S::TAB . S::TAB;
    echo $newDesign ? '<br>' : '<span>&nbsp;</span>';
    echo PHP_EOL;

    echoInput($newDesign, ParamName::DATETIME_START_HOUR, S::INPUT_HOUR, S::INPUT_HOUR_HELP, S::INPUT_HOUR_PATTERN, 2, 2, true, false);
    if (!$newDesign) {
        echo S::TAB . S::TAB;
        echo '<span class="text-input__field"> : </span>' . PHP_EOL;
    }

    echoInput($newDesign, ParamName::DATETIME_START_MINUTES, S::INPUT_MINUTES, S::INPUT_MINUTES_HELP, S::INPUT_MINUTES_PATTERN, 2, 2, true, false);
    if (!$newDesign) {
        echo S::TAB . S::TAB . '<br>';
    }

    echo PHP_EOL;
    echo S::TAB . S::TAB;
    echo '<h6>' . S::HEADER_PERIOD_END . '</h6>';
    echo PHP_EOL;

    echoInput($newDesign, ParamName::DATETIME_END_DAY, S::INPUT_DAY, S::INPUT_DAY_HELP, S::INPUT_DAY_PATTERN, 2, 2, true, false);
    if (!$newDesign) {
        echo S::TAB . S::TAB;
        echo '<span class="text-input__field">.</span>' . PHP_EOL;
    }

    echoInput($newDesign, ParamName::DATETIME_END_MONTH, S::INPUT_MONTH, S::INPUT_MINUTES_HELP, S::INPUT_MONTH_PATTERN, 2, 2, true, false);
    if (!$newDesign) {
        echo S::TAB . S::TAB;
        echo '<span class="text-input__field">.</span>' . PHP_EOL;
    }

    echoInput($newDesign, ParamName::DATETIME_END_YEAR, S::INPUT_YEAR, S::INPUT_YEAR_HELP, S::INPUT_YEAR_PATTERN, 4, 4, true, false);

    echo S::TAB . S::TAB;
    echo $newDesign ? '<br>' : '<span>&nbsp</span>';
    echo PHP_EOL;

    echoInput($newDesign, ParamName::DATETIME_END_HOUR, S::INPUT_HOUR, S::INPUT_HOUR_HELP, S::INPUT_HOUR_PATTERN, 2, 2, true, false);
    if (!$newDesign) {
        echo S::TAB . S::TAB;
        echo '<span class="text-input__field">:</span>' . PHP_EOL;
    }

    echoInput($newDesign, ParamName::DATETIME_END_MINUTES, S::INPUT_MINUTES, S::INPUT_MINUTES_HELP, S::INPUT_MINUTES_PATTERN, 2, 2, true, false);

    if (!$newDesign) {
        echo S::TAB . S::TAB;
        echo '<br>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<small>' . S::HELP_PERIOD_OLD . '</small>' . PHP_EOL;
    }

    if ($newDesign) {
        echo S::TAB;
        echo '</div> <!-- mdl-cell -->' . PHP_EOL . PHP_EOL;
    } else {
        echo S::TAB . S::TAB;
        echo '</td>' . PHP_EOL;
        echo S::TAB;
        echo '</tr>' . PHP_EOL . PHP_EOL;
    }

    /**
     * Поле "Поиск".
     */
    if ($newDesign) {
        echo S::TAB;
        echo '<div class="mdl-cell mdl-cell--4-col mdl-cell--stretch">' . PHP_EOL;
    }

    if ($scaleInfo->getType() == ScaleType::DEFAULT_TYPE ||
        $scaleInfo->getType() == ScaleType::WMR ||
        $scaleInfo->getType() == ScaleType::AUTO ||
        $scaleInfo->getType() == ScaleType::DP
    ) {
        if ($newDesign) {
            echo S::TAB . S::TAB;
            echo '<h5>' . PHP_EOL;
            echo S::TAB . S::TAB . S::TAB;
            echo '<span class="material-icons color--grey material-icons--for-header">search</span>';
        } else {
            echo S::TAB;
            echo '<tr>' . PHP_EOL;
            echo S::TAB . S::TAB;
            echo '<th class="query">';
        }

        echo S::HEADER_SEARCH;

        if ($newDesign) {
            if ($scaleInfo->getType() != ScaleType::DP) {
                echo '<span class="div-help for-header material-icons color--grey cursor-help" id="search_help">help</span>' . PHP_EOL;

                echo S::TAB . S::TAB;
                echo '</h5>' . PHP_EOL . PHP_EOL;

                echo S::TAB . S::TAB;
                /** @noinspection HtmlUnknownAttribute */
                echo '<div class="mdl-tooltip mdl-tooltip--large" for="search_help">' . PHP_EOL;
                echo S::TAB . S::TAB . S::TAB;

                echo '<span>' . S::HELP_SEARCH . '</span>' . PHP_EOL;

                echo S::TAB . S::TAB;
                echo '</div>' . PHP_EOL . PHP_EOL;
            } else {
                echo S::TAB . S::TAB;
                echo '</h5>' . PHP_EOL . PHP_EOL;
            }
        } else {
            echo '</th>' . PHP_EOL;
            echo S::TAB;
            echo '</tr>' . PHP_EOL . PHP_EOL;
        }

        if (!$newDesign) {
            echo S::TAB;
            echo '<tr>' . PHP_EOL;
            echo S::TAB . S::TAB;
            echo '<td class="query">' . PHP_EOL;
        }

        if ($scaleInfo->getType() == ScaleType::DEFAULT_TYPE ||
            $scaleInfo->getType() == ScaleType::WMR
        ) {
            echoInput($newDesign, ParamName::VAN_NUMBER, S::INPUT_VAN_NUMBER, S::INPUT_VAN_NUMBER_HELP,
                S::INPUT_VAN_NUMBER_PATTERN, 10, 8);
        } elseif ($scaleInfo->getType() == ScaleType::AUTO) {
            echoInput($newDesign, ParamName::VAN_NUMBER, S::INPUT_AUTO_NUMBER, S::INPUT_AUTO_NUMBER_HELP,
                S::INPUT_AUTO_NUMBER_PATTERN, 10, 9);
        }

        echo S::TAB . S::TAB;
        echo '<br>' . PHP_EOL;

        if ($scaleInfo->getType() == ScaleType::DEFAULT_TYPE ||
            $scaleInfo->getType() == ScaleType::WMR ||
            $scaleInfo->getType() == ScaleType::AUTO
        ) {
            echoInput($newDesign, ParamName::CARGO_TYPE, S::INPUT_CARGO_TYPE, "", "", "", "");
            echo S::TAB . S::TAB;
            echo '<br>' . PHP_EOL;

            echoInput($newDesign, ParamName::INVOICE_NUM, S::INPUT_INVOICE_NUM, "", "", 20, 16);
            echo S::TAB . S::TAB;
            echo '<br>' . PHP_EOL;

            echoInput($newDesign, ParamName::INVOICE_SUPPLIER, S::INPUT_INVOICE_SUPPLIER, "", "", 20, "");
            echo S::TAB . S::TAB;
            echo '<br>' . PHP_EOL;

            echoInput($newDesign, ParamName::INVOICE_RECIPIENT, S::INPUT_INVOICE_RECIPIENT, "", "", 20, "");
            echo S::TAB . S::TAB;
            echo '<br>' . PHP_EOL;

            if (!$newDesign) {
                echo S::TAB . S::TAB;
                echo '<small>' . S::HELP_SEARCH_OLD . '</small>' . PHP_EOL;
                echo S::TAB . S::TAB;
                echo '<br>' . PHP_EOL;
            }
        } elseif ($scaleInfo->getType() == ScaleType::DP) {
            echoCheckBox($newDesign, ParamName::ONLY_CHARK, S::CHECKBOX_ONLY_CHARK, true);
        }

        if ($scales == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
            echoInput($newDesign, ParamName::SCALES, S::INPUT_SCALES, S::INPUT_SCALES_HELP, S::INPUT_SCALES_PATTERN, "", "");

            if ($newDesign) {
                echo S::TAB . S::TAB;
                echo '<span class="div-help material-icons color--grey cursor-help" id="scales_find_help">help</span>' . PHP_EOL;

                echo S::TAB . S::TAB;
                /** @noinspection HtmlUnknownAttribute */
                echo '<div class="mdl-tooltip mdl-tooltip--large" for="scales_find_help">' . PHP_EOL;
                echo S::TAB . S::TAB . S::TAB;
                echo '<span>' . S::HELP_SCALES . '</span>' . PHP_EOL;
                echo S::TAB . S::TAB;
                echo '</div>' . PHP_EOL . PHP_EOL;
            } else {
                echo S::TAB . S::TAB;
                echo '<br>' . PHP_EOL;
                echo S::TAB . S::TAB;
                echo '<small>' . S::HELP_SCALES_OLD . '</small>';
            }
        }
    }

    /**
     * Поле "Настройки".
     */
    if ($newDesign) {
        echo PHP_EOL . S::TAB . S::TAB;
        echo '<h5>';
        echo '<span class="material-icons color--grey material-icons--for-header">settings</span>';
    } else {
        echo PHP_EOL . S::TAB . S::TAB;
        echo '</td>' . PHP_EOL;
        echo S::TAB;
        echo '</tr>' . PHP_EOL . PHP_EOL;

        echo S::TAB;
        echo '<tr>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<th class="query">';
    }

    echo S::HEADER_SETTINGS;

    if ($newDesign) {
        echo '</h5>' . PHP_EOL . PHP_EOL;
    } else {
        echo '</th>' . PHP_EOL;
        echo S::TAB;
        echo '</tr>' . PHP_EOL . PHP_EOL;
    }

    if (!$newDesign) {
        echo S::TAB;
        echo '<tr>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<td class="query">' . PHP_EOL;
    }

    // TODO: delete
//    if ($scales == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
//        echoCheckBox($newDesign, ParamName::ORDER_BY_DATETIME, S::CHECKBOX_ORDER_BY_DATETIME);
//    }

    echoCheckBox($newDesign, ParamName::ALL_FIELDS, S::CHECKBOX_ALL_FIELDS);

    if ($scaleInfo->getType() == ScaleType::DEFAULT_TYPE ||
        $scaleInfo->getType() == ScaleType::WMR
    ) {
        echo PHP_EOL;
        echoCheckBox($newDesign, ParamName::SHOW_CARGO_DATE, S::CHECKBOX_SHOW_CARGO_DATE);

        echo PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<h6>' . S::HEADER_COMPARE . '</h6>';
        echo PHP_EOL;

        echoCheckBox($newDesign, ParamName::COMPARE_FORWARD, S::CHECKBOX_COMPARE_FORWARD);

        echoCheckBox($newDesign, ParamName::COMPARE_BY_BRUTTO, S::CHECKBOX_COMPARE_BY_BRUTTO);
    }

    echo PHP_EOL;

    if ($newDesign) {
        echo S::TAB;
        echo '</div> <!-- mdl-cell -->' . PHP_EOL;
        echo S::TAB;
        echo '</div> <!-- mdl-grid -->' . PHP_EOL;
    } else {
        echo S::TAB . S::TAB;
        echo '</td>' . PHP_EOL;
        echo S::TAB;
        echo '</tr>' . PHP_EOL . PHP_EOL;

        echo S::TAB;
        echo '<tr>' . PHP_EOL;
        echo S::TAB . S::TAB;
        echo '<td class="query text-align--center">' . PHP_EOL;

        echoButtonReset($newDesign);

        echo S::TAB . S::TAB;
        echo '</td>' . PHP_EOL;
        echo S::TAB;
        echo '</tr>' . PHP_EOL;

        echo S::TAB;
        echo '</table>' . PHP_EOL;
    }

    echoFormEnd();

    echo '<script type="text/javascript">';
    echo PHP_EOL;
    echo S::TAB;
    echo "setInputs(" . PHP_EOL .
        S::TAB . S::TAB .
        "'" . ParamName::DATETIME_START_DAY . "', " .
        "'" . ParamName::DATETIME_START_MONTH . "', " .
        "'" . ParamName::DATETIME_START_YEAR . "', " .
        "'" . ParamName::DATETIME_START_HOUR . "', " .
        "'" . ParamName::DATETIME_START_MINUTES . "', "
        . PHP_EOL .
        S::TAB . S::TAB .
        "'" . ParamName::DATETIME_END_DAY . "', " .
        "'" . ParamName::DATETIME_END_MONTH . "', " .
        "'" . ParamName::DATETIME_END_YEAR . "', " .
        "'" . ParamName::DATETIME_END_HOUR . "', " .
        "'" . ParamName::DATETIME_END_MINUTES . "'" .
        ");";
    echo PHP_EOL;
    echo '</script>';
    echo PHP_EOL;
} else {
    echoErrorPage($resultMessage->getError(), $resultMessage->getErrorDetails());
}

echoEndContent();

echoFooter($newDesign);

echoEndMain($newDesign);

echoEndBody($newDesign, "updateContentMinHeightOnEndBody();");

echoEndPage();