<?php

namespace QueryBuilder;

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
            echo "<span class='OK'>OK</span>: $result" . PHP_EOL;
        } else {
            $this->failureCount++;

            echo "<span class='FAIL'>FAIL</span> on line $line:<br>" . PHP_EOL;
            echo "expected:<br>" . PHP_EOL;
            echo "$expected<br>" . PHP_EOL;
            echo "received:<br>" . PHP_EOL;

            $failCharIndex = $this->getFailCharIndex($result, $expected);
            if ($failCharIndex >= strlen($result)) {
                echo $result . "<b>[unexpected end]</b>";
            } else {
                $failChar = $result[$failCharIndex];
                if ($failChar == " ") {
                    $failChar = "[unexpected space]";
                }

                echo substr($result, 0, $failCharIndex) .
                    "<b>" . $failChar . "</b>" . substr($result, $failCharIndex + 1);
            }
            echo "<br>" . PHP_EOL;
        }
    }

    function test()
    {
        $queryBuilder = Builder::getInstance();

        $this->assertEquals(
            $queryBuilder->build(),
            "SELECT *", __LINE__);

        $this->assertEquals(
            $queryBuilder->table("xxx")->build(),
            "SELECT * FROM xxx", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->column("column_1")
                ->column("column_2")
                ->column("column_2", null, "column_2_alias")
                ->column("")
                ->table("yyy")
                ->table("xxx")
                ->build(),
            "SELECT column_1, column_2, column_2 AS column_2_alias FROM xxx", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->column("column_1", "xxx")
                ->column("column_2", "yyy")
                ->column("column_3")
                ->table("zzz")
                ->build(),
            "SELECT xxx.column_1, yyy.column_2, column_3 FROM zzz", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->table("xxx")
                ->where("column_null", Builder::COMPARISON_EQUAL, null)
                ->where("column_str", Builder::COMPARISON_EQUAL, "123")
                ->where("column_str_empty", Builder::COMPARISON_EQUAL, "")
                ->where("column_int", Builder::COMPARISON_LESS, 123)
                ->where("column_bool", Builder::COMPARISON_EQUAL, true)
                ->where("column_float", Builder::COMPARISON_GREATER_OR_EQUAL, 1.234)
                ->build(),
            "SELECT * FROM xxx WHERE " .
            "column_str = '123' AND " .
            "column_str_empty = '' AND " .
            "column_int < 123 AND " .
            "column_bool = TRUE AND " .
            "column_float >= 1.234", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->table("xxx")
                ->where("column_like", Builder::COMPARISON_LIKE, "%qwerty%")
                ->where("column_in_null", Builder::COMPARISON_IN, null)
                ->where("column_in_num", Builder::COMPARISON_IN, "123,456.789")
                ->where("column_in_str", Builder::COMPARISON_IN, "qwerty, '456', \"\", '\"', '''")
                ->build(),
            "SELECT * FROM xxx WHERE " .
            "column_like LIKE '%qwerty%' AND " .
            "column_in_num IN (123, 456.789) AND " .
            "column_in_str IN ('qwerty', '456', '', '\\\"', '\'')", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->table("xxx")
                ->where("column_1", Builder::COMPARISON_LIKE, "'%qwerty%'")
                ->where("column_2", Builder::COMPARISON_EQUAL, "';'qwerty" . PHP_EOL)
                ->build(),
            "SELECT * FROM xxx WHERE " .
            "column_1 LIKE '\'%qwerty%\'' AND " .
            "column_2 = '\';\'qwerty\\r\\n'", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->table("xxx")
                ->order("column_asc")
                ->order("column_desc", true)
                ->order("column_asc_collate", false, "latin1_bin")
                ->order("column_desc_collate", true, "latin1_bin")
                ->build(),
            "SELECT * FROM xxx ORDER BY " .
            "column_asc, " .
            "column_desc DESC, " .
            "column_asc_collate COLLATE latin1_bin, " .
            "column_desc_collate COLLATE latin1_bin DESC", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->table("xxx")
                ->group("column_1")
                ->group("column_1")
                ->build(),
            "SELECT * FROM xxx GROUP BY column_1", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->table("xxx")
                ->limit(1)
                ->build(),
            "SELECT * FROM xxx LIMIT 1", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->params(Builder::SELECT_SQL_BUFFER_RESULT)
                ->table("xxx")
                ->build(),
            "SELECT SQL_BUFFER_RESULT * FROM xxx", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->table("xxx")
                ->join("yyy", "column_1")
                ->build(),
            "SELECT * FROM xxx LEFT JOIN yyy USING (column_1)", __LINE__);

        $this->assertEquals(
            $queryBuilder
                ->clear()
                ->table("xxx")
                ->join("yyy", array("column_1", "column_2", null))
                ->build(),
            "SELECT * FROM xxx LEFT JOIN yyy USING (column_1, column_2)", __LINE__);
    }
}

echo "<head>" . PHP_EOL;
echo "<title>" . PHP_EOL;
echo "Query Builder test" . PHP_EOL;
echo "</title>" . PHP_EOL;

echo "<style>" . PHP_EOL;
echo ".OK {". PHP_EOL;
echo "color: forestgreen;". PHP_EOL;
echo "font-weight: bold". PHP_EOL;
echo "}". PHP_EOL;
echo ".FAIL {". PHP_EOL;
echo "color: red;". PHP_EOL;
echo "font-weight: bold". PHP_EOL;
echo "}". PHP_EOL;
echo "</style>" . PHP_EOL;
echo "</head>" . PHP_EOL;

echo "<body>" . PHP_EOL;

echo "Start" . PHP_EOL;

$builderTest = new BuilderTest();
$builderTest->test();

$failureCount = $builderTest->getFailureCount();

echo "<p>";
if ($failureCount == 0) {
    echo "<span class='OK'>All tests OK</span>";
} else {
    echo "<span class='FAIL'>FAILURES</span>: $failureCount";
}

echo "<p>";
echo "End" . PHP_EOL;

echo "</body>";