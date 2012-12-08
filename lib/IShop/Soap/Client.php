<?php
namespace IShop\Soap;
use IShop\ServerMethods as S;

/**
 * Client class
 *
 *
 *
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class Client extends \SoapClient {

	const SERVER_URL = 'http://server.ishop.mw.ru/';

	protected $classmapMethods = array(
		'checkBill'           => 'CheckBill',
		'checkBillResponse'   => 'CheckBillResponse',
		'cancelBill'          => 'CancelBill',
		'cancelBillResponse'  => 'CancelBillResponse',
		'createBill'          => 'CreateBill',
		'createBillResponse'  => 'CreateBillResponse',
		'getBillList'         => 'GetBillList',
		'getBillListResponse' => 'GetBillListResponse',
	);
	protected $classmap;

	protected function createDefaultClassmap() {
		$namespace = __NAMESPACE__ . '\ServerMethods';
		$this->classmap = array();
		foreach ($this->classmapMethods as $method => $binding) {
			$this->classmap[$method] = $namespace . '\\' . $binding;
		}
	}

	public function __construct($wsdl, $options = array()) {
		$this->createDefaultClassmap();
		$options['classmap'] = $this->classmap;
		parent::__construct($wsdl, $options);
	}

	/**
	 *
	 *
	 * @param S\CheckBill $parameters
	 * @return S\CheckBillResponse
	 */
	public function checkBill(S\CheckBill $parameters) {
		return $this->__soapCall('checkBill', array($parameters), array(
				'uri'        => static::SERVER_URL,
				'soapaction' => '',
			)
		);
	}

	/**
	 *
	 *
	 * @param S\GetBillList $parameters
	 * @return S\GetBillListResponse
	 */
	public function getBillList(S\GetBillList $parameters) {
		return $this->__soapCall('getBillList', array($parameters), array(
				'uri'        => static::SERVER_URL,
				'soapaction' => '',
			)
		);
	}

	/**
	 *
	 *
	 * @param S\CancelBill $parameters
	 * @return S\CancelBillResponse
	 */
	public function cancelBill(S\CancelBill $parameters) {
		return $this->__soapCall('cancelBill', array($parameters), array(
				'uri'        => static::SERVER_URL,
				'soapaction' => '',
			)
		);
	}

	/**
	 *
	 *
	 * @param S\CreateBill $parameters
	 * @return S\CreateBillResponse
	 */
	public function createBill(S\CreateBill $parameters) {
		return $this->__soapCall('createBill', array($parameters), array(
				'uri'        => static::SERVER_URL,
				'soapaction' => '',
			)
		);
	}

}
