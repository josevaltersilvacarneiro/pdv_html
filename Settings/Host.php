<?php

declare(strict_types=1);

/**
 * This comprehensive PHP package is designed to simplify the
 * process of setting up and managing global variables. It
 * provides a convenient and organized approach to define,
 * access, and share variables across different components
 * of the application.
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
 * @package	Settings
 */

define('__ROOT__',
    substr($_SERVER['DOCUMENT_ROOT'] ?? getenv('DOCUMENT_ROOT'), -1) == DIRECTORY_SEPARATOR ?
    $_SERVER['DOCUMENT_ROOT'] ?? getenv('DOCUMENT_ROOT') :
    ($_SERVER['DOCUMENT_ROOT'] ?? getenv('DOCUMENT_ROOT')) . DIRECTORY_SEPARATOR
);
