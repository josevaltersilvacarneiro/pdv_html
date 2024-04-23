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

use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\SessionEntityInterface;

use Josevaltersilvacarneiro\Html\Src\Classes\Dao\GenericDao;
use Josevaltersilvacarneiro\Html\Src\Classes\Sql\Connect;
use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;

use Josevaltersilvacarneiro\Html\Src\Traits\BarCodeTrait;

/**
 * This class process a new item in the cart of the customer.
 * 
 * @category  ProcessCart
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Cart
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.2
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class ProcessCart implements RequestHandlerInterface
{
    use BarCodeTrait;

    /**
     * Initializes the ProcessCart controller.
     * 
     * @param SessionEntityInterface $_session session
     * 
     * @return void
     */
    public function __construct(
        private readonly SessionEntityInterface $_session
    ) {
    }

    /**
     * This method receives the request to add a new product
     * based on ean13 and returns a response.
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

        $order = filter_input(INPUT_POST, 'order', FILTER_VALIDATE_INT);

        $bar_code = filter_input(INPUT_POST, 'bar_code', FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[0-9]{13}$/']
        ]);

        // if there was a problem to validate the bar code, redirect to orders

        if ($bar_code === false || is_null($bar_code) || !$this->_isCodeValid($bar_code)) {
            return new Response(302, ['Location' => '/orders']);
        }

        if ($order === false || is_null($order) || $order === 0) {

            $dao = new GenericDao(Connect::newMysqlConnection(), 'orders');

            $order_date = new \DateTimeImmutable('now', new \DateTimeZone('America/Bahia'));
            $order = $dao->ic(['order_date' => $order_date->format('Y-m-d H:i:s')]);

            if ($order === false) { // Unable to include
                return new Response(302, ['Location' => '/bag?order=' . $order]);
            }

            // conversion to int

            $order = intval($order);
        }

        if ($order < 1) { // bug
            return new Response(302, ['Location' => '/bag']);
        }

        // adding a item to order

        // first, search by package
        // verify if number_of_items_purchased - number_of_items_sold > 0 #STOCK

        $repository = new Repository();

        $query = <<<QUERY
        SELECT * FROM `packages`
        WHERE bar_code = :bar_code
        LIMIT 1;
        QUERY;

        $stmt = $repository->query($query, ['bar_code' => $bar_code]);
        
        if ($stmt === false || $stmt->rowCount() < 1) {
            // there was an error or this package doesn't exist
            return new Response(302, ['Location' => '/bag?order=' . $order]);
        }

        $packageRecord = $stmt->fetch(\PDO::FETCH_ASSOC);

        $packagePurchased = intval($packageRecord['number_of_items_purchased']);
        $packageSold = intval($packageRecord['number_of_items_sold']);
        $stock = $packagePurchased - $packageSold;

        if ($stock < 1) {
            return new Response(302, ['Location' => '/bag?order=' . $order]);
        }

        $package = intval($packageRecord['package_id']);
        $packageRecord = [
            'package_id' => $packageRecord['package_id'],
            'number_of_items_sold' => intval($packageRecord['number_of_items_sold']) + 1
        ];

        // after this, search by ITEM with order and package

        $query = <<<QUERY
        SELECT * FROM order_items
        WHERE `order` = :order AND package = :package
        LIMIT 1;
        QUERY;

        $stmt = $repository->query($query, ['order' => $order, 'package' => $package]);
        $itemRecord = $stmt === false || $stmt->rowCount() < 1 ? false : $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($itemRecord !== false) {
            // if this item is already added
            // amount ++ on order_items
            // number_of_items_sold ++ on order_items

            $itemQuery = <<<QUERY
            UPDATE `order_items`
            SET `amount` = :amount
            WHERE `package` = :package AND `order` = :order
            LIMIT 1;
            QUERY;

            $itemRecord = [
                'order' => intval($itemRecord['order']),
                'package' => intval($itemRecord['package']),
                'amount' => intval($itemRecord['amount']) + 1
            ];

            $query1 = [$itemQuery, $itemRecord];
            $query2 = $repository->cleanUpdate('packages', $packageRecord);

            //echo var_dump($query1); exit();

            $repository->queryAll($query1, $query2); // ignore result

            return new Response(200, ['Location' => '/bag?order=' . $order]);
        }

        // searching by price

        $query = <<<QUERY
        SELECT t.price FROM `types_of_product` AS t
        INNER JOIN `packages` AS p
        ON p.type_of_product = t.type_of_product_id
        WHERE p.bar_code = :bar_code
        LIMIT 1
        QUERY;

        $stmt = $repository->query($query, ['bar_code' => $bar_code]);

        if ($stmt === false || $stmt->rowCount() < 1) {
            return new Response(302, ['Location' => '/bag?order=' . $order]);
        }

        $price = $stmt->fetch(\PDO::FETCH_ASSOC)['price'];

        // adding a new item

        $query = <<<QUERY
        INSERT INTO order_items (`order`, package, price)
        VALUES (:order, :package, :price);
        QUERY;

        $query1 = [$query, ['order' => $order, 'package' => $package, 'price' => $price]];
        $query2 = $repository->cleanUpdate('packages', $packageRecord);

        $repository->queryAll($query1, $query2); // ignore result

        return new Response(200, ['Location' => '/bag?order=' . $order]);
    }
}
