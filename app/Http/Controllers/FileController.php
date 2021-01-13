<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function writeFile(Request $request,$directory)
    {

        $file = $request->file('file');
        $request->validate([
            'file'  => 'mimes:jpg,jpeg,gif,png|max:2048',
        ]);
        $filename = $this->getFileName($file,$directory);
        $path = $file->storeAs(
            'media/' . $directory, $filename
        );
        return url($path);
    }

    public function getFileName($file,$directory)
    {
        $name = $file->getClientOriginalName();
        $extension = '.' . $file->getClientOriginalExtension();
        $trimName = trim(strtolower(str_replace($extension,'',str_replace(' ','-',$name))));
        $name = $trimName . $extension;
        $exists = Storage::disk('local')->exists('media/' . $directory . '/' . $name);
        $x = 1;
        while($exists && $x < 1000) {
            $name = $trimName . '-' . $x . $extension;
            $x++;
            $exists = Storage::disk('local')->exists('media/' . $directory . '/' . $name);
        }
        return $name;
    }
}
