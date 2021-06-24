<?php
require_once "QueryScaleInfo.php";

require_once "Strings.php";
require_once "Constants.php";

require_once "ResultMessage.php";

use database\Columns;

class ScaleInfo
{
    private int $scaleNum;
    private string $place = "";
    private string $header = "";
    private int $type;
    private int $class;

    private int $sensorsMCount = 0;
    private int $sensorsTCount = 0;

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
            case Constants::SCALE_NUM_REPORT_SLAG_CONTROL:
                $this->place = Strings::SCALE_INFO_SLAG_PLACE;
                $this->header = Strings::SCALE_INFO_SLAG_CONTROL_HEADER;
                $this->type = ScaleType::SLAG_CONTROL;
                $this->class = ScaleClass::CLASS_DYNAMIC_AND_STATIC;

                return null;
            case Constants::SCALE_NUM_REPORT_SENSORS_INFO:
                $this->place = Strings::SCALE_INFO_SENSORS_INFO_PLACE;
                $this->header = Strings::SCALE_INFO_SENSORS_INFO_HEADER;
                $this->type = ScaleType::DEFAULT_TYPE;
                $this->class = ScaleClass::CLASS_DYNAMIC;

                return null;
            default:
                if ($mysqli->connect_errno) {
                    return connectionError($mysqli);
                } else {
                    $query = new QueryScaleInfo();
                    $query->setScaleNum($this->scaleNum);
                    $result = $mysqli->query($query->getQuery());

                    if ($result) {
                        if ($result->num_rows > 0) {
                            if ($row = $result->fetch_array()) {
                                $this->place = (string)latin1ToUtf8($row[Columns::SCALE_PLACE]);

                                $this->header = sprintf(Strings::SCALE_INFO_HEADER, $this->place, $this->scaleNum);

                                $this->type = match ((int)$row[Columns::SCALE_WTYPE]) {
                                    // Значения в таблице lst_wtype
                                    1 => ScaleType::AUTO,
                                    2 => ScaleType::DP,
                                    3 => ScaleType::KANAT,
                                    default => $row[Columns::SCALE_TYPE],
                                };

                                $this->sensorsMCount = (int)$row[Columns::SCALE_SENSORS_M_COUNT];
                                $this->sensorsTCount = (int)$row[Columns::SCALE_SENSORS_T_COUNT];

                                $this->class = match ((int)$row[Columns::SCALE_TYPE_DYN]) {
                                    // Значения в таблице lst_typedyn
                                    0 => ScaleClass::CLASS_STATIC,
                                    1 => ScaleClass::CLASS_DYNAMIC,
                                    default => ScaleClass::CLASS_DYNAMIC_AND_STATIC,
                                };

                                return null;
                            } else {
                                return queryError($mysqli);
                            }
                        } else {
                            return new ResultMessage(sprintf(Strings::ERROR_MYSQL_BAD_SCALE_NUM, $this->scaleNum), null);
                        }
                    } else {
                        return queryError($mysqli);
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

    public function getSensorsMCount(): int
    {
        return $this->sensorsMCount;
    }

    public function getSensorsTCount(): int
    {
        return $this->sensorsTCount;
    }
}