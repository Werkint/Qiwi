<?php
namespace IShop\Soap;
use
	IShop\ServerMethods as S,
	IShop\Exception as E,
	IShop\Status;

class Server extends \SoapServer {

	protected $classmapMethods = array(
		'tns:updateBill'         => 'UpdateBill',
		'tns:updateBillResponse' => 'UpdateBillResponse',
	);
	protected $classmap;

	protected function createDefaultClassmap() {
		$namespace = 'IShop\ServerMethods';
		$this->classmap = array();
		foreach ($this->classmapMethods as $method => $binding) {
			$this->classmap[$method] = $namespace . '\\' . $binding;
		}
	}

	protected $wsdl;
	protected $login;
	protected $password;

	public function __construct($wsdl, $login, $password) {
		$this->createDefaultClassmap();
		$this->wsdl = $wsdl;
		$this->login = $login;
		$this->password = $password;
	}

	protected $callback;

	public function processRequest(callable $callback) {
		$server = new \SoapServer(
			$this->wsdl, array('classmap' => $this->classmap)
		);
		$server->setObject($this);
		$this->callback = $callback;
		$server->handle();
		$this->callback = null;
	}

	public function updateBill(S\UpdateBillResponse $param) {
		// Проверки подписи
		if ($param->login != $this->login) {
			throw new E\LoginException('Wrong login: ' . $param->login);
		}
		$crypt = uppercase(md5($param->txn . uppercase(md5($this->password))));
		if ($param->password != $crypt) {
			throw new E\PasswordException('Wrong sign. Expected: ' . $crypt . ', got: ' . $param->password);
		}

		// Вызываем обработчик, передав расшифрованный статус
		$callback = $this->callback;
		$param = new Status\Bill($param->txn, $param->status);
		$result = $callback($param);

		// Выдаем ответ QIWI
		$ret = new S\UpdateBill();
		$ret->updateBillResult = $result;
		return $ret;
	}

}
