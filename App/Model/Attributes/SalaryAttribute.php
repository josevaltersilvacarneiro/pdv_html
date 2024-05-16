<?php

declare(strict_types=1);

/**
 * This package conatins the attributes of the entities.
 * PHP VERSION >= 8.2.0
 * 
 * Copyright (C) 2023, José Carneiro
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
 * @category Attributes
 * @package  Josevaltersilvacarneiro\Html\App\Model\Attributes
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/App/Model/Attributes
 */

namespace Josevaltersilvacarneiro\Html\App\Model\Attributes;

use Josevaltersilvacarneiro\Html\Src\Interfaces\Attributes\{
    SalaryAttributeInterface};
use Josevaltersilvacarneiro\Html\Src\Classes\Exceptions\AttributeException;

/**
 * This class represents a SalaryAttribute.
 * 
 * @category  SalaryAttribute
 * @package   Josevaltersilvacarneiro\Html\App\Model\Attributes
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2024 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.2
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Model/Attributes
 */
final class SalaryAttribute implements SalaryAttributeInterface
{
    /**
     * Initializes the attribute.
     * 
     * @param float $_salary salary
     * 
     * @return void
     * @throws AttributeException if isn't a valid salary
     */
    public function __construct(private float $_salary)
    {
        if ($_salary < 0) {
            throw new AttributeException(
                "The value \"{$_salary}\" isn't a valid salary"
            );
        }
    }

    /**
     * Returns a representation of salary to be
     * stored.
     * 
     * @return mixed salary
     */
    public function getRepresentation(): mixed
    {
        return number_format($this->_salary, 2);
    }

    /**
     * Returns a new instance of this attribute.
     * 
     * @param mixed $value representation of a salary
     * 
     * @return ?static instance on success; false otherwise
     */
    public static function newInstance(mixed $value): ?static
    {
        if (!is_numeric($value)) {
            return null;
        }

        try {
            return new static(floatval($value));
        } catch (AttributeException) {
            return null;
        }
    }
}