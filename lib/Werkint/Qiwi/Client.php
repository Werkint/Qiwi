<?php
namespace Werkint\Qiwi;
use
    Werkint\Qiwi\Soap,
    Werkint\Qiwi\ServerMethods as SM,
    Werkint\Qiwi\Status as S;

class Client
{

    const CLIENT_WS = 'IShopClientWS.wsdl';
    const SERVER_WS = 'IShopServerWS.wsdl';

    const DATE_FORMAT = 'd.m.Y H:i:s';

    protected function getResPath()
    {
        return __DIR__ . '/../../res';
    }

    protected $login;
    protected $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /** @var Soap\Client */
    protected $client;

    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new Soap\Client(
                $this->getResPath() . '/' . static::SERVER_WS
            );
        }
        return $this->client;
    }

    /** @var Soap\Server */
    protected $server;

    protected function getServer()
    {
        if (!$this->server) {
            $this->server = new Soap\Server(
                $this,
                $this->getResPath() . '/' . static::CLIENT_WS,
                $this->login,
                $this->password
            );
        }
        return $this->server;
    }

    /**
     * Обрабатывает входящий запрос
     * @param callable $callback
     */
    public function processRequest(callable $callback)
    {
        $this->getServer()->processRequest(
            $callback
        );
    }

    /**
     * Выписывает новый счет
     * @param string    $phone    Телефон (0123456789, без плюса, только Россия)
     * @param float     $amount   Сумма
     * @param string    $txn_id   Код счета
     * @param string    $comment  Комментарий
     * @param \DateTime $lifetime Время жизни счета (пусто = 30 дней), максимум - 45 дней
     * @param bool|int  $alarm    Оповещение (0 - нет, 1 - sms, 2 - звонок), платно
     * @param bool      $create   Создать ли пользователя, если он не существует
     * @return S\StatusResult
     */
    public function createBill(
        $phone, $amount, $txn_id, $comment,
        \DateTime $lifetime = null, $alarm = false, $create = true
    ) {
        $query = new SM\CreateBill();
        $query->login = $this->login;
        $query->password = $this->password;

        $query->user = $phone;
        $query->amount = (string)$amount;
        $query->comment = $comment;
        $query->txn = $txn_id;
        $query->lifetime = $lifetime ? $lifetime->format(static::DATE_FORMAT) : '';
        $query->alarm = (int)$alarm;
        $query->create = $create;

        $res = $this->getClient()->createBill($query);
        $res = new S\StatusResult($res->createBillResult);
        return $res;
    }

    /**
     * Отменяет выписанный ранее счет
     * @param string $txn_id Код счета
     * @return int
     */
    public function cancelBill($txn_id)
    {
        $query = new SM\CancelBill();
        $query->login = $this->login;
        $query->password = $this->password;

        $query->txn = $txn_id;

        $res = $this->getClient()->cancelBill($query);
        $res = $res->cancelBillResult;
        return $res;
    }

    /**
     * Проверяет состояние выписанного счета
     * @param string $txn_id
     * @return SM\CheckBillResponse Код счета
     */
    public function checkBill($txn_id)
    {
        $query = new SM\CheckBill();
        $query->login = $this->login;
        $query->password = $this->password;

        $query->txn = $txn_id;

        $res = $this->getClient()->checkBill($query);
        $res->id = $txn_id;
        $res->status = new S\StatusBill($res->status);
        $res->date = new \DateTime($res->date);
        $res->lifetime = new \DateTime($res->lifetime);
        return $res;
    }

    /**
     * Возвращает список счетов в виде массива
     * 'id'     - id счета
     * 'status' - состояние
     * @param \DateTime $dateFrom Дата начала
     * @param \DateTime $dateTo   Дата конца (максимум - 31 день)
     * @param int|bool  $status   Статус (чтобы получить все счета - 0)
     * @return array
     */
    public function getBillList(
        \DateTime $dateFrom, \DateTime $dateTo, $status = false
    ) {
        $query = new SM\GetBillList();
        $query->login = $this->login;
        $query->password = $this->password;

        $query->dateFrom = $dateFrom->format(static::DATE_FORMAT);
        $query->dateTo = $dateTo->format(static::DATE_FORMAT);
        $query->status = (int)$status;

        $res = $this->getClient()->getBillList($query);
        if ($res->count > 0) {
            $xml = new \DOMDocument();
            $xml->loadXML($res->txns);
            $xpath = new \DOMXPath($xml);
            $ret = array();
            $nodes = $xpath->query('/bills/bill');
            foreach ($nodes as $node) {
                $ret[] = array(
                    'id'     => $node->getAttribute('trm-txn-id'),
                    'status' => (int)$node->getAttribute('status'),
                );
            }
            return $ret;
        }
        return array();
    }

}
