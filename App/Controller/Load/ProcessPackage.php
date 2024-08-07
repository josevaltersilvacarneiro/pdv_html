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

namespace Josevaltersilvacarneiro\Html\App\Controller\Load;

use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\SessionEntityInterface;

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;

use Josevaltersilvacarneiro\Html\Src\Traits\DateTrait;
use Josevaltersilvacarneiro\Html\Src\Traits\BarCodeTrait;

/**
 * This class is responsible for adding a new package to system.
 * 
 * @category  ProcessPackage
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Load
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.1.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class ProcessPackage implements RequestHandlerInterface
{
    use DateTrait;
    use BarCodeTrait;

    /**
     * Initializes the ProcessPackage controller.
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
     * This method receives a request and produces a response
     * to register a new package.
     * 
     * If the bar code doesn't registered, create it adding
     * the VALIDITY and TYPE OF PRODUCT; but, if the bar
     * code already was registered, just increments the
     * NUMBER OF ITEMS PURCHASED according to the amount
     * variable, that is, only NUMBER OF ITEMS PURCHASED
     * is modified (the other parameters are ignored).
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

        // getting the code

        $code = filter_input(INPUT_POST, 'bar_code', FILTER_VALIDATE_REGEXP, [
            'options' => ['regexp' => '/^[0-9]{13}$/']
        ]);

        if ($code === false || is_null($code) || !$this->_isCodeValid($code)) {
            return new Response(302, ['Location' => '/failed']);
        }

        // getting product

        $product = filter_input(INPUT_POST, 'product', FILTER_VALIDATE_INT);

        if ($product === false || is_null($product) || $product < 1) {
            return new Response(302, ['Location' => '/failed']);
        }

        // getting amount

        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);

        if ($amount === false || is_null($amount) || $amount < 1) {
            return new Response(302, ['Location' => '/failed']);
        }

        // if this bar code already be registered

        $query = <<<QUERY
        SELECT COUNT(*) AS amount FROM packages
        WHERE bar_code = :bar_code
        QUERY;

        $repository = new Repository();

        $stmt = $repository->query($query, ['bar_code' => $code]);

        if ($stmt === false) {
            return new Response(302, ['Location' => '/failed']);
        }

        $count = intval($stmt->fetch(\PDO::FETCH_ASSOC)['amount']);

        // already a bar code with this number

        if ($count > 0) {
            $query = <<<QUERY
            UPDATE packages
            SET number_of_items_purchased = number_of_items_purchased + :amount
            WHERE bar_code = :code
            LIMIT 1;
            QUERY;

            $stmt = $repository->query($query, ['amount' => $amount, 'code' => $code]);
        } else {

            // doesn't exist a bar code with this code

            $record = $repository->cleanCreate('packages', [
                'type_of_product' => $product,
                'bar_code' => $code,
                'number_of_items_purchased' => $amount
            ]);

            if ($record === false) {
                return new Response(302, ['Location' => '/failed']);
            }

            list($query, $record) = $record;
            $stmt = $repository->query($query, $record);
        }

        if ($stmt === false || $stmt->rowCount() < 1) {
            return new Response(302, ['Location' => '/failed']);
        }

        return new Response(200, ['Location' => '/ok']);
    }
}