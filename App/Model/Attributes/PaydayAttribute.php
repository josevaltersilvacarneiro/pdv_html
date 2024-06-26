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

use Josevaltersilvacarneiro\Html\Src\Interfaces\Attributes\AttributeInterface;

/**
 * This class represents the payday of the employee.
 * 
 * @category  Payday
 * @package   Josevaltersilvacarneiro\Html\App\Model\Attribute
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Model/Attribute
 */
class PaydayAttribute implements AttributeInterface
{
    /**
     * Initializes the attribute.
     * 
     * @param string $_value its value
     */
    public function __construct(private string $_value)
    {
    }

    /**
     * This method returns the representation of the attribute.
     * 
     * @return mixed its value
     */
    public function getRepresentation(): mixed
    {
        return $this->_value;
    }

    /**
     * This method returns a new instance.
     * 
     * @param mixed $value to initialize.
     * 
     * @return ?static a new instance on success; null otherwise.
     */
    public static function newInstance(mixed $value): ?static
    {
        return new static((string) $value);
    }
}
