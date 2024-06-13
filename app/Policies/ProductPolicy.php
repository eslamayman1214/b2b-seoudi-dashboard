<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Product $product)
    {
        return true;
    }

    public function upload(User $user)
    {
        return $user->role === 'admin' || $user->role === 'super admin';
    }

    public function update(User $user)
    {
        return $user->role === 'super admin';
    }
}