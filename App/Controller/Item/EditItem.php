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

namespace Josevaltersilvacarneiro\Html\App\Controller\Item;

use Josevaltersilvacarneiro\Html\App\Controller\HTMLController;
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\{
    SessionEntityInterface};

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class is responsible for showing a page to
 * edit the types of product.
 * 
 * @category  EditItem
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Item
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.3
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class EditItem extends HTMLController
{
    /**
     * Intializes the EditItem controller.
     * 
     * @param SessionEntityInterface $_session session
     */
    public function __construct(private readonly SessionEntityInterface $_session)
    {
        $this->setPage('EditItem');
        $this->setTitle('Edite o título e preço do produto');
        $this->setDescription('Página para editar o título e o preço dos produtos.');
        $this->setKeywords('josevaltersilvacarneiro Editar Joilma PDV');
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

        // verify if the id is set

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === false || is_null($id) || $id < 1) {
            return new Response(302, ['Location' => '/failed']);
        }

        // getting the data based on the ID

        $repository = new Repository();
        $record = $repository->cleanRead('types_of_product', ['type_of_product_id' => $id]);
        if ($record === false) {
            return new Response(302, ['Location' => '/failed']);
        }
        list($query, $record) = $record;
        $stmt = $repository->query($query, $record);
        if ($stmt === false) {
            return new Response(302, ['Location' => '/failed']);
        }
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);

        // validating title

        $title = filter_input(INPUT_POST, 'title');
        $edit_title = $title !== false && !is_null($title);

        if ($edit_title) {
            $title = mb_convert_case($title, MB_CASE_TITLE, "UTF-8");
            $edit_title = $edit_title && !mb_ereg_match($record['title'], $title);
        }

        // validating price

        $price = filter_input(INPUT_POST, 'price');
        $edit_price = $price !== false && !is_null($price) && preg_match('/^(0|[1-9]\d{0,2}(\.\d{3})*),\d{2}$/', $price);

        if ($edit_price) {
            $price = mb_ereg_replace('\.', '', $price);
            $price = mb_ereg_replace(',', '.', $price);
        }

        // updating

        if ($edit_title) {
            $record['title'] = $title;
        }

        if ($record['price'] == $price) {
            $edit_price = false;
        }

        if ($edit_price) {
            $record['price'] = $price;
        }

        if ($edit_title || $edit_price) {
            $rc = $repository->cleanUpdate('types_of_product', $record);
            if ($rc !== false) {
                list($query, $rc) = $rc;
                $stmt = $repository->query($query, $rc);
                if ($stmt === false || $stmt->rowCount() < 1) {
                    return new Response(302, ['Location' => '/failed']);
                }
            }
        }

        // presenting

        $this->setVariables([
            'FORM_ID_' => $id,
            'FORM_TITLE_' => $record['title'],
            'FORM_PRICE_' => mb_ereg_replace('\.', ',', $record['price'])
        ]);

        return new Response(
            200, [
                'Content-Type'  => 'text/html;charset=UTF-8'
            ], parent::renderLayout()
        );
    }
}
