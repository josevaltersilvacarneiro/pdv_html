<?php

declare(strict_types=1);

/**
 * This package is responsible for offering useful functions.
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
 * @category Traits
 * @package  Josevaltersilvacarneiro\Html\Src\Traits
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/Src/Traits
 */

namespace Josevaltersilvacarneiro\Html\Src\Traits;

/**
 * This trait handles date.
 * 
 * @category  DateTrait
 * @package   Josevaltersilvacarneiro\Html\Src\Traits
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/Src/Traits
 */
trait DateTrait
{
    /**
     * This method is responsible for validating dates
     * in a give format.
     * 
     * @param string $date date to be validated
     * @param string $format date format
     * 
     * @return bool true if is valid; false otherwise
     */
    private function _isDueDateValid(string $date, string $format = "Y-m-d"): bool
    {
        $dt = \DateTime::createFromFormat($format, $date);
        return $dt && $dt->format($format) === $date;
    }
}
