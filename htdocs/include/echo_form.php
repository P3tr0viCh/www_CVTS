<?php
require_once "Strings.php";

use Strings as S;

/**
 * @param string $name
 * @param string $href
 * @param null|string $onSubmit
 * @param null|string $onReset
 * @param bool $methodGet
 * @param bool $blank
 */
function echoFormStart($name, $href, $onSubmit = null, $onReset = null, $methodGet = true, $blank = false)
{
    if ($blank) {
        $blank = "target='_blank'";
    }

    if ($onSubmit) {
        $onSubmit = "onsubmit='$onSubmit'";
    }

    if ($onReset) {
        $onReset = "onreset='$onReset'";
    }

    $params = concatStrings($blank, concatStrings($onSubmit, $onReset, " "), " ");

    $method = $methodGet ? 'get' : 'post';

    echo "<form id='$name' name='$name' method='$method' action='$href' $params>" . PHP_EOL;
}

/**
 * @param string $name
 * @param mixed $value
 */
function echoHidden($name, $value)
{
    $id = $name . '_hidden';

    echo S::TAB;
    echo "<input type='hidden' id='$id' name='$name' value='$value'>" . PHP_EOL;
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
        "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" : "input-button submit";

    echo S::TAB . S::TAB;
    if ($newDesign) {
        echo "<button class='$buttonClass' name='$name' value='$value'>";
        echo $text;
        echo "</button>";
    } else {
        /** @var string $onClick */
        $onClick = "onButtonClick($value)";
        echo "<input type='button' class='$buttonClass' value='$text' onclick='$onClick'>";
    }
    echo PHP_EOL . S::TAB . S::TAB;
    echo "<br>";

    echo PHP_EOL;
}

function echoButtonReset($newDesign)
{
    $text = S::BUTTON_CLEAR;
    echo S::TAB . S::TAB;
    if ($newDesign) {
        echo "<button type='reset'>";
        echo $text;
        echo "</button>";
    } else {
        echo "<input type='reset' class='input-button reset' value='$text'>";
    }
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
    echo S::TAB;

    echoHidden($id, null);

    echo S::TAB . S::TAB;

    if ($newDesign) {
        $width80 = $width80 ? ' input-width80' : '';
        $pattern = $pattern ? " pattern='$pattern'" : '';
        $title = $title ? " title='$title'" : '';

        $params = $pattern . $title;

        echo "<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label$width80'>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo "<input type='search' class='mdl-textfield__input' id='$id' $params/>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo "<label for='$id' class='mdl-textfield__label'>$name</label>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '<span class="mdl-textfield__error">' . S::ERROR_ERROR . '</span>' . PHP_EOL;
    } else {
        echo "<div class='text-input__field'>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        $labelClass = 'text-input__label';
        if ($showName) {
            $labelClass .= ' text-input__label--width';
        }
        echo "<label for='$id' class='$labelClass'>";
        if ($showName) {
            echo "<span>$name</span>";
        }
        echo "</label>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo "<input type='text' class='text-input__input' id='$id' size='$size' maxlength='$maxLength'>" . PHP_EOL;
    }
    echo S::TAB . S::TAB;
    echo "</div>" . PHP_EOL;
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

    $params = $checked ? 'checked' : '';

    echo S::TAB . S::TAB;
    echo "<div class='field--checkbox'>" . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB;
    echo "<label for='$id' class='$labelClass'>" . PHP_EOL;
    echo S::TAB . S::TAB . S::TAB . S::TAB;
    echo "<input type='checkbox' id='$id' name='$id' class='$inputClass' $params>" . PHP_EOL;
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