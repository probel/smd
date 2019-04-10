<?php
namespace SMD;
/**
 * Class Exception
 *
 * Базовый класс для всех исключений SMD API
 *
 * @package SMD
 * @link https://github.com/probel/smd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Exception extends \Exception
{
    /**
     * @var array Справочник ошибок и ответов SMD API
     */
    protected $errors = [
        '101' => 'Аккаунт не найден',
    ];
    /**
     * Exception constructor
     *
     * @param null|string $message Сообщения исключения
     * @param int $code Код исключения
     */
    public function __construct($message = null, $code = 0)
    {
        if (isset($this->errors[$code])) {
            $message = $this->errors[$code];
        }
        parent::__construct($message, $code);
    }
}