<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_visit_comment()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_comment_save()
    {
        $response = $this->postJson('/comments', ['name' => 'Migz', 'comment'=>'test']);

        $response->assertStatus(200)->assertJson(['status' => true]);
    }
}
