<?php
namespace IShop\ServerMethods;
use IShop\Status\StatusBill;

class checkBillResponse {
	/** @var string */
	public $user;

	/** @var string */
	public $amount;

	/** @var string */
	public $date;

	/** @var string */
	public $lifetime;

	/** @var int|StatusBill */
	public $status;
}