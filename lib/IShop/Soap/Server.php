<?php
namespace IShop\Soap;
use IShop\ServerMethods as S;

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

	public function __construct($wsdl) {
		$this->createDefaultClassmap();
		$this->wsdl = $wsdl;
	}

	protected $returnResult;

	public function processRequest() {
		$server = new \SoapServer(
			$this->wsdl, array('classmap' => $this->classmap)
		);
		$server->setObject($this);
		$this->returnResult = null;
		$server->handle();
		return $this->returnResult;
	}

	public function updateBill($param) {
		$this->returnResult = print_r($param, true);
		return 0;
	}

}
