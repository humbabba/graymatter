<?php

namespace App\Http\Controllers;

use App\Logger;
use Illuminate\Http\Request;

class LoggerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $output = Logger::getSearchedLoggers($request);

        return view('loggers.index')->with('output', $output)->with('error', $output->error);
    }

}
