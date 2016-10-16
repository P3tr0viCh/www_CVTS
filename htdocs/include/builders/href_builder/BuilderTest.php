<?php

namespace HrefBuilder;

require "Builder.php";

class BuilderTest
{

    private $failureCount = 0;

    /**
     * @return int
     */
    public function getFailureCount()
    {
        return $this->failureCount;
    }

    private function getFailCharIndex($result, $expected)
    {
        $resultLength = strlen($result);
        for ($i = 0, $l = strlen($expected); $i < $l; $i++) {
            if ($i >= $resultLength) {
                return $i;
            }
            if ($expected[$i] !== $result[$i]) {
                return $i;
            }
        }
        return $i;
    }

    private function assertEquals($result, $expected, $line)
    {
        echo "<p>";
        if ($result === $expected) {
            echo "OK: " . str_replace("&", "&amp;", $result) . PHP_EOL;
        } else {
            $this->failureCount++;

            echo "FAIL on line $line:<br>" . PHP_EOL;
            echo "expected:<br>" . PHP_EOL;
            echo str_replace("&", "&amp;", $expected);
            echo "<br>" . PHP_EOL;
            echo "received:<br>" . PHP_EOL;

            $failCharIndex = $this->getFailCharIndex($result, $expected);
            if ($failCharIndex >= strlen($result)) {
                echo str_replace("&", "&amp;", $result) . "<b>[unexpected end]</b>";
            } else {
                $failChar = $result[$failCharIndex];
                if ($failChar == " ") {
                    $failChar = "[unexpected space]";
                }

                echo str_replace("&", "&amp;", substr($result, 0, $failCharIndex)) .
                    "<b>" . $failChar . "</b>" . str_replace("&", "&amp;", substr($result, $failCharIndex + 1));
            }
            echo "<br>" . PHP_EOL;
        }
    }

    function test()
    {
        $hrefBuilder = Builder::getInstance();

        $this->assertEquals(
            $hrefBuilder
                ->setUrl("url.html")
                ->build(),
            "url.html", __LINE__);

        $this->assertEquals(
            $hrefBuilder
                ->clear()
                ->setUrl("url.html")
                ->setParam("", 123)
                ->setParam(null, 123)
                ->build(),
            "url.html", __LINE__);

        $this->assertEquals(
            $hrefBuilder
                ->clear()
                ->setParam("param", 123)
                ->build(),
            "param=123", __LINE__);

        $this->assertEquals(
            $hrefBuilder
                ->clear()
                ->setUrl("url.html")
                ->setParam("param", 123)
                ->setParam("param", 456)
                ->setParam("param", 789)
                ->build(),
            "url.html?param=789", __LINE__);

        $this->assertEquals(
            $hrefBuilder
                ->clear()
                ->setUrl("url.html")
                ->setParam("param_null", null)
                ->setParam("param_empty_str", "")
                ->setParam("param", 123)
                ->build(),
            "url.html?param_null=&param_empty_str=&param=123", __LINE__);

        $this->assertEquals(
            $hrefBuilder
                ->clear()
                ->setUrl("url.html")
                ->setParam("param_int", 123)
                ->setParam("param_float", 123.456)
                ->setParam("param_bool", false)
                ->setParam("param_str", "param")
                ->setParam("param_str_int", "параметр")
                ->build(),
            "url.html?" .
            "param_int=123&" .
            "param_float=123.456&" .
            "param_bool=false&" .
            "param_str=param&" .
            "param_str_int=" . urlencode("параметр")
            , __LINE__);
    }
}

echo "<head>" . PHP_EOL;
echo "<title>" . PHP_EOL;
echo "Href Builder test" . PHP_EOL;
echo "</title>" . PHP_EOL;
echo "</head>" . PHP_EOL;

echo "<body>" . PHP_EOL;

echo "Start" . PHP_EOL;

$builderTest = new BuilderTest();
$builderTest->test();

$failureCount = $builderTest->getFailureCount();

echo "<p>";
if ($failureCount == 0) {
    echo "All tests OK";
} else {
    echo "FAILURES: $failureCount";
}

echo "<p>";
echo "End" . PHP_EOL;

echo "</body>";