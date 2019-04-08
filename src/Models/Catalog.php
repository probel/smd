<?php
namespace SMD\Models;
/**
 * Class Catalog
 *
 * Класс модель для работы с Каталогам
 *
 * @package SMD\Models
 * @link https://github.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Catalog extends AbstractModel
{
    /**
     * @var array Список доступный полей для модели (исключая кастомные поля)
     */
    /* protected $fields = [
        'name',
        'request_id',
    ]; */
    /**
     * Список каталогов
     *
     * Метод для получения всех категорий.
     *
     * @link http://docs.api.smartdeal.practicalse.com/?ruby#get-all-sections-list
     * 
     * @return array Ответ SMD API
     */
    public function sectionsAll()
    {
        $parameters = [];
        $response = $this->getRequest('/catalog/sections/all.json', $parameters);
        return isset($response['catalogs']) ? $response['catalogs'] : [];
    }
    /**
     * Добавление каталогов
     *
     * Метод позволяет добавлять каталоги по одному или пакетно
     *
     * @link https://developers.SMD.ru/rest_api/catalogs/set.php
     * @param array $catalogs Массив каталогов для пакетного добавления
     * @return int|array Уникальный идентификатор каталога или массив при пакетном добавлении
     */
    public function apiAdd($catalogs = [])
    {
        if (empty($catalogs)) {
            $catalogs = [$this];
        }
        $parameters = [
            'catalogs' => [
                'add' => [],
            ],
        ];
        foreach ($catalogs AS $catalog) {
            $parameters['catalogs']['add'][] = $catalog->getValues();
        }
        $response = $this->postRequest('/private/api/v2/json/catalogs/set', $parameters);
        if (isset($response['catalogs']['add']['catalogs'])) {
            $result = array_map(function ($item) {
                return $item['id'];
            }, $response['catalogs']['add']['catalogs']);
        } else {
            return [];
        }
        return count($catalogs) == 1 ? array_shift($result) : $result;
    }
    /**
     * Обновление каталогов
     *
     * Метод позволяет обновлять данные по уже существующим каталогам
     *
     * @link https://developers.SMD.ru/rest_api/catalogs/set.php
     * @param int $id Уникальный идентификатор каталога
     * @return bool Флаг успешности выполнения запроса
     * @throws \SMD\Exception
     */
    public function apiUpdate($id)
    {
        $this->checkId($id);
        $parameters = [
            'catalogs' => [
                'update' => [],
            ],
        ];
        $catalog = $this->getValues();
        $catalog['id'] = $id;
        $parameters['catalogs']['update'][] = $catalog;
        $response = $this->postRequest('/private/api/v2/json/catalogs/set', $parameters);
        if (!isset($response['catalogs']['update']['errors'])) {
            return false;
        }
        return empty($response['catalogs']['update']['errors']);
    }
    /**
     * Удаление каталогов
     *
     * Метод позволяет удалять данные по уже существующим каталогам
     *
     * @link https://developers.SMD.ru/rest_api/catalogs/set.php
     * @param int $id Уникальный идентификатор каталога
     * @return bool Флаг успешности выполнения запроса
     * @throws \SMD\Exception
     */
    public function apiDelete($id)
    {
        $this->checkId($id);
        $parameters = [
            'catalogs' => [
                'delete' => [$id],
            ],
        ];
        $response = $this->postRequest('/private/api/v2/json/catalogs/set', $parameters);
        if (!isset($response['catalogs']['delete']['errors'])) {
            return false;
        }
        return empty($response['catalogs']['delete']['errors']);
    }
}