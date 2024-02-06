<?php

namespace maurienejunior\lazyapi;
use Illuminate\Support\Facades\Storage;

class LazyApiFile
{

    public static function storage($file, $name)
    {
        $file->storeAs('/', $name);
    }

    public static function delete($path)
    {
        Storage::delete($path);
    }
}
