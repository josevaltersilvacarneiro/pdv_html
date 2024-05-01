<?php

declare(strict_types=1);

/**
 * This package is responsible for creating the tests
 * for the classes that implement the GenericDao class.
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
 * @category DaoTests
 * @package  Josevaltersilvacarneiro\Html\Tests\App\Model\Dao
 * @author   José Carneiro <git@josevaltersilvacarneiro.net>
 * @license  GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @link     https://github.com/josevaltersilvacarneiro/html/tree/main/Tests\App/Model/Dao
 */

namespace Josevaltersilvacarneiro\Html\Tests\App\Model\Dao;

use Josevaltersilvacarneiro\Html\App\Model\Dao\UserDao;

use PHPUnit\Framework\TestCase;

/**
 * This class performs a CRUD test on the UserDao class.
 * 
 * @category  UserDaoTest
 * @package   Josevaltersilvacarneiro\Html\Tests\App\Model\Dao
 * @author    José Carneiro <git@josevaltersilvacarneiro.net>
 * @copyright 2023 José Carneiro
 * @license   GPLv3 https://www.gnu.org/licenses/quick-guide-gplv3.html
 * @version   Release: 0.1.0
 * @link      https://github.com/josevaltersilvacarneiro/html/tree/main/Tests\App/Model/Dao
 */
class UserDaoTest extends TestCase
{
    private static UserDao $dao;
    private static array $createdUsers;

    /**
     * This method is executed before of all
     * tests.
     * 
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$dao = new UserDao();
    }

    /**
     * Tests the process of creating a user in the database.
     * 
     * @param array $user User data
     * @param bool  $ok   True if it's expected to work, false otherwise
     * 
     * @return void
     * 
     * @dataProvider returnCreateUserDatabase
     */
    public function testCreateUserDatabase(array $user, bool $ok): void
    {
        $userId = self::$dao->ic($user);

        if ($ok) {
            self::$createdUsers[] = $userId;
            $this->assertIsString($userId);
        } else {
            $this->assertEquals(false, $userId);
        }
    }

    /**
     * This method returns the data to be used in the
     * testCreateUserDatabase method.
     * 
     * @return array<array> Data to be used in the testCreateUserDatabase method
     */
    public static function returnCreateUserDatabase(): array
    {
        $user1 = [
            'employee_id'=> 1,
            'position'  => 1,
            'fullname'  => 'José Valter',
            'email'     => 'test@josevaltersilvacarneiro.net',
            'hash_code' => '$2y$10$I8dud/n/.ew89tN/wZ8xw.zEi6U1zrJfS1c8ffqpKIaklmKIw.Wse',
            'active'    => true
        ];

        $user2 = [
            'fullname' => 'José',
            'position' => 1,
            'email'    => 'test@josevaltersilvacarneiro.net',
            'hash_code'=> '$2y$10$I8dud/n/.ew89tN/wZ8xw.zEi6U1zrJfS1c8ffqpKIaklmKIw.Wse',
            'active'   => false
        ];

        $user3 = [
            'employee_id'   => null,
            'fullname'  => 'Valter Silva',
            'email'     => 'otherother@josevaltersilvacarneiro.net',
            'hash_code' => 'jose',
            'active'    => false
        ];

        return [
            'adding a user normally! This should work'                 => [$user1, true],
            'user with the same email already cannot be added'         => [$user2, false],
            'the length of the hash must be bigger then 60 characters' => [$user3, false],
        ];
    }

    /**
     * @depends testCreateUserDatabase
     * @dataProvider returnCreateIdUserDatabase
     */
    public function testCreateIdUserDatabase(array $user, bool $ok): void
    {
        $id = self::$dao->ic($user);

        if ($ok) {
            self::$createdUsers[] = $id;
            $this->assertIsString($id);
        } else {
            $this->assertEquals(false, $id);
        }
    }

    public static function returnCreateIdUserDatabase(): array
    {
        $user1 = [
            'fullname'  => 'Maria da Silva',
            'email'     => 'test@test.com',
            'hash_code' => '',
            'active'    => false
        ];

        $user2 = [
            'fullname' => 'Maria da Silva',
            'email'    => 'other@test.com',
            'hash_code'     => 'anythinganythinganythinganythinganythinganythinganythinganything',
            'active'   => new \DateTime()
        ];

        $user3 = [
            'fullname'  => 'José',
            'email'     => 'test@test.com',
            'hash_code' => 'otherhash',
            'active'    => true
        ];

        $user4 = [
            'fullname'  => 'José Valter',
            'email'     => 'test@josev.com',
            'hash_code' => 'asasaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'active'    => false,
            'foo'       => 'TEST'
        ];

        return [
            'salt and password cannot be null'                      => [$user1, false],
            'only valid types are accepted'                         => [$user2, false],
            'The fullname must consist of first name and last name' => [$user3, false],
            'fields that doesn\' exist are ignored'                 => [$user4, true]
        ];
    }

    /**
     * @depends testCreateIdUserDatabase
     * @dataProvider returnUpdateUserDatabase
     */

    public function testUpdateUserDatabase(array $user, bool $ok): void
    {
        $this->assertEquals($ok, self::$dao->u($user));
    }

    public static function returnUpdateUserDatabase(): array
    {
        return [
            'UNIQUE CONSTRAINTS cannot be used to change a record' => [array('email' => 'test@test.com', 'fullname' => 'João Reis'), false],
        ];
    }

    /**
     * @dataProvider returnDeleteUserDatabase
     */

    public function testDeleteUserDatabase(array $user, $ok): void
    {
        $this->assertEquals($ok, self::$dao->d($user));
    }

    public static function returnDeleteUserDatabase(): array
    {
        return [
            'only PRIMARY KEYS can be used to delete' => [['test@test.com'], false],
        ];
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanUsers();
    }

    /**
     * This method is responsible for deleting the users
     * created in the process of testing the UserDao class.
     * 
     * @return void
     */
    public static function cleanUsers(): void
    {
        foreach (self::$createdUsers as $user) {
            self::assertEquals(true, self::$dao->d(['employee_id' => $user]));
        }
    }
}
