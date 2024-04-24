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
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\{
    SessionEntityInterface};

use Josevaltersilvacarneiro\Html\App\Model\Repository\Repository;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class serves as the main controller for the system's home page,
 * which acts as the primary entry point and central hub for the
 * application.
 * 
 * @category  Home
 * @package   Josevaltersilvacarneiro\Html\App\Controllers\Home
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.2.0
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Cotrollers
 */
final class Home extends HTMLController
{
    /**
     * Initializes the home page controller.
     * 
     * @param SessionEntityInterface $session session
     */
    public function __construct(private readonly SessionEntityInterface $session)
    {
        $this->setPage('Home');
        $this->setTitle('PDV - Bem-vindo');
        $this->setDescription(
            'The home page serves as the central hub of our system, providing users
			with a comprehensive overview and access to various features. It welcomes
			users with a personalized message and offers an intuitive navigation
			menu to explore different sections of the system.'
        );
        $this->setKeywords('PDV josevaltersilvacarneiro Home');
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
        if (!$this->session->isUserLogged()) {
            return new Response(302, ['Location' => '/login']);
        }

        $repository = new Repository();

        $query1 = <<<QUERY
        SELECT
            CASE
                WHEN MONTHNAME(MIN(o.order_date)) = 'January' THEN 'Janeiro'
                WHEN MONTHNAME(MIN(o.order_date)) = 'February' THEN 'Fevereiro'
                WHEN MONTHNAME(MIN(o.order_date)) = 'March' THEN 'Março'
                WHEN MONTHNAME(MIN(o.order_date)) = 'April' THEN 'Abril'
                WHEN MONTHNAME(MIN(o.order_date)) = 'May' THEN 'Maio'
                WHEN MONTHNAME(MIN(o.order_date)) = 'June' THEN 'Junho'
                WHEN MONTHNAME(MIN(o.order_date)) = 'July' THEN 'Julho'
                WHEN MONTHNAME(MIN(o.order_date)) = 'August' THEN 'Agosto'
                WHEN MONTHNAME(MIN(o.order_date)) = 'September' THEN 'Setembro'
                WHEN MONTHNAME(MIN(o.order_date)) = 'October' THEN 'Outubro'
                WHEN MONTHNAME(MIN(o.order_date)) = 'November' THEN 'Novembro'
                WHEN MONTHNAME(MIN(o.order_date)) = 'December' THEN 'Dezembro'
            END AS month, COALESCE(SUM(i.price * i.amount), 0) as total
        FROM `order_items` AS i
        INNER JOIN `orders` AS o
        ON o.order_id = i.`order`
        WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY YEAR(o.order_date), MONTH(o.order_date)
        ORDER BY YEAR(o.order_date), MONTH(o.order_date);
        QUERY;

        $query2 = <<<QUERY
        SELECT COALESCE(SUM(i.price * i.amount), 0) AS total FROM `order_items` AS i
        INNER JOIN `orders` AS o
        ON o.order_id = i.order
        WHERE MONTH(o.order_date) = MONTH(CURRENT_DATE());
        QUERY;

        $query3 = <<<QUERY
        SELECT COALESCE(SUM(purchase_cost), 0) AS total FROM `loads`
        WHERE MONTH(due_date) = MONTH(CURRENT_DATE());
        QUERY;

        $stmt1 = $repository->query($query1);
        $stmt2 = $repository->query($query2);
        $stmt3 = $repository->query($query3);

        $this->setVariables([
            'MONTHLY_SALES_' => $stmt1 === false ? [] : $stmt1->fetchAll(\PDO::FETCH_ASSOC),
            'INCOME_MONTH_' => $stmt2 === false ? 0 : $stmt2->fetch(\PDO::FETCH_ASSOC)['total'],
            'MONTHLY_DEBT_' => $stmt3 === false ? 0 : $stmt3->fetch(\PDO::FETCH_ASSOC)['total'],
        ]);

        return new Response(
            200, [
            'Content-Type'    => 'text/html;charset=UTF-8'
            ], parent::renderLayout()
        );
    }
}
