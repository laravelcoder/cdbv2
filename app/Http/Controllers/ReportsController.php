<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendClipToRest;

class ReportsController extends Controller
{
    public function index()
    {


        $this->dispatch(new SendClipToRest());

        return 'done';
    }
}
