<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function writeFile(Request $request)
    {
        return $request->file('file')->store('public/images');
    }
}
