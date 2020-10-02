<?php
/**
 * Функции, создающие шаблон таблицы.
 *
 * Использование:
 * echoTableStart --            <TABLE>.
 *     echoTableHeadStart --        <THEAD>.
 *         echoTableTRStart --          <TR>.
 *             echoTableTH --               <TH>..</TH>.
 *         echoTableTREnd --            </TR>.
 *     echoTableHeadEnd --          </THEAD>.
 *     echoTableBodyStart --        <TBODY>.
 *         echoTableTRStart --          <TR>.
 *             echoTableTD --               <TD>..</TD>.
 *         echoTableTREnd --            </TR>.
 *         echoTableTRStart --          <TR>.
 *             echoTableTD --               <TD>..</TD>.
 *         echoTableTREnd --            </TR>.
 *         ...
 *     echoTableBodyEnd --          </TBODY>.
 * echoTableEnd --              </TABLE>.
 */

require_once "Strings.php";

use Strings as S;

/**
 * @param bool $odd
 * @return string
 */
function getRowColorClass($odd)
{
    return $odd ? "row-color--odd" : "row-color--even";
}

/**
 * @param float $value
 * @param float $yellowValue
 * @param float $redValue
 * @return null|string
 */
function getCellWarningColor($value, $yellowValue, $redValue)
{
    $value = abs($value);

    if ($value >= $redValue) {
        return 'color--red';
    }

    if ($value >= $yellowValue) {
        return 'color--yellow';
    }

    return null;
}

/**
 * @param string $attr
 * @param null|string $value
 * @return null|string
 */
function formatAttr($attr, $value)
{
    return $value ? " $attr='$value'" : null;
}

/**
 * @param null|string $class
 * @return null|string
 */
function formatClass($class)
{
    return formatAttr("class", $class);
}

/**
 * @param null|string $colSpan
 * @return null|string
 */
function formatColSpan($colSpan)
{
    return formatAttr("colspan", $colSpan);
}

/**
 * @param null|string $class
 */
function echoTableStart($class = null)
{
    $class = formatClass($class);
    echo "<table$class>" . PHP_EOL;
}

/**
 * @param null|string $class
 */
function echoTableHeadStart($class = null)
{
    $class = formatClass($class);

    $id = " id='tableHead'";

    echo "<thead$id$class>" . PHP_EOL;
}

/**
 * @param null|string $class
 * @param null|string $onClick
 */
function echoTableTRStart($class = null, $onClick = null)
{
    $class = formatClass($class);
    $onClick = formatAttr("onclick", $onClick);

    echo S::TAB;
    echo "<tr$class$onClick>" . PHP_EOL;
}

/**
 * @param string $text
 * @param null|string $class
 * @param null|string $colSpan
 * @param null|string $title
 */
function echoTableTH($text, $class = null, $colSpan = null, $title = null)
{
    $class = formatClass($class);
    $colSpan = formatColSpan($colSpan);

    echo S::TAB . S::TAB;
    $title = formatAttr("title", $title);
    echo "<th$class$colSpan$title>";
    echo $text;
    echo "</th>";
    echo PHP_EOL;
}

function echoTableTREnd()
{
    echo S::TAB;
    echo "</tr>" . PHP_EOL;
}

function echoTableHeadEnd()
{
    echo "</thead>" . PHP_EOL . PHP_EOL;
}

/**
 * @param null|string $class
 */
function echoTableBodyStart($class = null)
{
    $class = formatClass($class);

    $id = " id='tableBody'";

    echo "<tbody$id$class>" . PHP_EOL;
}

/**
 * @param string $text
 * @param null|string $class
 * @param null|string $href
 * @param null|string $colSpan
 */
function echoTableTD($text, $class = null, $href = null, $colSpan = null)
{
    $class = formatClass($class);
    if ($href) {
        $text = "<a href='$href' target='_self'>$text</a>";
    }
    $colSpan = formatColSpan($colSpan);

    echo S::TAB . S::TAB;
    echo "<td$class$colSpan>";
    echo $text;
    echo "</td>";
    echo PHP_EOL;
}

function echoTableBodyEnd()
{
    echo "</tbody>" . PHP_EOL;
}

function echoTableEnd()
{
    echo "</table>" . PHP_EOL . PHP_EOL;
}