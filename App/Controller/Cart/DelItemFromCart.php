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
 * Copyright (C) 2024, José V S Carneiro
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

use Josevaltersilvacarneiro\Html\Src\Traits\BarCodeTrait;

/**
 * This class deletes any item from the item order.
 * 
 * @category  DelItemFromCart
 * @package   Josevaltersilvacarneiro\Html\App\Controller\Cart\DelItemFromCart
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2024 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class DelItemFromCart implements RequestHandlerInterface
{
    /**
     * Initializes the DelItemFromCart controller.
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
     * This method receives the request to delete a order item
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
        $package = filter_input(INPUT_GET, 'package', FILTER_VALIDATE_INT);

        if ($order < 1 || $package < 1) {
            return new Response(302, ['Location' => '/failed']);
        }

        $repos = new Repository();

        $query1 = <<<QUERY
        UPDATE `packages`
        SET number_of_items_sold = number_of_items_sold - (SELECT amount FROM `order_items` WHERE `order` = :order AND `package` = :package)
        WHERE package_id = :package;
        QUERY;

        $query2 = <<<QUERY
        DELETE FROM `order_items`
        WHERE `order` = :order AND `package` = :package;
        QUERY;

        $query3 = <<<QUERY
        DELETE o FROM `orders` o
        LEFT JOIN `order_items` oi ON o.order_id = oi.order
        WHERE o.order_id = :order AND oi.order IS NULL;
        QUERY;

        $rec = ['order' => $order, 'package' => $package];
        $stmt = $repos->queryAll([$query1, $rec], [$query2, $rec], [$query3, ['order' => $order]]);

        if ($stmt !== false && $stmt->rowCount() > 0) {
            return new Response(200, ['Location' => '/orders']);
        }

        if ($repos->getTotalRowCount() < 2) {
            return new Response(302, ['Location' => '/failed']);
        }

        return new Response(200, ['Location' => '/bag?order=' . $order]);
    }
}