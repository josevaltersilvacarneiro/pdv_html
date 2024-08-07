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

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * This class handles the abandonment of purchases at
 * the checkout.
 * 
 * @category  AbandonCart
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Cart
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.1.0
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class AbandonCart implements RequestHandlerInterface
{
    /**
     * Initializes the AbandonCart object.
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
     * This methods receives a request to delete the order
     * and returns a response.
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
            return new Response(302, ['Location' => '/bag']);
        }

        // first, search by all items which the order is $order

        $repository = new Repository();

        $query = <<<QUERY
        SELECT `order`, package, amount FROM order_items
        WHERE `order` = :order;
        QUERY;

        $stmt = $repository->query($query, ['order' => $order]);

        $orderItems = $stmt === false ? [] : $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // decrease the items sold from package and delete all items

        $queries = [];
        foreach ($orderItems as $item) {
            $orderId = intval($item['order']);
            $packageId = intval($item['package']);
            $amount = intval($item['amount']);

            // WARNING -> the code below doesn't checking if
            // the number_of_items_sold = 0 (it would be an
            // inconsistency in the database)

            $query1 = <<<QUERY
            UPDATE `packages`
            SET number_of_items_sold = number_of_items_sold - :amount
            WHERE package_id = :package;
            QUERY;

            $query2 = <<<QUERY
            DELETE FROM `order_items`
            WHERE `order` = :order AND `package` = :package;
            QUERY;

            $record1 = [
                $query1,
                ['package' => $packageId, 'amount' => $amount]
            ];

            $record2 = [
                $query2,
                ['order' => $orderId, 'package' => $packageId]
            ];

            array_push($queries, $record1, $record2);
        }

        // delete order

        $query = <<<QUERY
        DELETE FROM `orders`
        WHERE `order_id` = :order
        QUERY;

        $orderRecord = [$query, [
            'order' => $order
        ]];

        array_push($queries, $orderRecord);

        $stmt = $repository->queryAll(...$queries);

        if ($stmt !== false && $stmt->rowCount() > 0) {
            return new Response(200, ['Location' => '/ok']);
        }

        return new Response(302, ['Location' => '/failed']);
    }
}