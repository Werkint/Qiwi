<?php
namespace IShop;
use IShop\ServerMethods as SM;
use IShop\Status as S;

class Client {

	const CLIENT_WS = 'IShopClientWS.wsdl';
	const SERVER_WS = 'IShopServerWS.wsdl';

	protected $login;
	protected $password;

	/** @var SoapClient */
	protected $client;

	protected function getResPath() {
		return __DIR__ . '/../../res';
	}

	protected function __construct($login, $password) {
		$this->login = $login;
		$this->password = $password;
		$this->client = new SoapClient(
			$this->getResPath() . '/' . static::SERVER_WS
		);
	}

	/**
	 * Выписывает новый счет
	 * @param string   $phone    Телефон (0123456789, без плюса, только Россия)
	 * @param float    $amount   Сумма
	 * @param string   $txn_id   Код счета
	 * @param string   $comment  Комментарий
	 * @param string   $lifetime Время жизни счета (пусто = 30 дней), максимум - 45 дней
	 * @param bool|int $alarm    Оповещение (0 - нет, 1 - sms, 2 - звонок), платно
	 * @param bool     $create   Создать ли пользователя, если он не существует
	 * @return S\StatusResult
	 */
	public function createBill(
		$phone, $amount, $txn_id, $comment, $lifetime = '', $alarm = false, $create = true
	) {
		$query = new SM\CreateBill();
		$query->login = $this->login;
		$query->password = $this->password;

		$query->user = $phone;
		$query->amount = (string)$amount;
		$query->comment = $comment;
		$query->txn = $txn_id;
		$query->lifetime = $lifetime;
		$query->alarm = (int)$alarm;
		$query->create = $create;

		$res = $this->client->createBill($query);
		$res = new S\StatusResult($res->createBillResult);
		return $res;
	}

	/**
	 * Отменяет выписанный ранее счет
	 * @param string $txn_id Код счета
	 * @return int
	 */
	public function cancelBill($txn_id) {
		$query = new SM\CancelBill();
		$query->login = $this->login;
		$query->password = $this->password;

		$query->txn = $txn_id;

		$res = $this->client->cancelBill($query);
		$res = $res->cancelBillResult;
		return $res;
	}

	/**
	 * Проверяет состояние выписанного счета
	 * @param string $txn_id
	 * @return SM\CheckBillResponse Код счета
	 */
	public function checkBill($txn_id) {
		$query = new SM\CheckBill();
		$query->login = $this->login;
		$query->password = $this->password;

		$query->txn = $txn_id;

		$res = $this->client->checkBill($query);
		$res->status = new S\StatusBill($res->status);
		return $res;
	}

}
