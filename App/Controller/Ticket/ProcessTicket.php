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

use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\SessionEntityInterface;

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;

use Josevaltersilvacarneiro\Html\Src\Traits\DateTrait;

/**
 * This class process the new billet.
 * 
 * @category  ProcessTicket
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Ticket
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.3
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class ProcessTicket implements RequestHandlerInterface
{
    use DateTrait;

    /**
     * Initializes the ProcessTicket controller.
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
     * Handles a request and returns a response.
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

        // processing the price

        $price = filter_input(INPUT_POST, 'price');

        if ($price === false || is_null($price) || !preg_match('/^(0|[1-9]\d{0,2}(\.\d{3})*),\d{2}$/', $price)) {
            return new Response(302, ['Location' => '/failed']);
        }

        $price = mb_ereg_replace('\.', '', $price);
        $price = mb_ereg_replace(',', '.', $price);

        // processing due date

        $dueDate = filter_input(INPUT_POST, 'due_date');
        
        if ($dueDate === false || is_null($dueDate) || !$this->_isDueDateValid($dueDate)) {
            return new Response(302, ['Location' => '/failed']);
        }

        // processing supplier

        $supplier = filter_input(INPUT_POST, 'supplier', FILTER_VALIDATE_INT);

        if ($supplier === false || is_null($supplier) || $supplier < 1) {
            return new Response(302, ['Location' => '/failed']);
        }

        // inserting on database

        $repository = new Repository();

        $record = ['supplier' => $supplier, 'purchase_cost' => $price, 'due_date' => $dueDate];

        $record = $repository->cleanCreate('loads', $record);

        if ($record === false) {
            return new Response(302, ['Location' => '/failed']);
        }

        list($query, $record) = $record;
        $stmt = $repository->query($query, $record);

        if ($stmt !== false && $stmt->rowCount() > 0) {
            return new Response(200, ['Location' => '/ok']);
        }

        return new Response(302, ['Location' => '/failed']);
    }
}
