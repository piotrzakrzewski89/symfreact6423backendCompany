<?php

declare(strict_types=1);

namespace App\Tests\UI\Http\Controller;

use App\Tests\BaseTestController;

class UserControllerTest extends BaseTestController
{
    public function testOfTestInit(): void
    {
        self::assertTrue(true);
    }
    // public function testListUser(): void
    // {
    //     $this->request('GET', '/api/list-users', [], [], ['CONTENT_TYPE' => 'application/json']);

    //     $this->assertResponseIsSuccessful();
    //     $data = json_decode($this->response()->getContent(), true);
    //     $this->assertEquals([], $data);

    //     $connection = static::getContainer()->get('database_connection');

    //     $result = $connection->prepare('SELECT * FROM "user" u WHERE u."id" = 1')->executeQuery()->fetchOne();
    //     $this->assertEquals(1, $result);
    // }

    // public function testCreateUser(): void
    // {
    //     $this->request(
    //         'POST',
    //         '/api/new-user',
    //         [],
    //         [],
    //         ['CONTENT_TYPE' => 'application/json'],
    //         json_encode(
    //             [
    //                 'email' => 'test@example.com',
    //                 'password' => $_ENV['TEST_USER_PASSWORD'],
    //                 'firstName' => 'John',
    //                 'lastName' => 'Doe',
    //                 'employeeNumber' => 'EMP001',
    //                 'isActive' => 'true',
    //             ]
    //         )
    //     );

    //     $this->assertResponseIsSuccessful();
    //     $this->assertResponseStatusCodeSame(200);
    // }
}
