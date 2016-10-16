<?php
require_once "QueryScaleInfo.php";

require_once "Strings.php";
require_once "Constants.php";

require_once "Database.php";

require_once "ResultMessage.php";

class ScaleInfo
{
    /**
     * @var int
     */
    private $scaleNum;

    /**
     * @var string
     */
    private $place;

    /**
     * @var string
     */
    private $header;

    /**
     * @var int
     */
    private $type;

    /**
     * @var int
     */
    private $class;

    /**
     * ScaleInfo constructor.
     * @param int $scaleNum
     */
    public function __construct($scaleNum)
    {
        $this->scaleNum = $scaleNum;
    }

    /**
     * @param mysqli $mysqli
     * @return null|ResultMessage
     */
    public function query($mysqli)
    {
        if ($this->scaleNum == Constants::SCALE_NUM_ALL_TRAIN_SCALES) {
            $this->place = Strings::SCALE_INFO_ALL_TRAIN_PLACE;
            $this->header = Strings::SCALE_INFO_ALL_TRAIN_HEADER;
            $this->type = ScaleType::DEFAULT_TYPE;
            $this->class = ScaleClass::CLASS_DYNAMIC_AND_STATIC;

            return null;
        } else {
            if ($mysqli->connect_errno) {
                return connectionError($mysqli);
            } else {
                $query = new QueryScaleInfo();
                $query->setScaleNum($this->scaleNum);
                $result = $mysqli->query($query->getQuery());

                if ($result->num_rows > 0) {
                    if ($row = $result->fetch_array()) {
                        $this->place = $row[\Database\Columns::SCALE_PLACE];

                        $staticClass = $row[Database\Columns::SCALE_CLASS_STATIC];
                        $dynamicClass = $row[Database\Columns::SCALE_CLASS_DYNAMIC];

                        if ($staticClass && $dynamicClass) {
                            $this->class = ScaleClass::CLASS_DYNAMIC_AND_STATIC;
                        } elseif (!$staticClass && $dynamicClass) {
                            $this->class = ScaleClass::CLASS_DYNAMIC;
                        } elseif ($staticClass && !$dynamicClass) {
                            $this->class = ScaleClass::CLASS_STATIC;
                        }

                        $this->header = sprintf(Strings::SCALE_INFO_HEADER, $this->place, $this->scaleNum);
                        $this->type = $row[Database\Columns::SCALE_TYPE];

                        return null;
                    } else {
                        return queryError($mysqli);
                    }
                } else {
                    return new ResultMessage(sprintf(Strings::ERROR_MYSQL_BAD_SCALE_NUM, $this->scaleNum), null);
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getScaleNum()
    {
        return $this->scaleNum;
    }

    /**
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getClass()
    {
        return $this->class;
    }
}