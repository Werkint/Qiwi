<?php
namespace IShop;
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
class SoapClient extends \SoapClient {

	const SERVER_URL = 'http://server.ishop.mw.ru/';

	protected $classmapMethods = array(
		'checkBill',
		'checkBillResponse',
		'getBillList',
		'getBillListResponse',
		'cancelBill',
		'cancelBillResponse',
		'createBill',
		'createBillResponse',
	);
	protected $classmap;

	protected function createDefaultClassmap() {
		$namespace = __NAMESPACE__ . '\ServerMethods';
		$this->classmap = array();
		foreach ($this->classmapMethods as $method) {
			$this->classmap[$method] = $namespace . '\\' . $method;
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
	 * @param S\checkBill $parameters
	 * @return S\checkBillResponse
	 */
	public function checkBill(S\checkBill $parameters) {
		return $this->__soapCall('checkBill', array($parameters), array(
				'uri'        => static::SERVER_URL,
				'soapaction' => '',
			)
		);
	}

	/**
	 *
	 *
	 * @param S\getBillList $parameters
	 * @return S\getBillListResponse
	 */
	public function getBillList(S\getBillList $parameters) {
		return $this->__soapCall('getBillList', array($parameters), array(
				'uri'        => static::SERVER_URL,
				'soapaction' => '',
			)
		);
	}

	/**
	 *
	 *
	 * @param S\cancelBill $parameters
	 * @return S\cancelBillResponse
	 */
	public function cancelBill(S\cancelBill $parameters) {
		return $this->__soapCall('cancelBill', array($parameters), array(
				'uri'        => static::SERVER_URL,
				'soapaction' => '',
			)
		);
	}

	/**
	 *
	 *
	 * @param S\createBill $parameters
	 * @return S\createBillResponse
	 */
	public function createBill(S\createBill $parameters) {
		return $this->__soapCall('createBill', array($parameters), array(
				'uri'        => static::SERVER_URL,
				'soapaction' => '',
			)
		);
	}

}
