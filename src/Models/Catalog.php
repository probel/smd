<?php
namespace SMD\Models;
/**
 * Class Catalog
 *
 * Класс модель для работы с Каталогам
 *
 * @package SMD\Models
 * @link https://github.com/probel/smd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Catalog extends AbstractModel
{
    /**
     * Список разделов
     *
     * Метод для получения всех разделов.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?ruby#get-all-sections-list
     * @param array $parameters параметры для запроса
     * 
     * @return array Ответ SMD API
     */
    public function sections($parameters = [])
    {
        $response = $this->getRequest('/catalog/sections/all.json', $parameters);
        return is_array($response) ? $response : [];
    }
    /**
     * Список каталогов
     *
     * Метод для получения информации о разделе.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?ruby#information-about-section
     * @param int $id Уникальный id раздела
     * 
     * @return array Ответ SMD API
     */
    public function section($id)
    {
        $response = $this->getRequest("/catalog/sections/$id.json");
        return is_array($response) ? $response : [];
    }
    /**
     * Список каталогов
     *
     * Метод для получения списка параметров для раздела каталога.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?ruby#list-of-params-of-the-catalog-section
     * @param int $id Уникальный id раздела
     * 
     * @return array Ответ SMD API
     */
    public function sectionProps($id)
    {
        $response = $this->getRequest("/catalog/sections/$id/props.json");
        return is_array($response) ? $response : [];
    }

    /**
     * Продукт
     *
     * Метод для получения продукта по Id.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#get-product-by-id
     * @param int $id Уникальный id продукта
     * 
     * @return array Ответ SMD API
     */
    public function product($id)
    {
        $this->checkId($id);
        $response = $this->getRequest("/catalog/products/$id.json", $parameters);
        return $response;
    }

    /**
     * Аналоги продукта
     *
     * Метод для получения списка аналогов продукта.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#get-analogs-of-product
     * @param int $id Уникальный id продукта
     * @param array $parameters параметры для запроса
     * 
     * @return array Ответ SMD API
     */
    public function analogs($id, $parameters = [])
    {
        $response = $this->getRequest("/catalog/products/$id/analogs.json", $parameters);
        return is_array($response) ? $response : [];
    }
    
    /**
     * Список продуктов
     *
     * Метод для получения списка продуктов.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?ruby#get-products-list
     * @param array $parameters параметры для запроса
     * 
     * @return array Ответ SMD API
     */
    public function products($parameters = [])
    {
        $response = $this->getRequest("/catalog/products.json", $parameters);
        return [
            'items' => is_array($response) ? $response : [],
            'total' => $this->getTotal(),
        ];
    }
    /**
     * Количество продуктов в каталоге
     *
     * Метод для получения количества продуктов в каталоге.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?http#get-products-list-count
     * @param array $parameters параметры для запроса
     * 
     * @return int|null Ответ SMD API
     */
    public function counters($parameters = [])
    {
        $response = $this->getRequest("/catalog/products/counters.json", $parameters);
        return is_numeric($response) ? $response : null;
    }

    /**
     * Количество продуктов
     *
     * Метод для получения количества продуктов из заголовка.
     *
     * @return int|null
     */
    private function getTotal()
    {
        $total = null;
        preg_match("/X-Counters-Total: ([0-9]{1,}?).*$/Umi",$this->getLastHttpHeaders(),$matches);
        if (isset($matches[1])) {
            $total = $matches[1];
        }
        return $total;
    }       
}