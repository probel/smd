<?php
namespace SMD\Models;
use ArrayAccess;
use SMD\Exception;
use SMD\Helpers\Format;
use SMD\Request\Request;
/**
 * Class AbstractModel
 *
 * Абстрактный класс для всех моделей
 *
 * @package SMD\Models
 * @link https://github.com/probel/smd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class AbstractModel extends Request implements ArrayAccess, ModelInterface
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    protected $fields = [];
    /**
     * @var array Список значений полей для модели
     */
    protected $values = [];
    /**
     * Возвращает называние Модели
     *
     * @return mixed
     */
    public function __toString()
    {
        return static::class;
    }
    /**
     * Определяет, существует ли заданное поле модели
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset Название поля для проверки
     * @return boolean Возвращает true или false
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }
    /**
     * Возвращает заданное поле модели
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset Название поля для возврата
     * @return mixed Значение поля
     */
    public function offsetGet($offset)
    {
        if (isset($this->values[$offset])) {
            return $this->values[$offset];
        }
        return null;
    }
    /**
     * Устанавливает заданное поле модели
     *
     * Если есть сеттер модели, то будет использовать сеттер
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset Название поля, которому будет присваиваться значение
     * @param mixed $value Значение для присвоения
     */
    public function offsetSet($offset, $value)
    {
        $setter = 'set' . Format::camelCase($offset);
        if (method_exists($this, $setter)) {
            return $this->$setter($value);
        } elseif (in_array($offset, $this->fields)) {
            $this->values[$offset] = $value;
        }
    }
    /**
     * Удаляет поле модели
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset Название поля для удаления
     */
    public function offsetUnset($offset)
    {
        if (isset($this->values[$offset])) {
            unset($this->values[$offset]);
        }
    }
    /**
     * Получение списка значений полей модели
     *
     * @return array Список значений полей модели
     */
    public function getValues()
    {
        return $this->values;
    }
   
    /**
     * Проверяет ID на валидность
     *
     * @param mixed $id ID
     * @return bool
     * @throws Exception
     */
    protected function checkId($id)
    {
        if (intval($id) != $id || $id < 1) {
            throw new Exception('Id must be integer and positive');
        }
        return true;
    }
}