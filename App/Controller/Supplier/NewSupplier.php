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

namespace Josevaltersilvacarneiro\Html\App\Controller\Supplier;

use Josevaltersilvacarneiro\Html\App\Controller\HTMLController;
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\{
    SessionEntityInterface};

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class is responsible for handling the new supplier
 * page.
 * 
 * @category  NewSupplier
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Supplier
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
class NewSupplier extends HTMLController
{
    /**
     * Initializes the NewSupplier object.
     * 
     * @param SessionEntityInterface $_session session
     * 
     * @return void
     */
    public function __construct(private readonly SessionEntityInterface $_session)
    {
        $this->setPage('NewSupplier');
        $this->setTitle('Cadastre um novo fornecedor');
        $this->setDescription('Página para cadastrar um novo fornecedor para a empresa.');
        $this->setKeywords('PDV Fornecedor josevaltersilvacarneiro Joilma');
    }

    /**
     * Handles the request and returns a response.
     * 
     * @param ServerRequestInterface $request new request
     * 
     * @return ResponseInterface response
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->_session->isUserLogged()) {
            return new Response(302, ['Location' => '/login']);
        }

        return new Response(
            200, [
                'Content-type' => 'text/html;charset=UTF-8'
            ], parent::renderLayout()
        );
    }
}
