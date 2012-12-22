IShop
========

# Платежная библиотека для QIWI через SOAP.

Преимущества библиотеки:
* Можно вообще не задумываться о SOAP, WSDL и т.д. Все просто работает.
* Для сервера есть проверка подписи, после обновления статуса, чек повторно подгружается с сервера (как рекомендуют доки киви).
* PHPDoc и все-все-все, так сложнее ошибиться. Кое-где есть дополнительные уточнения (где грабли лежат).
* Статусы расшифровываются (код -> текст).
* Есть в packagist, подключение займет 2 минуты.
* Нормально работает с автолоадером.
* PSR-2, код проще дорабатывать

Хабр: http://habrahabr.ru/post/162185
Протокол: https://ishop.qiwi.ru/docs/OnlineStoresProtocols_SOAP.pdf

### Код клиента (выписывает чек)

```php
<?php
namespace MyOwnMegaPrefix\Qiwi;

use MyOwnMegaPrefix\Settings,
    IShop;

class Qiwi extends IShop\Client
{
    protected $settings;

    public function __construct(
        Settings $settings // Нечто, что выдает нам настройки
    ) {
        $this->settings = $settings;

        parent::__construct(
            $this->settings->get('qiwi.login'),
            $this->settings->get('qiwi.pass')
        );
    }
}
```

### Код сервера (принимает запросы киви)

```php
use IShop\ServerMethods\CheckBillResponse as QiwiBill;
$callback = function ($bill) use (&$myMegaService) {
    /** @var QiwiBill $bill */
    $row = $myMegaService->findByKey( // Ищем чек в нашей базе
        $bill->id
    );
    if (!$row) {
        throw new \Exception('Неправильный код чека');
    }
    $myMegaService->process($row); // Что-то делаем с этим
    return $myMegaService->status(); // Код возврата для сервера QIWI. 0 - все нормально
};
// Вызываем метод обработки запроса
$theIShopObject->processRequest($callback);
// Если мы отдадим text/html, qiwi не пропустит платеж (да и вообще, надо протоколу следовать)
header('Content-Type: text/xml; charset=utf-8');
```