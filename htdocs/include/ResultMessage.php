<?php
require_once "Strings.php";

use JetBrains\PhpStorm\Pure;

class ResultMessage
{
    private string $error;
    private ?string $errorDetails;

    public function __construct(string $error, ?string $errorDetails = null)
    {
        $this->error = $error;
        $this->errorDetails = $errorDetails;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrorDetails(): ?string
    {
        return $this->errorDetails;
    }
}

#[Pure] function mysqlConnectionFileError(): ResultMessage
{
    return new ResultMessage(
        Strings::ERROR_MYSQL_CONNECTION,
        sprintf(Strings::ERROR_MYSQL_DETAILS, 404, Strings::ERROR_MYSQL_CONNECTION_FILE_ERROR));
}

#[Pure] function connectionError(mysqli $mysqli): ResultMessage
{
    return new ResultMessage(
        Strings::ERROR_MYSQL_CONNECTION,
        sprintf(Strings::ERROR_MYSQL_DETAILS, $mysqli->connect_errno, $mysqli->connect_error));
}

#[Pure] function queryError(mysqli $mysqli): ResultMessage
{
    return new ResultMessage(Strings::ERROR_MYSQL_QUERY,
        sprintf(Strings::ERROR_MYSQL_DETAILS, $mysqli->errno, latin1ToUtf8($mysqli->error)));
}