<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait Slugify
{
    public function to_slug(string $title) : string
    {
        $slug = Str::slug($title);
        $id = uniqid(rand(), true);

        return $slug.'-'.$id;
    }
}
