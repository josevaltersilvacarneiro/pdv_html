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

namespace Josevaltersilvacarneiro\Html\App\Controller\Ticket;

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Josevaltersilvacarneiro\Html\App\Controller\HTMLController;
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\{
    SessionEntityInterface};

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class is responsible for handling the ShowTickets
 * page.
 * 
 * @category  ShowTickets
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Ticket
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.2
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class ShowTickets extends HTMLController
{
    /**
     * Initializes the controller.
     * 
     * @param SessionEntityInterface $_session
     * 
     * @return void
     */
    public function __construct(private readonly SessionEntityInterface $_session)
    {
        $this->setPage('ShowTickets');
        $this->setTitle('Lista dos boletos');
        $this->setDescription('Página para listar os boletos.');
        $this->setKeywords('PDV Joilma Boletos');
    }

    /**
     * Gets a request and produces a response.
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

        // getting the variable limit

        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);

        $limit = $limit === false || is_null($limit) || $limit < 1 ? 1 : $limit;
        $min = ($limit - 1) * 10;
        $max = $limit * 10;

        $query = <<<QUERY
        SELECT s.name, l.purchase_cost, l.due_date FROM `loads` AS l
        INNER JOIN suppliers AS s
        ON l.supplier = s.supplier_id
        ORDER BY l.due_date DESC
        LIMIT :min, :max;
        QUERY;

        $record = ['min' => $min, 'max' => $max];

        // searching

        $repository = new Repository();

        $stmt = $repository->query($query, $record);

        $this->setVariables([
            'TICKET_LIST_' => $stmt === false ? [] : $stmt->fetchAll(\PDO::FETCH_ASSOC),
            'PAGINATION_' => $limit + 1
        ]);

        return new Response(
            200, [
                'Content-Type' => 'text/html;charset=UTF-8'
            ], parent::renderLayout()
        );
    }
}
