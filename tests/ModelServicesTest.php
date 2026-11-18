<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ModelServicesTest extends TestCase
{
    private ModelServices $model;

    protected function setUp(): void
    {
        TestDb::reset();
        TestDb::seedBasics();
        $this->model = new ModelServices();
    }

    public function testServicesByCategoryReturnOnlyThatCategory(): void
    {
        $services = $this->model->getServicesByCategory(1);

        $this->assertCount(2, $services);
        foreach ($services as $service) {
            $this->assertSame(1, (int) $service['category_id']);
        }
    }

    public function testServicesByCategoryWithoutMatchesReturnsEmptyArray(): void
    {
        $this->assertSame([], $this->model->getServicesByCategory(3));
    }

    public function testAllServicesIncludeCategoryName(): void
    {
        $services = $this->model->getAllServices();

        $this->assertCount(3, $services);
        $this->assertArrayHasKey('category_name', $services[0]);
        $names = array_column($services, 'category_name');
        $this->assertContains('Juuksuriteenused', $names);
        $this->assertContains('Küünehooldus', $names);
    }

    public function testGetServiceByIdReturnsServiceWithCategory(): void
    {
        $service = $this->model->getServiceById(2);

        $this->assertNotFalse($service);
        $this->assertSame('Värvimine', $service['title']);
        $this->assertSame('Kadri Tamm', $service['master_name']);
        $this->assertSame('Juuksuriteenused', $service['category_name']);
    }

    public function testGetServiceByIdForUnknownIdReturnsFalse(): void
    {
        $this->assertFalse($this->model->getServiceById(9999));
    }

    public function testCategoriesAreReturnedInIdOrder(): void
    {
        $categories = $this->model->getCategories();

        $this->assertCount(2, $categories);
        $this->assertSame('Juuksuriteenused', $categories[0]['name']);
        $this->assertSame('Küünehooldus', $categories[1]['name']);
    }

    public function testServicesWithoutMasterAreExcludedFromMasterNames(): void
    {
        TestDb::conn()->exec("INSERT INTO services (id, category_id, title, master_name, price, is_active)
                              VALUES (4, 2, 'Ilma meistrita', NULL, 10.00, 1)");

        $masters = $this->model->getMasterNames();

        $this->assertNotContains(null, $masters);
        $this->assertSame(['Anni Kask', 'Kadri Tamm'], $masters);
    }
}
