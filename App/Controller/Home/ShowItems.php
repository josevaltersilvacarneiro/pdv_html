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

namespace Josevaltersilvacarneiro\Html\App\Controller\Home;

use Josevaltersilvacarneiro\Html\App\Controller\HTMLController;
use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\{
    SessionEntityInterface};

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class is responsible for controlling the search
 * for items.
 * 
 * @category  ShowItems
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\ShowItems
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class ShowItems extends HTMLController
{
    /**
     * Initializes thw ShowItems Controller.
     * 
     * @param SessionEntityInterface $_session session
     * 
     * @return void
     */
    public function __construct(private readonly SessionEntityInterface $_session)
    {
        $this->setPage("ShowItems");
        $this->setTitle("Produtos cadastrados");
        $this->setDescription("Página com a lista dos produtos cadastrados.");
        $this->setKeywords("PDV Joilma Produto josevaltersilvacarneiro");
    }

    /**
     * Handles the request and returns a response.
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

        // getting the variables limit and product

        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);

        $limit = $limit === false || is_null($limit) || $limit < 1 ? 1 : $limit;
        $min = ($limit - 1) * 10;
        $max = $limit * 10;

        $product = filter_input(INPUT_GET, 'product');

        $record = ['min' => $min, 'max' => $max];
        if ($product === false || is_null($product)) {

            $product = '';

            $query = <<<QUERY
            SELECT type_of_product_id, title FROM `types_of_product`
            LIMIT :min, :max;
            QUERY;
        } else {
            $query = <<<QUERY
            SELECT type_of_product_id, title FROM `types_of_product`
            WHERE title LIKE :pattern
            LIMIT :min, :max;
            QUERY;

            $record['pattern'] = '%' . mb_ereg_replace(' ', '%', $product) . '%';
        }

        $repository = new Repository();
        $stmt = $repository->query($query, $record);

        $this->setVariables([
            'PRODUCT_LIST_' => $stmt === false ? [] : $stmt->fetchAll(\PDO::FETCH_ASSOC),
            'PRODUCT_SEARCH_' => $product
        ]);

        return new Response(
            200, [
                'Content-Type' => 'text/html;charset=UTF-8'
            ], parent::renderLayout()
        );
    }
}
