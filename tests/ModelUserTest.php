<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ModelUserTest extends TestCase
{
    private ModelUser $model;

    protected function setUp(): void
    {
        TestDb::reset();
        $this->model = new ModelUser();
    }

    public function testCreatesUserWithHashedPasswordAndUserRole(): void
    {
        $created = $this->model->createUser('Test Klient', 'test@example.com', '55512345', 'testlogin', 'secret123');

        $this->assertTrue($created);

        $row = $this->model->getUserByUsername('testlogin');
        $this->assertNotFalse($row);
        $this->assertSame('Test Klient', $row['username']);
        $this->assertSame('test@example.com', $row['email']);
        $this->assertSame('user', $row['status']);
        $this->assertSame(date('Y-m-d'), $row['registration_date']);

        $this->assertNotSame('secret123', $row['password'], 'Пароль не должен храниться в открытом виде');
        $this->assertTrue(password_verify('secret123', $row['password']));
    }

    public function testDuplicateLoginIsRejected(): void
    {
        $this->assertTrue($this->model->createUser('Первый', 'one@example.com', null, 'same_login', 'pass1'));
        $this->assertFalse($this->model->createUser('Второй', 'two@example.com', null, 'same_login', 'pass2'));

        $count = (int) TestDb::conn()
            ->query("SELECT COUNT(*) FROM users WHERE login = 'same_login'")
            ->fetchColumn();

        $this->assertSame(1, $count);
    }

    public function testDuplicateEmailIsRejected(): void
    {
        $this->assertTrue($this->model->createUser('Первый', 'same@example.com', null, 'login1', 'pass1'));
        $this->assertFalse($this->model->createUser('Второй', 'same@example.com', null, 'login2', 'pass2'));
    }

    public function testGetUserByUsernameFindsByLogin(): void
    {
        $this->model->createUser('Имя Фамилия', 'find@example.com', null, 'findme', 'pass');

        $row = $this->model->getUserByUsername('findme');

        $this->assertNotFalse($row);
        $this->assertSame('Имя Фамилия', $row['username']);
    }

    public function testGetUserByUsernameFindsByDisplayName(): void
    {
        $this->model->createUser('Mari Maasikas', 'mari2@example.com', null, 'mari2', 'pass');

        $row = $this->model->getUserByUsername('Mari Maasikas');

        $this->assertNotFalse($row);
        $this->assertSame('mari2', $row['login']);
    }

    public function testUnknownUserReturnsFalse(): void
    {
        $this->assertFalse($this->model->getUserByUsername('nobody'));
    }
}
