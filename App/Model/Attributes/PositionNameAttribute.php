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
    PositionNameInterface};
use Josevaltersilvacarneiro\Html\Src\Classes\Exceptions\AttributeException;

/**
 * This class represents a PositionName.
 * 
 * @category  SalaryAttribute
 * @package   Josevaltersilvacarneiro\Html\App\Model\Attributes
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2024 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Model/Attributes
 */
final class PositionNameAttribute implements PositionNameInterface
{
    /**
     * Initializes the attribute.
     * 
     * @param string $_positionName position
     * 
     * @return void
     * @throws AttributeException if isn't a valid name
     */
    public function __construct(private string $_positionName)
    {
        if (mb_strlen($_positionName) < 3) {
            throw new AttributeException(
                "The value \"{$_salary}\" isn't a valid name"
            );
        }
    }

    /**
     * Returns a representation of position.
     * 
     * @return mixed position
     */
    public function getRepresentation(): mixed
    {
        return $this->_positionName;
    }

    /**
     * Returns a new instance of PositionAttribute.
     * 
     * @param mixed $value representation of position name
     * 
     * @return ?static instance on success; false otherwise
     */
    public static function newInstance(mixed $value): ?static
    {
        try {
            return new static($value);
        } catch (AttributeException) {
            return null;
        }
    }
}