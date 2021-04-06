<?php
require_once "Strings.php";

use Strings as S;

function echoFormStart(string $name, string $href, ?string $onSubmit = null, ?string $onReset = null, bool $methodGet = true, bool $blank = false)
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

    $params = concatStrings($blank, concatStrings($onSubmit, $onReset, Strings::SPACE), Strings::SPACE);

    $method = $methodGet ? 'get' : 'post';

    echo "<form id='$name' name='$name' method='$method' action='$href' $params>" . PHP_EOL;
}

function echoHidden(string $name, mixed $value)
{
    $id = $name . '_hidden';

    echo S::TAB;
    echo "<input type='hidden' id='$id' name='$name' value='$value'>" . PHP_EOL;
}

function echoButton(bool $newDesign, string $text, string $name, int $value, ?string $class = null)
{
    $buttonClass = $newDesign ?
        "mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" : "input-button submit";

    if ($class) {
        $buttonClass .= Strings::SPACE . $class;
    }

    echo S::TAB . S::TAB;
    if ($newDesign) {
        echo "<button class='$buttonClass' name='$name' value='$value'>";
        echo $text;
        echo "</button>";
    } else {
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

function echoInput(bool $newDesign, string $id, string $name, ?string $title, string $pattern, ?int $size, ?int $maxLength,
                   bool $width80 = false, bool $showName = true)
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

function echoTextArea(bool $newDesign, string $id, string $name, ?string $title)
{
    $cols = 40;
    $rows = 20;

    echo S::TAB;

    echoHidden($id, null);

    echo S::TAB . S::TAB;

    if ($newDesign) {
        $title = $title ? " title='$title'" : '';

        $params = $title;

        echo "<div class='mdl-textfield mdl-js-textfield mdl-textfield--floating-label'>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo "<textarea type='text' class='mdl-textfield__input' id='$id' $params cols='$cols' rows='$rows'></textarea>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo "<label for='$id' class='mdl-textfield__label'>$name</label>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo '<span class="mdl-textfield__error">' . S::ERROR_ERROR . '</span>' . PHP_EOL;
    } else {
        echo "<div class='text-area__field'>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        $labelClass = 'text-area__label';

        $labelClass .= ' text-area__label--width';

        echo "<label for='$id' class='$labelClass'>";

        echo "<span>$name</span>";

        echo "</label><br>" . PHP_EOL;
        echo S::TAB . S::TAB . S::TAB;
        echo "<textarea class='text-area__input' id='$id' cols='$cols' rows='$rows'></textarea>" . PHP_EOL;
    }
    echo S::TAB . S::TAB;
    echo "</div>" . PHP_EOL;
}

function echoCheckBox(bool $newDesign, string $id, string $name, bool $checked = false)
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