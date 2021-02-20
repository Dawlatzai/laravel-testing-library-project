<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function a_book_can_be_added_to_library()
    {
        $response = $this->post('/books', $this->data());
        $book = Book::first();
        $this->assertCount(1, Book::all());
        $response->assertRedirect($book->path());
    }

    /** @test */
    public function a_title_is_required()
    {
        $response = $this->post('/books', array_merge(['title' => '', $this->data()]));
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function author_field_is_required()
    {
        $response = $this->post('/books', array_merge([$this->data(), 'author_id' => '']));
        $response->assertSessionHasErrors('author_id');
    }

    /** @test */
    public function a_book_can_be_updated()
    {
        $this->post('/books', $this->data());
        $book = Book::first();

        $response = $this->patch('/books/'.$book->id, [
            'title' => 'New Title', 
            'author_id' => 2
        ]);

        $this->assertEquals('New Title', Book::first()->title);

        $response->assertRedirect($book->path());
    }

    /**
     *  @test 
     */

    public function a_book_can_be_deleted()
    {
        $this->post('/books', $this->data());
        $book = Book::first();

        $response = $this->delete('/books/'.$book->id);

        $this->assertCount(0, Book::all());

        $response->assertRedirect('/books');
    }

    /**
     * @test
     */
    public function a_new_author_is_automatically_added()
    {
        $this->withoutExceptionHandling();

        $this->post('/books', [
            'title' => 'Test is green, over Green', 
            'author_id' => 'Doglas'
        ]);

        $book = Book::first();
        $author = Author::first();
        
        $this->assertEquals($author->id, $book->author_id);
        $this->assertCount(1, Author::all());
    }

    protected function data()
    {
        return [
            'title' => 'Test', 
            'author_id' => 'Doglas'
        ];
    }
}
