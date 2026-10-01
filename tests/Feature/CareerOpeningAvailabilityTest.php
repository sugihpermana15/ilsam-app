<?php

namespace Tests\Feature;

use App\Models\CareerOpening;
use Tests\TestCase;

class CareerOpeningAvailabilityTest extends TestCase
{
    public function test_opening_is_past_its_deadline_only_after_deadline_date(): void
    {
        $available = new CareerOpening([
            'title' => 'Available opening',
            'is_active' => true,
            'deadline' => today(),
        ]);

        $expired = new CareerOpening([
            'title' => 'Expired opening',
            'is_active' => true,
            'deadline' => today()->subDay(),
        ]);

        $this->assertFalse($available->isPastDeadline());
        $this->assertTrue($expired->isPastDeadline());
    }
}