<?php
namespace SMD;
use SMD\Models\ModelInterface;
use SMD\Request\CurlHandle;
use SMD\Request\ParamsBag;
use SMD\Helpers\Format; 
/**
 * Class Client
 *
 * Основной класс для получения доступа к моделям SMD API
 *
 * @package SMD
 * @link https://github.com/probel/smd
 * @property \SMD\Models\Catalog $catalog
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Client
{
    /**
     * @var ParamsBag|null Экземпляр ParamsBag для хранения аргументов
     */
    public $parameters = null;
    /**
     * @var CurlHandle Экземпляр CurlHandle для повторного использования
     */
    private $curlHandle;
    /**
     * Client constructor
     *
     * @param string $token Токен пользователя
     * @param string|null $proxy Прокси сервер для отправки запроса
     */
    public function __construct($token, $proxy = null)
    {
        
        $this->parameters = new ParamsBag();
        $this->parameters->addAuth('token', $token);
        if ($proxy !== null) {
            $this->parameters->addProxy($proxy);
        }
        //$this->fields = new Fields();
        $this->curlHandle = new CurlHandle();
    }
    /**
     * Возвращает экземпляр модели для работы с SMD API
     *
     * @param string $name Название модели
     * @return ModelInterface
     * @throws ModelException
     */
    public function __get($name)
    {
        $classname = '\\SMD\\Models\\' . Format::camelCase($name);
        if (!class_exists($classname)) {
            throw new ModelException('Model not exists: ' . $name);
        }
        // Чистим GET, POST, PATCH от предыдущих вызовов
        $this->parameters->clearGet()->clearPost()->clearPatch()->clearDelete();
        return new $classname($this->parameters, $this->curlHandle);
    }
}