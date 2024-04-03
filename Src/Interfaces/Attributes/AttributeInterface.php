<?php

declare(strict_types=1);

/**
 * Based on the project requirements, the interfaces will be written.
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
 * @package  Josevaltersilvacarneiro\Html\Src\Interfaces\Attributes
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/Src/Interfaces/Attributes
 */

namespace Josevaltersilvacarneiro\Html\Src\Interfaces\Attributes;

use Josevaltersilvacarneiro\Html\Src\Interfaces\Exceptions\{
    AttributeExceptionInterface};

/**
 * All attributes must implement this interface.
 * 
 * @category  AttributeInterface
 * @package   Josevaltersilvacarneiro\Html\Src\Interfaces\Attributes
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.0.4
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/Src/Interfaces/Attributes
 */
interface AttributeInterface
{
    /**
     * This method returns the representation of the attribute.
     * For example, if the attribute is a date, it could return
     * a string with the date in the format "Y-m-d".
     * 
     * @return mixed Types that can be converted to fields in a database
     */
    public function getRepresentation(): mixed;

    /**
     * This method returns a new instance of the class that
     * implements this interface.
     * 
     * @param mixed $value Types that can be converted to fields in a database
     * 
     * @return static|null $this on success, null on failure
     * @throws AttributeExceptionInterface If $value is not valid
     */
    public static function newInstance(mixed $value): ?static;
}
