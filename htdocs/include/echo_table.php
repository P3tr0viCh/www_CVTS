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

use JetBrains\PhpStorm\Pure;
use Strings as S;

#[Pure] function getRowColorClass(bool $odd): string
{
    return $odd ? "row-color--odd" : "row-color--even";
}

#[Pure] function getCellWarningColor(?float $value, float $yellowValue, float $redValue): ?string
{
    if (is_null($value) || !is_numeric($value)) return null;

    $value = abs($value);

    if ($value >= $redValue)        return 'color--red';
    elseif ($value >= $yellowValue) return 'color--yellow';
    else                            return null;
}

#[Pure] function formatAttr(string $attr, ?string $value): ?string
{
    return $value ? " $attr='$value'" : null;
}

#[Pure] function formatClass(?string $class): ?string
{
    return formatAttr("class", $class);
}

#[Pure] function formatColSpan(?string $colSpan): ?string
{
    return formatAttr("colspan", $colSpan);
}

function echoTableStart(?string $class = null)
{
    $class = formatClass($class);
    echo "<table$class>" . PHP_EOL;
}

function echoTableHeadStart(?string $class = null)
{
    $class = formatClass($class);

    $id = " id='tableHead'";

    echo "<thead$id$class>" . PHP_EOL;
}

function echoTableTRStart(?string $class = null, ?string $onClick = null)
{
    $class = formatClass($class);
    $onClick = formatAttr("onclick", $onClick);

    echo S::TAB;
    echo "<tr$class$onClick>" . PHP_EOL;
}

function echoTableTH(string $text, ?string $class = null, ?int $colSpan = null, ?string $title = null)
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

function echoTableBodyStart(?string $class = null)
{
    $class = formatClass($class);

    $id = " id='tableBody'";

    echo "<tbody$id$class>" . PHP_EOL;
}

function echoTableTD(string $text, ?string $class = null, ?string $href = null, ?int $colSpan = null)
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