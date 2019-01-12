<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Clip;

class BC extends Controller
{
    public $clip;

    /**
     * BC constructor.
     */
    public function __construct(Clip $clips)
    {
        $this->clip = $clips;
    }

    public function index()
    {

        return Clip::all();
    }

    public function store()
    {
        Clip::create([
            'title' => 'THE WANKER HOUR',
            'percentage' => '95'
        ]);
    }
}
