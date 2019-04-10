<?php
namespace SMD\Models;
/**
 * Class Lots
 *
 * Класс модель для работы с Лотами
 *
 * @package SMD\Models
 * @link https://github.com/probel/smd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Lots extends AbstractModel
{
    /**
     * Список лотов
     *
     * Метод для получения списка лотов текущего пользователя
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#smart-deal-lots
     * @param array $parameters Массив параметров
     * @return array Ответ SMD API
     */
    public function get($parameters = [])
    {
        $response = $this->getRequest('/lots.json', $parameters);
        return is_array($response) ? $response : [];
    }

    /**
     * Добавление лота
     *
     * Метод позволяет добавлять лот
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#create-new-lot
     * @param array $parameters Массив параметров
     * @return object|null Ответ SMD API
     */
    public function add($parameters = [])
    {
        $response = $this->postRequest('/lots.json', $parameters);
        return is_object($response) ? $response : null;
    }
    /**
     * Отчет
     *
     * Метод для получения отчета по лоту
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#get-analytics-report-for-lot
     * @param int $id Уникальный id лота
     * @param array $parameters Массив параметров
     * @return array Ответ SMD API
     */
    public function report($id, $parameters = [])
    {
        $response = $this->getRequest("/lots/$id/report.json", $parameters);
        return is_array($response) ? $response : [];
    }
}