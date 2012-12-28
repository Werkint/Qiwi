<?php
namespace Werkint\Qiwi\Soap;
use
    Werkint\Qiwi\ServerMethods as S,
    Werkint\Qiwi\Exception as E,
    Werkint\Qiwi\Client as ClientProcessor;

class Server extends \SoapServer
{

    protected $classmapMethods = array(
        'tns:updateBill'         => 'UpdateBill',
        'tns:updateBillResponse' => 'UpdateBillResponse',
    );
    protected $classmap;

    protected function createDefaultClassmap()
    {
        $namespace = 'Werkint\Qiwi\ServerMethods';
        $this->classmap = array();
        foreach ($this->classmapMethods as $method => $binding) {
            $this->classmap[$method] = $namespace . '\\' . $binding;
        }
    }

    protected $wsdl;
    protected $login;
    protected $password;

    /** @var ClientProcessor */
    protected $client;

    public function __construct(
        ClientProcessor $client, $wsdl, $login, $password
    ) {
        $this->createDefaultClassmap();
        $this->wsdl = $wsdl;
        $this->login = $login;
        $this->password = $password;
        $this->client = $client;
    }

    protected $callback;

    public function processRequest(callable $callback)
    {
        $server = new \SoapServer(
            $this->wsdl, array('classmap' => $this->classmap)
        );
        $server->setObject($this);
        $this->callback = $callback;
        $server->handle();
        $this->callback = null;
    }

    public function updateBill(S\UpdateBillResponse $param)
    {
        // Проверки подписи
        if ($param->login != $this->login) {
            throw new E\LoginException('Wrong login: ' . $param->login);
        }
        $crypt = strtoupper(md5(iconv('utf-8', 'windows-1251', $param->txn) . strtoupper(md5(iconv('utf-8', 'windows-1251', $this->password)))));
        if ($param->password != $crypt) {
            throw new E\PasswordException('Wrong sign. Expected: ' . $crypt . ', got: ' . $param->password);
        }

        // Обновляем, как рекомендует QIWI, статус
        // Это нужно для дополнительной защиты
        $param = $this->client->checkBill($param->txn);

        // Вызываем обработчик, передав расшифрованный статус
        $callback = $this->callback;
        $result = $callback($param);

        // Выдаем ответ QIWI
        $ret = new S\UpdateBill();
        $ret->updateBillResult = $result;
        return $ret;
    }

}
