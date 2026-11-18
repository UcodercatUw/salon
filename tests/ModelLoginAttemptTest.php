<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ModelLoginAttemptTest extends TestCase
{
    private ModelLoginAttempt $model;

    protected function setUp(): void
    {
        TestDb::reset();
        $this->model = new ModelLoginAttempt();
    }

    public function testUnknownKeyIsNotLocked(): void
    {
        $this->assertSame(0, $this->model->isLocked('key-unknown'));
    }

    public function testFourFailuresDoNotLock(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->model->registerFailure('key-four');
        }

        $this->assertSame(0, $this->model->isLocked('key-four'));
    }

    public function testFiveFailuresLockForFifteenMinutes(): void
    {
        $failures = 0;
        for ($i = 0; $i < 5; $i++) {
            $failures = $this->model->registerFailure('key-five');
        }

        $this->assertSame(ModelLoginAttempt::MAX_ATTEMPTS, $failures);

        $remaining = $this->model->isLocked('key-five');
        $this->assertGreaterThan(0, $remaining);
        $this->assertLessThanOrEqual(ModelLoginAttempt::LOCKOUT_SECONDS, $remaining);
    }

    public function testSuccessClearsCounter(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->model->registerFailure('key-clear');
        }

        $this->model->clear('key-clear');

        $this->assertSame(1, $this->model->registerFailure('key-clear'));
        $this->assertSame(0, $this->model->isLocked('key-clear'));
    }

    public function testLockExpiresAndCounterStartsOver(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->model->registerFailure('key-expire');
        }

        TestDb::conn()
            ->prepare("UPDATE login_attempts SET last_attempt = :old WHERE login_key = 'key-expire'")
            ->execute([':old' => date('Y-m-d H:i:s', time() - ModelLoginAttempt::LOCKOUT_SECONDS - 60)]);

        $this->assertSame(0, $this->model->isLocked('key-expire'));
        $this->assertSame(1, $this->model->registerFailure('key-expire'));
    }

    public function testDifferentKeysAreCountedSeparately(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->model->registerFailure('key-a');
        }

        $this->assertGreaterThan(0, $this->model->isLocked('key-a'));
        $this->assertSame(0, $this->model->isLocked('key-b'));
    }
}
