<?php

namespace App\Admin\Extensions\Nav;


class Links
{
    public function __toString(){
        $user = \Admin::user();
        return view('admin.nav.links')->render();
    }
}