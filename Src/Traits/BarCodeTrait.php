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
 * This trait handles bar code.
 * 
 * @category  BarCodeTrait
 * @package   Josevaltersilvacarneiro\Html\Src\Traits
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/Src/Traits
 */
trait BarcodeTrait
{
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
