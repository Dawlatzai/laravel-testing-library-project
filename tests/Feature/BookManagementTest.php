<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function a_book_can_be_added_to_library()
    {

        $response = $this->post('/books', [
            'title' => 'Ready Thing This Week', 
            'author' => 'David'
        ]);
        
        $book = Book::first();

        $this->assertCount(1, Book::all());

        $response->assertRedirect($book->path());
    }

    /** @test */
    public function a_title_is_required()
    {

        $response = $this->post('/books', [
            'title' => '', 
            'author' => 'David'
        ]);

        $response->assertSessionHasErrors('title');

    }

    /** @test */
    public function author_field_is_required()
    {

        $response = $this->post('/books', [
            'title' => 'Test', 
            'author' => ''
        ]);

        $response->assertSessionHasErrors('author');
    }

    /** @test */
    public function a_book_can_be_updated()
    {

        $this->post('/books', [
            'title' => 'Test', 
            'author' => 'Doglas'
        ]);

        $book = Book::first();

        $response = $this->patch('/books/'.$book->id, [
            'title' => 'New Title', 
            'author' => 'New Author'
        ]);

        $this->assertEquals('New Title', Book::first()->title);
        $this->assertEquals('New Author', Book::first()->author);

        $response->assertRedirect($book->path());
    }

    /**
     *  @test 
     */

    public function a_book_can_be_deleted()
    {

        $this->post('/books', [
            'title' => 'Test', 
            'author' => 'Doglas'
        ]);

        $book = Book::first();

        $response = $this->delete('/books/'.$book->id);

        $this->assertCount(0, Book::all());

        $response->assertRedirect('/books');
    }
}
