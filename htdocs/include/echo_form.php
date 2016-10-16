<?php
require_once "Strings.php";

use Strings as S;

/**
 * @param string $name
 * @param string $href
 * @param bool $blank
 */
function echoFormStart($name, $href, $blank = false)
{
    if ($blank) {
        $blank = " target='_blank'";
    }
    $params = "action='$href'$blank ";

    echo "<form id='$name' name='$name' method='post' $params>" . PHP_EOL;
}

/**
 * @param string $name
 * @param mixed $value
 */
function echoHidden($name, $value)
{
    echo S::TAB;
    echo "<input type='hidden' id='$name' name='$name' value='$value'>" . PHP_EOL;
}

/**
 * @param $newDesign
 * @param $text
 * @param $name
 * @param $value
 */
function echoButton($newDesign, $text, $name, $value)
{
    $buttonClass = $newDesign ?
        " class='mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect'" : null;

    echo S::TAB . S::TAB;
    echo "<button$buttonClass name='$name' value='$value'>";
    echo $text;
    echo "</button>";
    echo PHP_EOL . S::TAB . S::TAB;
    echo "<br>";

    echo PHP_EOL;
}

function echoButtonReset()
{
    echo S::TAB . S::TAB;
    echo "<button type='reset'>";
    echo S::BUTTON_CLEAR;
    echo "</button>";

    echo PHP_EOL;
}

/**
 * @param bool $newDesign
 * @param string $id
 * @param string $name
 * @param string|null $title
 * @param string $pattern
 * @param int $size
 * @param int $maxLength
 * @param bool $width80
 * @param bool $showName
 */
function echoInput($newDesign, $id, $name, $title, $pattern, $size, $maxLength, $width80 = false, $showName = true)
{
    echo S::TAB . S::TAB;
    if ($newDesign) {
        $width80 = $width80 ? ' input-width80' : '';
        $pattern = $pattern ? " pattern='$pattern'" : '';
        $title = $title ? " title='$title'" : '';

        echo "<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label$width80'>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        $params = "type='search' id='$id' name='$id'$pattern$title";
        echo "<input class='mdl-textfield__input' $params/>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo "<label class='mdl-textfield__label' for='$id'>$name</label>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '<span class="mdl-textfield__error">' . S::ERROR_ERROR . '</span>' . PHP_EOL;
    } else {
        echo "<div class='field--text-input'>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        $class = $showName ? " class='input-label-width'" : null;
        echo "<label$class for='$id'>";
        if ($showName) {
            echo $name;
        }
        echo '</label>' . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo "<input type='text' id='$id' name='$id' size='$size' maxlength='$maxLength'>" . PHP_EOL;
    }
    echo S::TAB . S::TAB;
    echo '</div>' . PHP_EOL;
}

/**
 * @param bool $newDesign
 * @param string $id
 * @param string $name
 * @param bool $checked
 */
function echoCheckBox($newDesign, $id, $name, $checked = false)
{
    if ($newDesign) {
        $labelClass = 'mdl-switch mdl-js-switch mdl-js-ripple-effect';
        $inputClass = 'mdl-switch__input';
        $spanClass = 'mdl-switch__label';
    } else {
        $labelClass = 'checkbox';
        $inputClass = 'checkbox__input';
        $spanClass = 'checkbox__label';
    }

    $params = "class='$inputClass'";
    if ($checked) {
        $params .= ' checked';
    }

    echo S::TAB . S::TAB;
    echo "<div class='field--checkbox'>" . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB;
    echo "<label class='$labelClass'>" . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB . S::TAB;
    echo "<input type='checkbox' name='$id' $params>" . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB . S::TAB;
    echo "<span class='$spanClass'>$name</span>" . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB;
    echo "</label>" . PHP_EOL;
    echo S::TAB . S::TAB;
    echo "</div>" . PHP_EOL;
}

function echoFormEnd()
{
    echo "</form>" . PHP_EOL . PHP_EOL;
}