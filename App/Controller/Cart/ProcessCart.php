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
 * @version   Release: 0.2.2
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
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
        $bar_code = filter_input(INPUT_POST, 'bar_code', FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[0-9]{13}$/']
        ]);

        // if there was a problem to validate the amount and bar code, redirect to orders

        $isAmountNotValid = $amount === false || is_null($amount) || $amount < 1;
        $isBarCodeNotValid = $bar_code === false || is_null($bar_code) || !$this->_isCodeValid($bar_code);

        if ($isAmountNotValid || $isBarCodeNotValid) {
            return new Response(302, ['Location' => '/orders']);
        }

        // if the order doesn't exist, you need to create it

        $doNeedCreateTheOrder = $order === false || is_null($order) || $order < 1;

        // first, search by package

        $repository = new Repository();

        $query = <<<QUERY
        SELECT * FROM `packages`
        WHERE bar_code = :bar_code
        LIMIT 1;
        QUERY;

        $stmt = $repository->query($query, ['bar_code' => $bar_code]);

        // there was an error or this package doesn't exist

        if ($stmt === false || $stmt->rowCount() < 1) {
            if ($doNeedCreateTheOrder) {
                return new Response(302, ['Location' => '/bag']);
            }

            return new Response(302, ['Location' => '/bag?order=' . $order]);
        }

        // getting the package

        $packageRecord = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$this->_isThereStockInThePackage(
            $packageRecord['number_of_items_purchased'],
            $packageRecord['number_of_items_sold'],
            $amount
        )) {
            if ($doNeedCreateTheOrder) {
                return new Response(302, ['Location' => '/bag']);
            }

            return new Response(302, ['Location' => '/bag?order=' . $order]);
        }

        // updating the number of items sold

        $package = intval($packageRecord['package_id']);
        $packageRecord['number_of_items_sold'] += $amount;

        // if the order already exists, search by ITEM with order and package

        if (!$doNeedCreateTheOrder) {
            $query = <<<QUERY
            SELECT * FROM order_items
            WHERE `order` = :order AND package = :package
            LIMIT 1;
            QUERY;

            $stmt = $repository->query($query, ['order' => $order, 'package' => $package]);
            $itemRecord = $stmt === false || $stmt->rowCount() < 1 ? false : $stmt->fetch(\PDO::FETCH_ASSOC);

            // if already exists a package with the same bar code added

            if ($itemRecord !== false) {

                $this->_addExistingItem($packageRecord, $itemRecord, $amount);
                return new Response(200, ['Location' => '/bag?order=' . $order]);
            }
        }

        // there is no package item added, search by price

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

        // if order already exists on db

        if (!$doNeedCreateTheOrder) {

            $query1 = <<<QUERY
            INSERT INTO order_items (`order`, package, amount, price)
            VALUES (:order, :package, :amount, :price);
            QUERY;

            $query2 = <<<QUERY
            UPDATE `packages`
            SET number_of_items_sold = number_of_items_sold + :amount
            WHERE package_id = :package;
            QUERY;

            $record1 = [$query1, ['order' => $order, 'package' => $package, 'amount' => $amount, 'price' => $price]];
            $record2 = [$query2, ['package' => $package, 'amount' => $amount]];

            // ignore results

            $repository->queryAll($record1, $record2);

            return new Response(200, ['Location' => '/bag?order=' . $order]);
        }

        // if the order doesn't exist

        $orderDate = new \DateTimeImmutable('now', new \DateTimeZone('America/Bahia'));
        $orderDate = $orderDate->format('Y-m-d H:i:s');

        $query1 = <<<QUERY
        INSERT INTO `orders` (order_date)
        VALUES (:order_date);
        QUERY;

        $query2 = <<<QUERY
        INSERT INTO `order_items` (`order`, `package`, amount, price)
        VALUES (LAST_INSERT_ID(), :package, :amount, :price);
        QUERY;

        $query3 = <<<QUERY
        UPDATE `packages`
        SET number_of_items_sold = number_of_items_sold + :amount
        WHERE package_id = :package;
        QUERY;

        $record1 = [$query1, ['order_date' => $orderDate]];
        $record2 = [$query2, ['package' => $package, 'amount' => $amount, 'price' => $price]];
        $record3 = [$query3, ['amount' => $amount, 'package' => $package]];

        $stmt = $repository->queryAll($record1, $record2, $record3);

        if ($stmt !== false && $stmt->rowCount() > 0) {
            return new Response(200, ['Location' => '/orders']);
        }

        return new Response(302, ['Location' => '/bag']);
    }

    /**
     * This method verifies if there is stock.
     * 
     * @param int $numberOfItemsPurchased purchased items
     * @param int $numberOfItemsSold sold items
     * 
     * @return bool true on success; false otherwise
     */
    private function _isThereStockInThePackage(
        int $numberOfItemsPurchased, int $numberOfItemsSold, int $amount
    ): bool {
        $stock = $numberOfItemsPurchased - $numberOfItemsSold;

        return $stock >= $amount;
    }

    /**
     * This method increases the amount on order_items
     * and number_of_items_sold on packages when a
     * product of this package already added.
     * 
     * @param array $packageRecord a array key-value to column and its value
     * @param array $itemRecord a array key-value of the item added
     * 
     * @return bool true on success; false otherwise
     */
    private function _addExistingItem(
        array $packageRecord, array $itemRecord, int $amount
    ): bool {
        // amount ++ on order_items
        // number_of_items_sold ++ on packages

        $repository = new Repository();

        $itemQuery = <<<QUERY
        UPDATE `order_items`
        SET `amount` = `amount` + :amount
        WHERE `package` = :package AND `order` = :order
        LIMIT 1;
        QUERY;

        $itemRecord = [
            'order' => intval($itemRecord['order']),
            'package' => intval($itemRecord['package']),
            'amount' => $amount
        ];

        $query1 = [$itemQuery, $itemRecord];
        $query2 = $repository->cleanUpdate('packages', $packageRecord);

        $stmt = $repository->queryAll($query1, $query2);

        return $stmt !== false && $stmt->rowCount() > 0;
    }
}
