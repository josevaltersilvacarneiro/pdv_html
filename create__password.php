<?php

/**
 * This script creates a hash according to the password typed.
 * PHP VERSION 8.2.0
 * 
 * Copyright (C) 2024, José V S Carneiro
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
 * @category FrontController
 * @package  Josevaltersilvacarneiro\Html
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  https://www.gnu.org/licenses/quick-guide-gplv3.html GPLv3
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/
 */

$email = "uefs@josevaltersilvacarneiro.net";
$password = "TEST123456abcd";

$hashCode = password_hash($password, PASSWORD_ARGON2ID);

echo $email . PHP_EOL;
echo $hashCode . PHP_EOL;
echo password_verify($password, $hashCode) . PHP_EOL;