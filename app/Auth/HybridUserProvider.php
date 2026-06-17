<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class HybridUserProvider extends EloquentUserProvider
{
    // Mock users for offline fallback
    protected static $mockUsers = [
        1 => [
            'id' => 1,
            'name' => 'Vecino Juan',
            'email' => 'vecino@example.com',
            'role' => 'vecino',
            'password' => 'vecino123',
        ],
        2 => [
            'id' => 2,
            'name' => 'Inspector Carlos',
            'email' => 'inspector@example.com',
            'role' => 'inspector',
            'password' => 'inspector123',
        ],
        3 => [
            'id' => 3,
            'name' => 'Admin Soporte IT',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => 'admin123',
        ],
    ];

    /**
     * Retrieve a user by their unique identifier.
     */
    public function retrieveById($identifier)
    {
        try {
            // Try Eloquent first
            return parent::retrieveById($identifier);
        } catch (\Exception $e) {
            // Fallback if DB is offline
            return $this->getMockUserById($identifier);
        }
    }

    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return null;
        }

        try {
            return parent::retrieveByCredentials($credentials);
        } catch (\Exception $e) {
            // Fallback if DB is offline
            $email = $credentials['email'] ?? null;
            if ($email) {
                foreach (self::$mockUsers as $id => $data) {
                    if (strtolower($data['email']) === strtolower($email)) {
                        return $this->getMockUserById($id);
                    }
                }
            }
            return null;
        }
    }

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Try parent validation first
        try {
            if (parent::validateCredentials($user, $credentials)) {
                return true;
            }
        } catch (\Exception $e) {
            // Continue to fallback
        }

        // Fallback checks for mock users / offline
        $plainPassword = $credentials['password'];
        
        // Find if this is a mock user email
        foreach (self::$mockUsers as $mock) {
            if (strtolower($mock['email']) === strtolower($user->email)) {
                return $plainPassword === $mock['password'];
            }
        }

        return false;
    }

    /**
     * Helper to instantiate a mock User model without hitting DB.
     */
    protected function getMockUserById($id)
    {
        $mock = self::$mockUsers[$id] ?? null;
        if (!$mock) {
            return null;
        }

        $user = new User();
        $user->id = $mock['id'];
        $user->name = $mock['name'];
        $user->email = $mock['email'];
        $user->role = $mock['role'];
        $user->password = Hash::make($mock['password']);
        $user->exists = true; // Tell Eloquent it exists

        return $user;
    }
}
