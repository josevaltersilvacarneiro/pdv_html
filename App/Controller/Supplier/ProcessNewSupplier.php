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

use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\SessionEntityInterface;

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * This class process a new register to supplier.
 * 
 * @category  ProcessNewSupplier
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Supplier
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.2
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class ProcessNewSupplier implements RequestHandlerInterface
{
    /**
     * Initializes the controller.
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
     * Handles the request and produces a response.
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

        // validating name and CNPJ

        $name = filter_input(INPUT_POST, 'name');
        $cnpj = filter_input(INPUT_POST, 'cnpj', FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => "/\d{2}\.?\d{3}\.?\d{3}\/?\d{4}\-?\d{2}/"]
        ]);

        if ($name === false || is_null($name) || $cnpj === false || is_null($cnpj)) {
            return new Response(302, ['Location' => '/failed']);
        }

        $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');

        if (!$this->_isCNPJValid($cnpj)) {
            return new Response(302, ['Location' => '/failed']);
        }

        // Remover caracteres especias
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // including on database

        $repository = new Repository();
        $record = $repository->cleanCreate('suppliers', ['name' => $name, 'cnpj' => $cnpj]);

        if ($record === false) {
            return new Response(302, ['Location' => '/failed']);
        }

        list($query, $record) = $record;
        $stmt = $repository->query($query, $record);

        if ($stmt === false || $stmt->rowCount() < 1) {
            return new Response(302, ['Location' => '/failed']);
        }

        // return on success

        return new Response(200, ['Location' => '/ok']);
    }

    /**
     * This function validates the CNPJ and returns its
     * status (true on success; false otherwise).
     * 
     * @param string $cnpj variable to be validated
     * 
     * @return bool true on success; false otherwise
     */
    private function _isCNPJValid(string $cnpj): bool
    {
        // Verificar se foi informado
        if(empty($cnpj)) {
            return false;
        }

        // Remover caracteres especias
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Verifica se o numero de digitos informados
        if (mb_strlen($cnpj) != 14) {
            return false;
        }
    
        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0, $n = 0; $i < 12; $n += $cnpj[$i] * $b[++$i]) ;
  
        if ($cnpj[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($i = 0, $n = 0; $i <= 12; $n += $cnpj[$i] * $b[$i++]) ;

        if ($cnpj[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }
}