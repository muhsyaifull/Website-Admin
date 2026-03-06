<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * Test user is admin method
     */
    public function test_user_is_admin(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        $this->assertTrue($user->isAdmin());
    }

    /**
     * Test user is not admin method
     */
    public function test_user_is_not_admin(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $this->assertFalse($user->isAdmin());
    }

    /**
     * Test user is active method
     */
    public function test_user_is_active(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'active',
        ]);

        $this->assertTrue($user->isActive());
    }

    /**
     * Test user is not active method
     */
    public function test_user_is_not_active(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'inactive',
        ]);

        $this->assertFalse($user->isActive());
    }
}