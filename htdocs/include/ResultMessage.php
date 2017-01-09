<?php
require_once "Strings.php";

class ResultMessage
{
    private $error;
    private $errorDetails;

    /**
     * ResultMessage constructor.
     * @param string $error
     * @param string|null $errorDetails
     */
    public function __construct($error, $errorDetails)
    {
        $this->error = $error;
        $this->errorDetails = $errorDetails;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return null|string
     */
    public function getErrorDetails()
    {
        return $this->errorDetails;
    }
}

function mysqlConnectionFileError() {
    return new ResultMessage(
        Strings::ERROR_MYSQL_CONNECTION,
        sprintf(Strings::ERROR_MYSQL_DETAILS, 404, Strings::ERROR_MYSQL_CONNECTION_FILE_ERROR));
}

/**
 * @param mysqli $mysqli
 * @internal param mysqli $mysqli
 * @return ResultMessage
 */
function connectionError($mysqli)
{
    return new ResultMessage(
        Strings::ERROR_MYSQL_CONNECTION,
        sprintf(Strings::ERROR_MYSQL_DETAILS, $mysqli->connect_errno, latin1ToUtf8($mysqli->connect_error)));
}

/**
 * @param mysqli $mysqli
 * @internal param mysqli $mysqli
 * @return ResultMessage
 */
function queryError($mysqli)
{
    return new ResultMessage(
        Strings::ERROR_MYSQL_QUERY,
        // TODO:  == 1?
        $mysqli->errno == 1 ?
            Strings::ERROR_MYSQL_MAX_LIMIT :
            sprintf(Strings::ERROR_MYSQL_DETAILS, $mysqli->errno, latin1ToUtf8($mysqli->error)));
}