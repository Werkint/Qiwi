<?php
namespace IShop\Status;

class Bill {

	protected $txn;
	protected $status;

	public function __construct($txn, $status) {
		$this->txn = $txn;
		$this->status = new StatusBill($status);
	}

	public function getTxn() {
		return $this->txn;
	}

	public function getStatus() {
		return $this->status;
	}

}