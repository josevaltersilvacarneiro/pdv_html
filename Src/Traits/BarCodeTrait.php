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
 * @version   Release: 0.0.2
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
    private function _isCodeValid(string $barcode): bool
    {
        // Check if the barcode is a 13-digit number
        if (!preg_match('/^\d{13}$/', $barcode)) {
            return false;
        }

        // Convert the barcode to an array of digits
        $digits = str_split($barcode);

        // Calculate the checksum
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            // If the position is odd, multiply the digit by 1
            // If the position is even, multiply the digit by 3
            $sum += $digits[$i] * (($i % 2 === 0) ? 1 : 3);
        }

        // Calculate the check digit
        $checkDigit = (10 - ($sum % 10)) % 10;

        // Check if the calculated check digit matches the 13th digit of the barcode
        return $checkDigit == $digits[12];
    }
}
