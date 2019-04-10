<?php
namespace SMD\Request;
use DateTime;
use SMD\Exception;
use SMD\NetworkException;
/**
 * Class Request
 *
 * Класс отправляющий запросы к API SMD используя cURL
 *
 * @package SMD\Request
 * @link https://github.com/probel/smd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Request
{
    /**
     * @var bool Флаг вывода отладочной информации
     */
    private $debug = false;
    /**
     * @var ParamsBag|null Экземпляр ParamsBag для хранения аргументов
     */
    private $parameters = null;
    /**
     * @var CurlHandle Экземпляр CurlHandle
     */
    private $curlHandle;
    /**
     * @var int|null Последний полученный HTTP код
     */
    private $lastHttpCode = null;
    /**
     * @var string|null Последний полученный HTTP ответ
     */
    private $lastHttpResponse = null;
    /**
     * @var string|null Последний полученный HTTP заголовок
     */
    private $lastHttpHeaders = null;
    /**
     * Request constructor
     *
     * @param ParamsBag       $parameters Экземпляр ParamsBag для хранения аргументов
     * @param CurlHandle|null $curlHandle Экземпляр CurlHandle для повторного использования
     */
    public function __construct(ParamsBag $parameters, CurlHandle $curlHandle = null)
    {
        $this->parameters = $parameters;
        $this->curlHandle = $curlHandle !== null ? $curlHandle : new CurlHandle();
    }
    /**
     * Установка флага вывода отладочной информации
     *
     * @param bool $flag Значение флага
     * @return $this
     */
    public function debug($flag = false)
    {
        $this->debug = (bool)$flag;
        return $this;
    }
    /**
     * Возвращает последний полученный HTTP код
     *
     * @return int|null
     */
    public function getLastHttpCode()
    {
        return $this->lastHttpCode;
    }
    /**
     * Возвращает последний полученный HTTP ответ
     *
     * @return null|string
     */
    public function getLastHttpResponse()
    {
        return $this->lastHttpResponse;
    }
    /**
     * Возвращает последний полученный HTTP заголовок
     *
     * @return null|string
     */
    public function getLastHttpHeaders()
    {
        return $this->lastHttpHeaders;
    }
    /**
     * Возвращает экземпляр ParamsBag для хранения аргументов
     *
     * @return ParamsBag|null
     */
    protected function getParameters()
    {
        return $this->parameters;
    }
    /**
     * Выполнить HTTP GET запрос и вернуть тело ответа
     *
     * @param string $url Запрашиваемый URL
     * @param array $parameters Список GET параметров
     * @param null|string $modified Значение заголовка IF-MODIFIED-SINCE
     * @return mixed
     * @throws Exception
     * @throws NetworkException
     */
    protected function getRequest($url, $parameters = [], $modified = null)
    {
        if (!empty($parameters)) {
            $this->parameters->addGet($parameters);
        }
        return $this->request($url, $modified);
    }
    /**
     * Выполнить HTTP POST запрос и вернуть тело ответа
     *
     * @param string $url Запрашиваемый URL
     * @param array $parameters Список POST параметров
     * @return mixed
     * @throws Exception
     * @throws NetworkException
     */
    protected function postRequest($url, $parameters = [])
    {
        if (!empty($parameters)) {
            $this->parameters->addPost($parameters);
        }
        return $this->request($url);
    }
    /**
     * Выполнить HTTP PATCH запрос и вернуть тело ответа
     *
     * @param string $url Запрашиваемый URL
     * @param array $parameters Список POST параметров
     * @return mixed
     * @throws Exception
     * @throws NetworkException
     */
    protected function patchRequest($url, $parameters = [])
    {
        if (!empty($parameters)) {
            $this->parameters->addPost($parameters);
        }
        $this->parameters->setPatch();
        return $this->request($url);
    }
    /**
     * Выполнить HTTP DELETE запрос и вернуть тело ответа
     *
     * @param string $url Запрашиваемый URL
     * @param array $parameters Список POST параметров
     * @return mixed
     * @throws Exception
     * @throws NetworkException
     */
    protected function deleteRequest($url, $parameters = [])
    {
        if (!empty($parameters)) {
            $this->parameters->addPost($parameters);
        }
        $this->parameters->setDelete();
        return $this->request($url);
    }
    /**
     * Подготавливает список заголовков HTTP
     *
     * @param mixed $modified Значение заголовка IF-MODIFIED-SINCE
     * @return array
     */
    protected function prepareHeaders($modified = null)
    {
        $headers = [
            'Connection: keep-alive',
            'Content-Type: application/json',
            'X-Token: '.$this->parameters->getAuth('token'),
        ];
        if ($modified !== null) {
            if (is_int($modified)) {
                $headers[] = 'IF-MODIFIED-SINCE: ' . $modified;
            } else {
                $headers[] = 'IF-MODIFIED-SINCE: ' . (new DateTime($modified))->format(DateTime::RFC1123);
            }
        }
        return $headers;
    }
    /**
     * Подготавливает URL для HTTP запроса
     *
     * @param string $url Запрашиваемый URL
     * @return string
     */
    protected function prepareEndpoint($url)
    {
        $query = http_build_query($this->parameters->getGet(), null, '&');
        return 'http://api.smartdeal.practicalse.com/rest/v1'.$url.($query ? ('?'.$query) : '');
    }
    /**
     * Выполнить HTTP запрос и вернуть тело ответа
     *
     * @param string $url Запрашиваемый URL
     * @param null|string $modified Значение заголовка IF-MODIFIED-SINCE
     * @return mixed
     * @throws Exception
     * @throws NetworkException
     */
    protected function request($url, $modified = null)
    {
        $headers = $this->prepareHeaders($modified);
        $endpoint = $this->prepareEndpoint($url);
        $this->printDebug('url', $endpoint);
        $this->printDebug('headers', $headers);
        $ch = $this->curlHandle->open();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        if ($this->parameters->hasPost()) {
            $fields = json_encode([
                'request' => $this->parameters->getPost(),
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $this->printDebug('post params', $fields);
        }
        if ($this->parameters->hasProxy()) {
            curl_setopt($ch, CURLOPT_PROXY, $this->parameters->getProxy());
        }
        if ($this->parameters->hasPatch()) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        }
        if ($this->parameters->hasDelete()) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $headerSize);
        $result = substr($result, $headerSize);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $this->curlHandle->close();
        $this->lastHttpCode = $info['http_code'];
        $this->lastHttpResponse = $result;
        $this->lastHttpHeaders = $header;
        $this->printDebug('curl_exec', $result);
        $this->printDebug('curl_getinfo', $info);
        $this->printDebug('curl_headers', $header);
        $this->printDebug('curl_error', $error);
        $this->printDebug('curl_errno', $errno);
        if ($result === false && !empty($error)) {
            throw new NetworkException($error, $errno);
        }
        
        return $this->parseResponse($result, $info);
    }
    /**
     * Парсит HTTP ответ, проверяет на наличие ошибок и возвращает тело ответа
     *
     * @param string $response HTTP ответ
     * @param array $info Результат функции curl_getinfo
     * @return mixed
     * @throws Exception
     */
    protected function parseResponse($response, $info)
    {
        $result = json_decode($response);
        if (floor($info['http_code'] / 100) >= 3) {
            if ($result !== null) {
                $code = 0;
            } else {
                $code = $info['http_code'];
            }
            
            if ($result) {
                /* ЗДЕСЬ НУЖНО ДОПИСАТЬ ОБРАБОТКУ ОШИБОК. 
                    На данный момент API не отдает ошибок 
                */
                //throw new Exception(json_encode($result));
            } elseif($response) {
                throw new Exception($response, $code);
            } else {
                throw new Exception('Invalid response body.', $code);
            }
        } elseif (!$result) {
            return false;
        }
        return $result;
    }
    /**
     * Вывода отладочной информации
     *
     * @param string $key Заголовок отладочной информации
     * @param mixed $value Значение отладочной информации
     * @param bool $return Возврат строки вместо вывода
     * @return mixed
     */
    protected function printDebug($key = '', $value = null, $return = false)
    {
        if ($this->debug !== true) {
            return false;
        }
        if (!is_string($value)) {
            $value = print_r($value, true);
        }
        $line = sprintf('[DEBUG] %s: %s', $key, $value);
        if ($return === false) {
            return print_r($line . PHP_EOL);
        }
        return $line;
    }
}