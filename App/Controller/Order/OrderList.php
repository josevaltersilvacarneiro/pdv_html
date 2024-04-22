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

namespace Josevaltersilvacarneiro\Html\App\Controller\Order;

use Josevaltersilvacarneiro\Html\App\Controller\HTMLController;
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\{
    SessionEntityInterface};

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class is responsive for handling the controller
 * to list the orders.
 * 
 * @category  OrderList
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Order
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2024 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class OrderList extends HTMLController
{
    /**
     * Initializes the OrderList controller.
     * 
     * @param SessionEntityInterface session
     * 
     * @return void
     */
    public function __construct(private readonly SessionEntityInterface $_session)
    {
        $this->setPage('OrderList');
        $this->setTitle('Lista de pedidos');
        $this->setDescription('Página para listar os pedidos e apresentar o botão de editar');
        $this->setKeywords('josevaltersilvacarneiro Joilma editar listar pedidos');
    }

    /**
     * This method handles the process to list the orders.
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

        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);

        $limit = $limit === false || is_null($limit) || $limit < 1 ? 1 : $limit;
        $min = ($limit - 1) * 10;
        $max = $limit * 10;

        $repository = new Repository();

        $query = <<<QUERY
        SELECT (
            SELECT sum(price * amount) AS total FROM `order_items` AS i WHERE i.order = o.order_id
        ) AS total, order_date, order_id
        FROM `orders` AS o
        ORDER BY o.order_date DESC
        LIMIT :min, :max;
        QUERY;

        $stmt = $repository->query($query, ['min' => $min, 'max' => $max]);

        $this->setVariables([
            'ORDER_LIST_' => $stmt === false ? [] : $stmt->fetchAll(\PDO::FETCH_ASSOC),
            'PAGINATION_' => $limit + 1
        ]);

        return new Response(
            200, [
                'Content-Type' => 'text/html;charset=UTF-8'
            ], parent::renderLayout()
        );
    }
}
