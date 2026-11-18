<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ModelEnrollmentTest extends TestCase
{
    private ModelEnrollment $model;

    protected function setUp(): void
    {
        TestDb::reset();
        TestDb::seedBasics();
        $this->model = new ModelEnrollment();
    }

    public function testCreateEnrollmentStoresConfirmedBookingAndNormalizesDate(): void
    {
        $created = $this->model->createEnrollment(1, 1, '2026-12-01T14:30');

        $this->assertTrue($created);

        $row = TestDb::conn()->query('SELECT * FROM enrollments')->fetch();
        $this->assertSame('confirmed', $row['status']);
        $this->assertSame(1, (int) $row['user_id']);
        $this->assertSame(1, (int) $row['course_id']);
        $this->assertSame('2026-12-01 14:30:00', $row['enrollment_date']);
    }

    public function testGetEnrollmentsByUserReturnsOnlyOwnBookings(): void
    {
        $this->model->createEnrollment(1, 1, '2026-12-01T10:00');
        $this->model->createEnrollment(2, 3, '2026-12-02T11:00');

        $bookings = $this->model->getEnrollmentsByUser(1);

        $this->assertCount(1, $bookings);
        $this->assertSame('Juukselõikus', $bookings[0]['service_title']);
        $this->assertArrayHasKey('price', $bookings[0]);
        $this->assertArrayHasKey('duration', $bookings[0]);
    }

    public function testGetEnrollmentsByUserWithoutBookingsReturnsEmptyArray(): void
    {
        $this->assertSame([], $this->model->getEnrollmentsByUser(2));
    }

    public function testCreateEnrollmentForMissingServiceFails(): void
    {
        $this->assertFalse($this->model->createEnrollment(1, 9999, '2026-12-01T10:00'));
    }

    public function testCreateEnrollmentForMissingUserFails(): void
    {
        $this->assertFalse($this->model->createEnrollment(9999, 1, '2026-12-01T10:00'));
    }
}
