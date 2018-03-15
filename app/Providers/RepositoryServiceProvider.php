<?php

namespace App\Providers;

use App\Shop\Addresses\Repositories\AddressRepository;
use App\Shop\Addresses\Repositories\Interfaces\AddressRepositoryInterface;
use App\Shop\Attributes\Repositories\AttributeRepository;
use App\Shop\Attributes\Repositories\AttributeRepositoryInterface;
use App\Shop\AttributeValues\Repositories\AttributeValueRepository;
use App\Shop\AttributeValues\Repositories\AttributeValueRepositoryInterface;
use App\Shop\Carts\Repositories\CartRepository;
use App\Shop\Carts\Repositories\Interfaces\CartRepositoryInterface;
use App\Shop\Categories\Repositories\CategoryRepository;
use App\Shop\Categories\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Shop\Cities\Repositories\CityRepository;
use App\Shop\Cities\Repositories\Interfaces\CityRepositoryInterface;
use App\Shop\Countries\Repositories\CountryRepository;
use App\Shop\Countries\Repositories\Interfaces\CountryRepositoryInterface;
use App\Shop\Couriers\Repositories\CourierRepository;
use App\Shop\Couriers\Repositories\Interfaces\CourierRepositoryInterface;
use App\Shop\Customers\Repositories\CustomerRepository;
use App\Shop\Customers\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Shop\Employees\Repositories\EmployeeRepository;
use App\Shop\Employees\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Shop\OrderDetails\Repositories\Interfaces\OrderProductRepositoryInterface;
use App\Shop\OrderDetails\Repositories\OrderProductRepository;
use App\Shop\Orders\Repositories\Interfaces\OrderRepositoryInterface;
use App\Shop\Orders\Repositories\OrderRepository;
use App\Shop\OrderStatuses\Repositories\Interfaces\OrderStatusRepositoryInterface;
use App\Shop\OrderStatuses\Repositories\OrderStatusRepository;
use App\Shop\PaymentMethods\Repositories\Interfaces\PaymentMethodRepositoryInterface;
use App\Shop\PaymentMethods\Repositories\PaymentMethodRepository;
use App\Shop\ProductAttributes\Repositories\ProductAttributeRepository;
use App\Shop\ProductAttributes\Repositories\ProductAttributeRepositoryInterface;
use App\Shop\Products\Repositories\Interfaces\ProductRepositoryInterface;
use App\Shop\Products\Repositories\ProductRepository;
use App\Shop\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Shop\Provinces\Repositories\ProvinceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            OrderProductRepositoryInterface::class,
            OrderProductRepository::class
        );

        $this->app->bind(
            ProductAttributeRepositoryInterface::class,
            ProductAttributeRepository::class
        );

        $this->app->bind(
            AttributeValueRepositoryInterface::class,
            AttributeValueRepository::class
        );

        $this->app->bind(
            AttributeRepositoryInterface::class,
            AttributeRepository::class
        );

        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class
        );

        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        $this->app->bind(
            AddressRepositoryInterface::class,
            AddressRepository::class
        );

        $this->app->bind(
            CountryRepositoryInterface::class,
            CountryRepository::class
        );

        $this->app->bind(
            ProvinceRepositoryInterface::class,
            ProvinceRepository::class
        );

        $this->app->bind(
            CityRepositoryInterface::class,
            CityRepository::class
        );

        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );

        $this->app->bind(
            OrderStatusRepositoryInterface::class,
            OrderStatusRepository::class
        );

        $this->app->bind(
            CourierRepositoryInterface::class,
            CourierRepository::class
        );

        $this->app->bind(
            PaymentMethodRepositoryInterface::class,
            PaymentMethodRepository::class
        );

        $this->app->bind(
            CartRepositoryInterface::class,
            CartRepository::class
        );
    }
}
