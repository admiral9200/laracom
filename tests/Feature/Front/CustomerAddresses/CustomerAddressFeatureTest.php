<?php

namespace Tests\Feature\Front\CustomerAddresses;

use App\Shop\Cities\City;
use App\Shop\Countries\Country;
use App\Shop\Provinces\Province;
use Tests\TestCase;

class CustomerAddressFeatureTest extends TestCase
{
    /** @test */
    public function it_can_show_the_list_of_address_of_the_customer()
    {
        $this
            ->actingAs($this->customer, 'web')
            ->get(route('customer.address.index', $this->customer->id))
            ->assertStatus(200)
            ->assertSee('Alias')
            ->assertSee('Address 1')
            ->assertSee('Country')
            ->assertSee('Province')
            ->assertSee('City')
            ->assertSee('Zip Code');
    }

    /** @test */
    public function it_can_save_the_customer_address()
    {
        $country = factory(Country::class)->create();
        $province = factory(Province::class)->create([
            'country_id' => $country->id
        ]);
        $city = factory(City::class)->create([
            'province_id' => $province->id
        ]);

        $data = [
            'alias' => 'home',
            'address_1' => $this->faker->address,
            'city_id' => $city->id,
            'province_id' => $province->id,
            'country_id' => $country->id
        ];

        $this
            ->actingAs($this->customer, 'web')
            ->post(route('customer.address.store', $this->customer->id), $data)
            ->assertStatus(302)
            ->assertRedirect(route('checkout.index'));
    }

    /** @test */
    public function it_can_show_the_create_address()
    {
        factory(City::class)->create();

        $this
            ->actingAs($this->customer, 'web')
            ->get(route('customer.address.create', $this->customer->id))
            ->assertStatus(200)
            ->assertSee('Alias')
            ->assertSee('Address 1')
            ->assertSee('Address 2')
            ->assertSee('Country')
            ->assertSee('Province')
            ->assertSee('City')
            ->assertSee('Zip Code');
    }
}
