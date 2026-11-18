<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ModelRatingTest extends TestCase
{
    private ModelRating $model;

    protected function setUp(): void
    {
        TestDb::reset();
        TestDb::seedBasics();
        $this->model = new ModelRating();
    }

    public function testInsertsRatingWithReview(): void
    {
        $this->assertTrue($this->model->addOrUpdateRating(1, 1, 5, 'Suurepärane teenus'));

        $row = $this->model->getUserRating(1, 1);
        $this->assertNotFalse($row);
        $this->assertSame(5, (int) $row['rating']);
        $this->assertSame('Suurepärane teenus', $row['review']);
        $this->assertSame(1, (int) $row['user_id']);
        $this->assertSame(1, (int) $row['service_id']);
    }

    public function testSecondSubmissionUpdatesInsteadOfDuplicating(): void
    {
        $this->model->addOrUpdateRating(1, 1, 5, 'Esimene');
        $this->model->addOrUpdateRating(1, 1, 2, 'Muudetud arvamus');

        $count = (int) TestDb::conn()
            ->query('SELECT COUNT(*) FROM ratings WHERE user_id = 1 AND service_id = 1')
            ->fetchColumn();

        $this->assertSame(1, $count, 'На услугу должен остаться ровно один отзыв пользователя');

        $row = $this->model->getUserRating(1, 1);
        $this->assertSame(2, (int) $row['rating']);
        $this->assertSame('Muudetud arvamus', $row['review']);
    }

    public function testAverageRatingAndReviewCount(): void
    {
        $this->model->addOrUpdateRating(1, 1, 5, null);
        $this->model->addOrUpdateRating(2, 1, 3, null);

        $stats = $this->model->getAverageRating(1);

        $this->assertEqualsWithDelta(4.0, (float) $stats['avg_rating'], 0.001);
        $this->assertSame(2, (int) $stats['total_reviews']);
    }

    public function testAverageRatingWithoutReviewsIsNullAndZero(): void
    {
        $stats = $this->model->getAverageRating(2);

        $this->assertNull($stats['avg_rating']);
        $this->assertSame(0, (int) $stats['total_reviews']);
    }

    public function testRatingsByServiceReturnsOnlyThatService(): void
    {
        $this->model->addOrUpdateRating(1, 1, 5, 'Первая услуга');
        $this->model->addOrUpdateRating(2, 2, 4, 'Вторая услуга');

        $rows = $this->model->getRatingsByService(1);

        $this->assertCount(1, $rows);
        $this->assertSame('Первая услуга', $rows[0]['review']);
        $this->assertArrayHasKey('user_name', $rows[0]);
        $this->assertSame('Mari Maasikas', $rows[0]['user_name']);
    }

    public function testAllAverageRatingsAreKeyedByServiceId(): void
    {
        $this->model->addOrUpdateRating(1, 1, 5, null);
        $this->model->addOrUpdateRating(2, 1, 3, null);
        $this->model->addOrUpdateRating(1, 3, 4, null);

        $ratings = $this->model->getAllAverageRatings();

        $this->assertArrayHasKey(1, $ratings);
        $this->assertArrayHasKey(3, $ratings);
        $this->assertArrayNotHasKey(2, $ratings);
        $this->assertEqualsWithDelta(4.0, (float) $ratings[1]['avg_rating'], 0.001);
    }

    public function testMasterStatsAggregateAllServicesOfMaster(): void
    {
        // Kadri Tamm ведёт услуги 1 и 2
        $this->model->addOrUpdateRating(1, 1, 5, null);
        $this->model->addOrUpdateRating(2, 2, 3, null);

        $stats = $this->model->getMasterStats('Kadri Tamm');

        $this->assertEqualsWithDelta(4.0, (float) $stats['avg_rating'], 0.001);
        $this->assertSame(2, (int) $stats['total_reviews']);
        $this->assertSame(2, (int) $stats['total_services']);
    }

    public function testMasterStatsForMasterWithoutRatings(): void
    {
        $stats = $this->model->getMasterStats('Anni Kask');

        $this->assertNull($stats['avg_rating']);
        $this->assertSame(0, (int) $stats['total_reviews']);
    }

    public function testAllMasterStatsGroupsRatingsByMasterName(): void
    {
        $this->model->addOrUpdateRating(1, 1, 5, null);
        $this->model->addOrUpdateRating(2, 3, 4, null);

        $masters = $this->model->getAllMasterStats();

        $this->assertArrayHasKey('Kadri Tamm', $masters);
        $this->assertArrayHasKey('Anni Kask', $masters);
        $this->assertEqualsWithDelta(5.0, (float) $masters['Kadri Tamm']['avg_rating'], 0.001);
        $this->assertEqualsWithDelta(4.0, (float) $masters['Anni Kask']['avg_rating'], 0.001);
    }

    public function testMasterNamesAreDistinctAndSorted(): void
    {
        $services = new ModelServices();

        $this->assertSame(['Anni Kask', 'Kadri Tamm'], $services->getMasterNames());
    }
}
