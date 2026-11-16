<?php

namespace App\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;

class RoleMenuFilter implements FilterInterface
{
    public function transform($item)
    {
        if (isset($item['role']) && (! Auth::check() || Auth::user()->role !== $item['role'])) {
            $item['restricted'] = true;
        }

        return $item;
    }
}