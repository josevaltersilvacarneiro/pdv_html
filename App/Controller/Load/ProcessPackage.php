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

/**
 * This class is responsible for adding a new package to system.
 * 
 * @category  ProcessPackage
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Load
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.2
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class ProcessPackage implements RequestHandlerInterface
{
    use DateTrait;

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
     * This method receives a request and produces a response.
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

        // getting validity

        $due_date = filter_input(INPUT_POST, 'due_date');

        if ($due_date === false || is_null($due_date) || !$this->_isDueDateValid($due_date)) {
            return new Response(302, ['Location' => '/failed']);
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $due_date);
        $today = new \DateTimeImmutable();
        $today = $today->setTime(0, 0, 0, 0);

        if (!$dt || $dt < $today) {
            return new Response(302, ['Location' => '/failed']);
        }

        // already exists a bar code with this number

        $query = <<<QUERY
        SELECT count(*) AS amount FROM packages
        WHERE bar_code = :bar_code
        QUERY;

        $repository = new Repository();

        $stmt = $repository->query($query, ['bar_code' => $code]);
        $count = $stmt === false ? 1 : intval($stmt->fetch(\PDO::FETCH_ASSOC)['amount']);

        if ($count > 0) {
            $query = <<<QUERY
            UPDATE packages
            SET number_of_items_purchase = number_of_items_purchased + :amount
            WHERE bar_code = :code
            LIMIT 1;
            QUERY;

            $stmt = $repository->query($query, ['amount' => $count, 'code' => $code]);
        } else {

            // doesn't exist a bar code with this code

            $record = $repository->cleanCreate('packages', [
                'type_of_product' => $product,
                'bar_code' => $code,
                'number_of_items_purchased' => $amount,
                'validity' => $due_date
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

    /**
     * The method above verifies if the bar code is valid.
     * 
     * @param string $code bar code ean13
     * 
     * @return bool true on success; false otherwise
     */
    private function _isCodeValid(string $code): bool
    {
        $codeArray = array_map(function ($element): int {
            return intval($element);
        }, str_split($code));

        $sumPairs = $codeArray[1] + $codeArray[3] + $codeArray[5] + $codeArray[7] + $codeArray[9] + $codeArray[11];
        $oddSum = $codeArray[0] + $codeArray[2] + $codeArray[4] + $codeArray[6] + $codeArray[8] + $codeArray[10];
        $result = $oddSum + $sumPairs * 3;
        $checkDigit = 10 - $result % 10;

        return $checkDigit === $codeArray[12];
    }
}