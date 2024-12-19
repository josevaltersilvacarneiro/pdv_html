<?php

declare(strict_types=1);

/**
 * The Entity package contains classes that represent the database
 * tables as entities. These entity classes encapsulate the structure
 * and behavior of specific tables, providing a convenient way to
 * interact with the corresponding database records.
 * PHP VERSION >= 8.2.0
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
 * @category Entity
 * @package  Josevaltersilvacarneiro\Html\App\Model\Entity
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/App/Model/Entity
 */

namespace Josevaltersilvacarneiro\Html\App\Model\Entity;

use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\SessionEntityInterface;
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\RequestEntityInterface;
use Josevaltersilvacarneiro\Html\Src\Interfaces\Entities\UserEntityInterface;

use Josevaltersilvacarneiro\Html\App\Model\Entity\Request;
use Josevaltersilvacarneiro\Html\App\Model\Entity\User;

use Josevaltersilvacarneiro\Html\App\Model\Attributes\IpAttribute;
use Josevaltersilvacarneiro\Html\App\Model\Attributes\PortAttribute;
use Josevaltersilvacarneiro\Html\App\Model\Attributes\DateAttribute;

use Josevaltersilvacarneiro\Html\Src\Interfaces\Exceptions\{
    AttributeExceptionInterface};
use Josevaltersilvacarneiro\Html\Src\Interfaces\Exceptions\{
    EntityExceptionInterface};

use Josevaltersilvacarneiro\Html\Src\Classes\Exceptions\{
    EntityException};

/**
 * This class represents a session. It contains properties and methods to manage
 * session-related data and operations.
 * 
 * @var ?UserEntity   $_sessionUser current user of the application
 * @var RequestEntity $_request     request data
 * 
 * @category  Session
 * @package   Josevaltersilvacarneiro\Html\App\Model\Entity
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2024 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.12.0
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/App/Model/Entity
 */
final class Session implements SessionEntityInterface
{
    /**
     * This constructor is responsible for initializing a Session object
     * with the provided values.
     * 
     * @param ?User   $_sessionUser current user of the application
     * @param Request $_request     request data
     * 
     * @return void
     */
    public function __construct(
        #[User("employee")] private ?User $_sessionUser,
        #[Request("last_request")] private Request $_request
    ) {
    }

    /**
     * This method is responsible for setting the user session property,
     * while also validating the user's activity status.
     * 
     * It ensures that the provided user object represents an active user by
     * invoking the isActive method. This validation helps maintain data
     * integrity and ensures that only valid user objects are assigned.
     * 
     * @param UserEntityInterface $user New user
     * 
     * @return static $this
     * @throws EntityExceptionInterface If the provided user isn't active
     */
    public function setUser(UserEntityInterface $user): static
    {
        if ($this->isUserLogged() || !$user->isActive()) {
            throw new EntityException(
                "This session belongs to another user or
				{$user->getFullname()->getRepresentation()} isn't active",
                1
            );
        }

        $this->_sessionUser = $user;
        $this->flush();
        return $this;
    }

    /**
     * This method is responsible for setting the session request,
     * while also validating if the request belonging to the session is
     * older than the current.
     * 
     * @param RequestEntityInterface $request New Request
     * 
     * @return static $this
     * @throws EntityExceptionInterface If the request is old
     */
    public function setRequest(RequestEntityInterface $request): static
    {
        if ($this->getRequest()->getDate() > $request->getDate()) {
            throw new EntityException("The request is old", 1);
        }

        $this->_request = $request;
        $this->flush();
        return $this;
    }

    /**
     * This method returns the user entity.
     * 
     * @return UserEntityInterface|null The user if he is logged in; null otherwise
     */
    public function getUser(): ?UserEntityInterface
    {
        return $this->_sessionUser;
    }

    /**
     * This method returns the request entity.
     * 
     * @return RequestEntityInterface The last request
     */
    public function getRequest(): RequestEntityInterface
    {
        return $this->_request;
    }

    /**
     * This method informs if the user is logged in.
     * 
     * @return bool True if yes; false otherwise
     */
    public function isUserLogged(): bool
    {
        return !is_null($this->getUser());
    }

    /**
     * This method is responsible for creating a new session.
     * 
     * @param array $dependencies Dependencies
     * 
     * @return static|false A new session on success; false otherwise
     */
    public static function fork(array $dependencies = []): static|false
    {
        if (!isset($_SESSION[self::KEYWORD])) {
            return self::_createSession() ?? false;
        }

        $session = unserialize($_SESSION[self::KEYWORD]);

        $request = new Request(
            new IpAttribute(__IP__),
            new PortAttribute(__PORT__),
            new DateAttribute
        );

        $session->setRequest($request);
        return $session;
    }

    /**
     * This method is responsible for creating a new session
     * and return it.
     * 
     * @return static|null A new session on success; null otherwise
     */
    private static function _createSession(): ?static
    {
        try {
            $request = new Request(
                new IpAttribute(__IP__),
                new PortAttribute(__PORT__),
                new DateAttribute
            );

            $session = new Session(
                null,
                $request
            );
        } catch (\InvalidArgumentException | AttributeExceptionInterface) {
            return null;
        }

        $session->flush();

        return $session;
    }

    /**
     * Updates the object representation.
     * 
     * @return true on success; false otherwise
     */
    public function flush(): true
    {
        $_SESSION[self::KEYWORD] = serialize($this);

        return true;
    }

    /**
     * This method is responsible for killing the session.
     * 
     * @return bool True on success; false otherwise
     */
    public function killme(): bool
    {
        if (!session_unset()) return false;
        if (!session_destroy()) return false;

        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        return true;
    }
}
