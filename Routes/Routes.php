<?php

declare(strict_types=1);

/**
 * Where the routes are defined.
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
 * @category Routes
 * @package  Josevaltersilvacarneiro\Html\Routes
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/Routes
 */

return [
    'GET|/' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Home\Home::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],
    'GET|/home' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Home\Home::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],
    'GET|/login' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Login\Login::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],
    'POST|/login/signin' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Login\Signin::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],
    'GET|/logout' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Login\Signout::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],
    'GET|/recover' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Recover\Recover::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],
    'POST|/recover/process' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Recover\ProcessRecovery::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
            \Josevaltersilvacarneiro\Html\Src\Classes\Mail\Mail::class,
        ]
    ],
    'GET|/recover/reset' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Recover\ResetPassword::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],
    'POST|/recover/newpassword' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Recover\NewPassword::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],
    'POST|/login/signout' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Login\Signout::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],
    'GET|/confirm/email' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Confirm\Email::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],

    # Other pages

    'GET|/ok' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\FS\Success::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],

    'GET|/failed' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\FS\Failed::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/items' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Item\ShowItems::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/item' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Item\Item::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class,
        ]
    ],

    'POST|/item/add' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Item\AddItem::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    # the route above shows the item

    'GET|/item/edit' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Item\EditItem::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    # the route above edits the item

    'POST|/item/edit' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Item\EditItem::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    # the route above deletes the item

    'GET|/item/delete' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Item\DeleteItem::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/supplier/add' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Supplier\NewSupplier::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'POST|/supplier/process/add' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Supplier\ProcessNewSupplier::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/ticket' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Ticket\NewTicket::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'POST|/ticket/process/add' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Ticket\ProcessTicket::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/tickets' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Ticket\ShowTickets::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/load' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Load\Load::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'POST|/package/process/add' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Load\ProcessPackage::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/bag' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Cart\Cart::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/bag/del' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Cart\DelItemFromCart::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'POST|/cart/process/add' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Cart\ProcessCart::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/orders' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Order\OrderList::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],

    'GET|/abandon' => [
        'controller' => \Josevaltersilvacarneiro\Html\App\Controller\Cart\AbandonCart::class,
        'dependencies' => [
            \Josevaltersilvacarneiro\Html\App\Model\Entity\Session::class
        ]
    ],
];
