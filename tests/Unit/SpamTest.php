<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpamTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_checks_for_invalid_keywords()
    {
        $spam = new Spam();
        $this->assertFalse($spam->detect('Innocent reply here.'));

        $this->expectException(\Exception::class);
        $spam->detect('Yahoo customer support');
    }

    /** @test */
    function it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();
        $this->expectException(\Exception::class);
        $spam->detect('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
    }
}
