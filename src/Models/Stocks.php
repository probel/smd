<?php
namespace SMD\Models;
/**
 * Class Stocks
 *
 * Класс модель для работы со Стоками
 *
 * @package SMD\Models
 * @link https://github.com/probel/smd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Stocks extends AbstractModel
{
    /**
     * Список стоков
     *
     * Метод для получения всех стоков текущего пользователя.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#get-stocks-of-current-user
     * @param array $parameters Массив параметров
     * @return array Ответ SMD API
     */
    public function get($parameters = [])
    {
        $response = $this->getRequest('/stocks.json', $parameters);
        return is_array($response) ? $response : [];
    }

    /**
     * Добавление стоков
     *
     * Метод позволяет добавлять сток
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#create-new-stock
     * @param array $parameters Массив параметров
     * @return object|null Ответ SMD API
     */
    public function add($parameters = [])
    {
        $response = $this->postRequest('/stocks.json', $parameters);
        return is_object($response) ? $response : null;
    }
    /**
     * Список позиций
     *
     * Метод для получения позиций в стоке.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#get-positions-of-the-stock
     * @param int|string $id Уникальный id раздела
     * 
     * @return array Ответ SMD API
     */
    public function positions($id = '_')
    {
        $response = $this->getRequest("/stocks/$id/positions.json");
        return is_array($response) ? $response : [];
    }
    /**
     * Обновление позиций в стоке
     *
     * Метод позволяет обновить позиции в стоке
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#update-positions-of-the-stock
     * @param int $id Уникальный id стока
     * @param array $positons Массив позиций
     * @return array Ответ SMD API
     */
    public function positionsPatch($id,$positons = [])
    {
        $response = $this->patchRequest("/stocks/$id/positions.json", $positons);
        return $response;;
    }
    /**
     * Создание позиций в стоке
     *
     * Метод позволяет добавить позиции в стоке
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#update-positions-of-the-stock
     * @param int $id Уникальный id стока
     * @param array $positons Массив позиций
     * @return array Ответ SMD API
     */
    public function positionsAdd($id,$positons)
    {
        $response = $this->postRequest("/stocks/$id/positions.json", $positons);
        return $response;//is_object($response) ? $response : null;
    }
    /**
     * Информация по позиции в стоке
     *
     * Метод позволяет узнать детали по позиции в стоке
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#get-details-of-position
     * @param int $stockId Уникальный id стока
     * @param int $positionId Уникальный id позиции
     * @return object|null Ответ SMD API
     */
    public function positionsDetails($stockId,$positionId)
    {
        $response = $this->getRequest("/stocks/$stockId/positions/$positionId.json");
        return is_object($response) ? $response : null;
    }
    /**
     * Обновление позиции в стоке
     *
     * Метод позволяет обновить одну позицию в стоке
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#update-single-position
     * @param int $stockId Уникальный id стока
     * @param int $positionId Уникальный id позиции
     * @param object $position Объект позиции
     * @return array Ответ SMD API
     */
    public function positionPatch($stockId,$positionId,$positon)
    {
        $response = $this->patchRequest("/stocks/$stockId/positions/$positionId.json", $positon);
        return $response;
    }
    /**
     * Закрытие позиции в стоке
     *
     * Метод позволяет закрыть позицию в стоке
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#close-position-of-stock
     * @param int $stockId Уникальный id стока
     * @param int $positionId Уникальный id позиции
     * @return array Ответ SMD API
     */
    public function positionClose($stockId,$positionId)
    {
        $response = $this->deleteRequest("/stocks/$stockId/positions/$positionId.json");
        return is_array($response) ? $response : [];
    }
    /**
     * Список позиций по статусу
     *
     * Метод для получения позиций в стоке по статусу.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#get-positions-of-the-stock
     * @param int|string $id Уникальный id раздела
     * 
     * @return array Ответ SMD API
     */
    public function positionsByStatus($id = '_', $status = 'open')
    {
        $available = ['open','close'];
        if (!in_array($status,$available)) {
            //return [];
        }
        print "\n---------"."/stocks/$id/positions/$status.json"."----------\n";
        $response = $this->getRequest("/stocks/$id/positions/$status.json");
        return $response;
        return is_array($response) ? $response : [];
    }
}