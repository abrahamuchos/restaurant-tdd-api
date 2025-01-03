<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Menu\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * @param Menu $menu
     *
     * @return MenuResource
     */
    public function show(Menu $menu): MenuResource
    {
        return new MenuResource($menu);
    }



}
