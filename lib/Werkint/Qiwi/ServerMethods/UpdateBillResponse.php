<?php
namespace Werkint\Qiwi\ServerMethods;

class UpdateBillResponse
{
    /** @var string */
    public $login;

    /** @var string */
    public $password;

    /** @var string */
    public $txn;

    /** @var int */
    public $status;
}