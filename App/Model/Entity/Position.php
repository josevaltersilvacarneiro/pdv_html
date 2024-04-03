<?php

declare(strict_types=1);

/**
 * The Entity package contains classes that represent the database
 * tables as entities. These entity classes encapsulate the structure
 * and behavior of specific tables, providing a convenient way to
 * interact with the corresponding database records.
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
 * @category Entity
 * @package  Josevaltersilvacarneiro\Html\App\Model\Entity
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/App/Model/Entity
 */

use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\PositionEntityInterface;

use Josevaltersilvacarneiro\Html\App\Model\Entity\EntityWithIncrementalPrimaryKey;

use Josevaltersilvacarneiro\Html\App\Model\Attributes\IncrementalPrimaryKeyAttribute;
use Josevaltersilvacarneiro\Html\App\Model\Attributes\NameAttribute;
use Josevaltersilvacarneiro\Html\App\Model\Attributes\PaydayAttribute;

/**
 * This class represents the position of the employee.
 * 
 * @category  Payday
 * @package   Josevaltersilvacarneiro\Html\App\Model\Entity
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.1
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Model/Entity
 */
final class Position extends EntityWithIncrementalPrimaryKey implements
    PositionEntityInterface
{
    /**
     * Initializes the object.
     */
    public function __construct(
        #[IncrementalPrimaryKeyAttribute('position_id')]
        private ?IncrementalPrimaryKeyAttribute $_positionId,
        #[NameAttribute('name')] private ?NameAttribute $_name,
        #[PaydayAttribute('payday')] private ?PaydayAttribute $_payday)
    {
    }

    /**
     * Returns the property's name that represents
     * the primary key.
     * 
     * @return string primary key
     */
    public static function getIdName(): string
    {
        return '_positionId';
    }
}
