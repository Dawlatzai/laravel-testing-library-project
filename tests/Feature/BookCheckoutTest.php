<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookCheckoutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function a_book_can_be_checkout_with_sign_in_user()
    {
        $this->withoutExceptionHandling();
        
        $book = Book::factory()->create();

        $this->actingAs($user = User::factory()->create())
            ->post('/checkout/'. $book->id);

        $this->assertCount(1, Reservation::all());

        $this->assertEquals($user->id, Reservation::first()->user_id);

        $this->assertEquals($book->id, Reservation::first()->book_id);

        $this->assertEquals(now(), Reservation::first()->checked_out_at);
    }

    /**
     * @test
     */
    public  function only_signed_in_user_can_checkout_a_book()
    {
        $this->withoutExceptionHandling();

        $book = Book::factory()->create();

        $this->post('/checkout/'. $book->id)
            ->assertRedirect('/login');

        $this->assertCount(0, Reservation::all());

    }
}
