<?php

declare(strict_types=1);

/**
 * This is a comprehensive PHP package designed to streamline the development
 * of controllers in the application following the MVC (Model-View-Controller)
 * architectural pattern. It provides a set of powerful tools and utilities to
 * handle user input, orchestrate application logic, and facilitate seamless
 * communication between the Model and View components.
 * PHP VERSION >= 8.2.0
 * 
 * Copyright (C) 2023, José V S Carneiro
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by  
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful,    
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License 
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category Controllers
 * @package  Josevaltersilvacarneiro\Html\App\Controllers
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/App/Controllers
 */

namespace Josevaltersilvacarneiro\Html\App\Controller\Cart;

use Josevaltersilvacarneiro\Html\App\Controller\HTMLController;
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\{
    SessionEntityInterface};

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This controller renders Cart page.
 * 
 * @category  Cart
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Cart
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.2
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class Cart extends HTMLController
{
    /**
     * Initializes the Cart controller.
     * 
     * @param SessionEntityInterface $_session session
     * 
     * @return void
     */
    public function __construct(private readonly SessionEntityInterface $_session)
    {
        $this->setPage('Cart');
        $this->setTitle('PDV - Adicione novos itens');
        $this->setDescription('Página para atender ao cliente.');
        $this->setKeywords('josevaltersilvacarneiro Joilma caixa');
    }

    /**
     * It handles a request and produces a response.
     * 
     * @param ServerRequestInterface $request request
     * 
     * @return ResponseInterface response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->_session->isUserLogged()) {
            return new Response(302, ['Location' => '/login']);
        }

        $order = filter_input(INPUT_GET, 'order', FILTER_VALIDATE_INT);

        if ($order === false || is_null($order) || $order < 1) {
            $order = 0;
            $itemList = [];
        } else {
            $query = <<<QUERY
            SELECT t.title, o.amount, o.price, (o.amount * o.price) AS total FROM `order_items` AS o
            INNER JOIN packages AS p
            ON p.package_id = o.package
            INNER JOIN types_of_product AS t
            ON p.type_of_product = t.type_of_product_id
            WHERE `order` = :order
            QUERY;

            $repository = new Repository();
            $stmt = $repository->query($query, ['order' => $order]);

            $itemList = $stmt === false ? [] : $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        $this->setVariables([
            'ITEM_LIST_' => $itemList,
            'ORDER_' => $order,
            'TOTAL_PRICE_' => $this->_getTotalPrice($itemList)
        ]);

        return new Response(
            200, [
                'Content-Type' => 'text/html;charset=UTF-8'
            ], parent::renderLayout()
        );
    }

    /**
     * This method receives the array with the list of products
     * that are added to cart and returns the total sum.
     * 
     * @param array $productList list of products
     * 
     * @return string total sum
     */
    private function _getTotalPrice(array $productList): string
    {
        $sum = 0;
        foreach ($productList as $value) {
            $price = floatval($value['total']);
            $sum += $price;
        }

        // convert to string
        return number_format($sum, 2, ',', '.');
    }
}
