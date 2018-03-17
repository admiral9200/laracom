<?php

namespace Tests\Unit\OrderDetails;

use App\Shop\Addresses\Address;
use App\Shop\Carts\Repositories\CartRepository;
use App\Shop\Carts\ShoppingCart;
use App\Shop\Couriers\Courier;
use App\Shop\Customers\Customer;
use App\Shop\OrderDetails\OrderProduct;
use App\Shop\OrderDetails\Repositories\OrderProductRepository;
use App\Shop\Orders\Order;
use App\Shop\Orders\Repositories\OrderRepository;
use App\Shop\OrderStatuses\OrderStatus;
use App\Shop\Products\Product;
use App\Shop\Products\Repositories\ProductRepository;
use Tests\TestCase;

class OrderDetailsUnitTest extends TestCase
{
    /** @test */
    public function it_can_build_the_order_details()
    {
        $cartRepo = new CartRepository(new ShoppingCart);
        $cartRepo->addToCart($this->product, 1);
        $cartRepo->saveCart($this->customer);

        $order = factory(Order::class)->create();

        $orderProductRepo = new OrderProductRepository(new OrderProduct);
        $orderProductRepo->buildOrderDetails($order, $cartRepo->getCartItems());

        $orderRepo = new OrderRepository(new Order);
        $products = $orderRepo->findProducts($order);

        $products->each(function (Product $product) {
            $this->assertEquals($this->product->name, $product->name);
            $this->assertEquals($this->product->sku, $product->sku);
            $this->assertEquals($this->product->slug, $product->slug);
            $this->assertEquals($this->product->description, $product->description);
            $this->assertEquals($this->product->cover, $product->cover);
            $this->assertEquals($this->product->price, $product->price);
            $this->assertEquals($this->product->status, $product->status);
        });
    }
    
    /** @test */
    public function it_can_show_all_the_products_attached_to_an_order()
    {
        $customer = factory(Customer::class)->create();
        $courier = factory(Courier::class)->create();
        $address = factory(Address::class)->create();
        $orderStatus = factory(OrderStatus::class)->create();

        $data = [
            'reference' => $this->faker->uuid,
            'courier_id' => $courier->id,
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_status_id' => $orderStatus->id,
            'payment' => 'paypal',
            'discounts' => 10.50,
            'total_products' =>  100,
            'tax' => 10.00,
            'total' => 100.00,
            'total_paid' => 100,
            'invoice' => null,
        ];

        $orderRepo = new OrderRepository(new Order);
        $order = $orderRepo->createOrder($data);

        $productRepo = new ProductRepository(new Product);

        $p = $this->faker->word;

        $params = [
            'sku' => $this->faker->numberBetween(1111111, 999999),
            'name' => $p,
            'slug' => str_slug($p),
            'description' => $this->faker->paragraph,
            'cover' => null,
            'quantity' => $this->faker->randomDigit,
            'price' => $this->faker->randomFloat(2, 10, 999),
            'status' => 1
        ];

        $product = $productRepo->createProduct($params);
        $quantity = $this->faker->randomDigit;

        $orderDetailRepo = new OrderProductRepository(new OrderProduct);
        $orderDetailRepo->createOrderDetail($order, $product, $quantity);

        $lists = $orderRepo->findProducts($order);

        foreach ($lists as $list) {
            $this->assertEquals($product->id, $list->id);
            $this->assertEquals($product->name, $list->name);
        }
    }
    
    /** @test */
    public function it_can_create_an_order_detail()
    {
        $customer = factory(Customer::class)->create();
        $courier = factory(Courier::class)->create();
        $address = factory(Address::class)->create();
        $orderStatus = factory(OrderStatus::class)->create();

        $data = [
            'reference' => $this->faker->uuid,
            'courier_id' => $courier->id,
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_status_id' => $orderStatus->id,
            'payment' => 'paypal',
            'discounts' => 10.50,
            'total_products' =>  100,
            'tax' => 10.00,
            'total' => 100.00,
            'total_paid' => 100,
            'invoice' => null,
        ];

        $orderRepo = new OrderRepository(new Order);
        $order = $orderRepo->createOrder($data);

        $productRepo = new ProductRepository(new Product);

        $p = $this->faker->word;
        $params = [
            'sku' => $this->faker->numberBetween(1111111, 999999),
            'name' => $p,
            'slug' => str_slug($p),
            'description' => $this->faker->paragraph,
            'cover' => null,
            'quantity' => $this->faker->randomDigit,
            'price' => $this->faker->randomFloat(2),
            'status' => 1
        ];

        $product = $productRepo->createProduct($params);
        $quantity = $this->faker->randomDigit;

        $orderDetailRepo = new OrderProductRepository(new OrderProduct);
        $orderDetails = $orderDetailRepo->createOrderDetail($order, $product, $quantity);

        foreach ($orderDetails as $detail) {
            $this->assertEquals($product->id, $detail->pivot->product_id);
            $this->assertEquals($order->id, $detail->pivot->order_id);
            $this->assertEquals($quantity, $detail->pivot->quantity);
        }
    }
}
