<?php

namespace App\Http\Controllers\Admin;

use App\Clip;
use App\Jobs\ConvertVideoForDownloading;
use App\Jobs\CreateFileInfo;
use App\Jobs\CreateFileInfoJob;
use App\Jobs\GeneratePngsJob;
use App\Jobs\MakeMP4;
use App\Jobs\ProcessConvertToCaiJob;
use App\Jobs\SendClipToRest;
use App\Jobs\ConvertVideoForStreaming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClipsRequest;
use App\Http\Requests\Admin\UpdateClipsRequest;
use App\Http\Controllers\Traits\FileUploadTrait;
use App\Helpers\Helpers;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Manipulations;
use Illuminate\Support\Facades\Log;
use Config;
use Pbmedia\LaravelFFMpeg\FFMpegFacade as FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\Point;
use FFMpeg\Media\Video;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Filters\Video\ExtractMultipleFramesFilter;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Filters\Video as Filters;

use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Format\Audio\DefaultAudio;
use FFMpeg\Format;
use FFMpeg\Media\MediaTypeInterface;
use FFMpeg\Format\ProgressListener\VideoProgressListener;

class ClipsController extends Controller
{
    use FileUploadTrait;

    /**
     * Display a listing of Clip.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('clip_access')) {
            return abort(401);
        }

        if (request('show_deleted') == 1) {
            if (! Gate::allows('clip_delete')) {
                return abort(401);
            }
            $clips = Clip::onlyTrashed()->get();
        } else {
            $clips = Clip::all();

        }

        // $images = [];


        return view('admin.clips.index', compact('clips'));
    }





    public function list()
    {
        if (! Gate::allows('clip_access')) {
            return abort(401);
        }

        if (request('show_deleted') == 1) {
            if (! Gate::allows('clip_delete')) {
                return abort(401);
            }
            $clips = Clip::onlyTrashed()->get();
        } else {
            $clips = Clip::all();

        }

        return view('admin.clips.list', compact('clips'));
    }


    /**
     * Show the form for creating new Clip.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('clip_create')) {
            return abort(401);
        }

        // flash('Welcome Aboard!')->overlay();
        // flash('Sorry! Please try again.')->error()->important();
        //return view('admin.clips.editor');
        return view('admin.clips.create');
    }

    /**
     * Store a newly created Clip in storage.
     *
     * @param \App\Http\Requests\Admin\StoreClipsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClipsRequest $request)
    {
        if (! Gate::allows('clip_create')) {
            return abort(401);
        }

        // if(getenv('CUSTOMDEBUG') === 'ON') {
        //     dd(getenv('CUSTOMDEBUG'));
        // }

        try{
            $request = $this->saveFiles($request);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }





        $clip = Clip::create($request->all());

        // Event::fire('clip.change');


        foreach ($request->input('videos', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $clip->id;
            $file->save();

            // MakeMP4::dispatch($clip);
          // GeneratePngsJob::dispatch($clip);

            // ConvertUploadToRightFormatJob::dispatch($clip);
           // CreateFileInfoJob::dispatch($clip);
        }

        foreach ($request->input('images_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $clip->id;
            $file->save();
        }


        return redirect()->route('admin.clips.index');
    }



    /**
     * Show the form for editing Clip.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('clip_edit')) {
            return abort(401);
        }
        $clip = Clip::findOrFail($id);

        return view('admin.clips.edit', compact('clip'));
    }

    /**
     * Update Clip in storage.
     *
     * @param \App\Http\Requests\Admin\UpdateClipsRequest $request
     * @param  int                                        $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClipsRequest $request, $id)
    {
        if (! Gate::allows('clip_edit')) {
            return abort(401);
        }
        $request = $this->saveFiles($request);
        $clip = Clip::findOrFail($id);
        $clip->update($request->all());


        $media = [];
        foreach ($request->input('videos_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $clip->id;
            $file->save();
            $media[] = $file->toArray();
        }
        $clip->updateMedia($media, 'videos');

        $media = [];

        foreach ($request->input('images_id', []) as $index => $id) {
            $model          = config('medialibrary.media_model');
            $file           = $model::find($id);
            $file->model_id = $clip->id;
            $file->save();
            $media[] = $file->toArray();
        }

        $clip->updateMedia($media, 'images');


        return redirect()->route('admin.clips.index');
    }


    /**
     * Display Clip.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('clip_view')) {
            return abort(401);
        }
        $clip = Clip::findOrFail($id);

        return view('admin.clips.show', compact('clip'));
    }


    /**
     * Remove Clip from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('clip_delete')) {
            return abort(401);
        }
        $clip = Clip::findOrFail($id);
        $clip->deletePreservingMedia();

        return redirect()->route('admin.clips.index');
    }

    /**
     * Delete all selected Clip at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('clip_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Clip::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->deletePreservingMedia();
            }
        }
    }


    /**
     * Restore Clip from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('clip_delete')) {
            return abort(401);
        }

        $clip = Clip::onlyTrashed()->findOrFail($id);

        $clip->restore();

        return redirect()->route('admin.clips.index');
    }

    /**
     * Permanently delete Clip from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('clip_delete')) {
            return abort(401);
        }
        $clip = Clip::onlyTrashed()->findOrFail($id);
        $clip->forceDelete();

        return redirect()->route('admin.clips.index');
    }


  public function servercheck()
    {
        // if (! Gate::allows('clip_create')) {
        //     return abort(401);
        // }

        return view('admin.clips.server_check');
    }

}
