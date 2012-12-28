<?php
namespace Werkint\Qiwi\ServerMethods;
use Werkint\Qiwi\Status\StatusBill;

class CheckBillResponse
{
    /** @var string Идентификатор платежа */
    public $id;

    /** @var string Идентификатор (номер телефона) */
    public $user;

    /** @var string Сумма */
    public $amount;

    /** @var string|\DateTime Дата (сервер возвращает в формате dd.MM.yyyy HH:mm:ss) */
    public $date;

    /** @var string|\DateTime Время жизни (в формате даты) */
    public $lifetime;

    /** @var int|StatusBill Возвращает код ошибки (0 - без ошибок) */
    public $status;
}