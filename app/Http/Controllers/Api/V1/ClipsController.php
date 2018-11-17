<?php

namespace App\Http\Controllers\Api\V1;

use App\Clip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClipsRequest;
use App\Http\Requests\Admin\UpdateClipsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;

class ClipsController extends Controller
{
    use FileUploadTrait;

    public function index()
    {
        return Clip::all();
    }

    public function show($id)
    {
        return Clip::findOrFail($id);
    }

    public function update(UpdateClipsRequest $request, $id)
    {
        $request = $this->saveFiles($request);
        $clip = Clip::findOrFail($id);
        $clip->update($request->all());
        

        return $clip;
    }

    public function store(StoreClipsRequest $request)
    {
        $request = $this->saveFiles($request);
        $clip = Clip::create($request->all());
        

        return $clip;
    }

    public function destroy($id)
    {
        $clip = Clip::findOrFail($id);
        $clip->delete();
        return '';
    }
}
