<?php
require_once "QueryScaleInfo.php";

require_once "Strings.php";
require_once "Constants.php";

require_once "ResultMessage.php";

use database\Columns;

class ScaleInfo
{
    private int $scaleNum;
    private string $place;
    private string $header;
    private int $type;
    private int $class;

    public function __construct(int $scaleNum)
    {
        $this->scaleNum = $scaleNum;
    }

    public function query(mysqli $mysqli): ?ResultMessage
    {
        switch ($this->scaleNum) {
            case Constants::SCALE_NUM_ALL_TRAIN_SCALES:
                $this->place = Strings::SCALE_INFO_ALL_TRAIN_PLACE;
                $this->header = Strings::SCALE_INFO_ALL_TRAIN_HEADER;
                $this->type = ScaleType::DEFAULT_TYPE;
                $this->class = ScaleClass::CLASS_DYNAMIC_AND_STATIC;

                return null;

            case Constants::SCALE_NUM_REPORT_VANLIST:
                $this->place = Strings::SCALE_INFO_VANLIST_PLACE;
                $this->header = Strings::SCALE_INFO_VANLIST_PLACE_HEADER;
                $this->type = ScaleType::VANLIST;
                $this->class = ScaleClass::CLASS_DYNAMIC_AND_STATIC;

                return null;

            case Constants::SCALE_NUM_REPORT_IRON:
                $this->place = Strings::SCALE_INFO_IRON_PLACE;
                $this->header = Strings::SCALE_INFO_IRON_HEADER;
                $this->type = ScaleType::IRON;
                $this->class = ScaleClass::CLASS_DYNAMIC_AND_STATIC;

                return null;
            case Constants::SCALE_NUM_REPORT_IRON_CONTROL:
                $this->place = Strings::SCALE_INFO_IRON_PLACE;
                $this->header = Strings::SCALE_INFO_IRON_CONTROL_HEADER;
                $this->type = ScaleType::IRON_CONTROL;
                $this->class = ScaleClass::CLASS_DYNAMIC_AND_STATIC;

                return null;

            default:
                if ($mysqli->connect_errno) {
                    return connectionError($mysqli);
                } else {
                    $query = new QueryScaleInfo();
                    $query->setScaleNum($this->scaleNum);
                    $result = $mysqli->query($query->getQuery());

                    if ($result->num_rows > 0) {
                        if ($row = $result->fetch_array()) {
                            $this->place = (string)latin1ToUtf8($row[Columns::SCALE_PLACE]);

                            $this->header = sprintf(Strings::SCALE_INFO_HEADER, $this->place, $this->scaleNum);
                            $this->type = $row[database\Columns::SCALE_TYPE];

                            if ($row[database\Columns::SCALE_TYPE_DYN] == 0) {
                                $this->class = ScaleClass::CLASS_STATIC;
                            } else {
                                $this->class = ScaleClass::CLASS_DYNAMIC;
                            }

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

    public function getScaleNum(): int
    {
        return $this->scaleNum;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getClass(): int
    {
        return $this->class;
    }
}