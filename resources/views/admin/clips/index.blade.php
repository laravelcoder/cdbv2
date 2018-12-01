@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
<div class="container-fluid pb-video-container">
    <div class="col-md-12">
    <h3 class="page-title">@lang('global.clips.title')</h3>
    @can('clip_create')
    <p>
        <a href="{{ route('admin.clips.create') }}" class="btn btn-success">@lang('global.app_add_new')</a>

    </p>
    @endcan


{{--     <div class="row">
        @if (count($clips) > 0)
            @foreach ($clips as $clip)
                @foreach($clip->getMedia('videos') as $media)
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        @can('clip_edit')


                        <h4 class="text-center">
                            <a href="{{ route('admin.clips.edit',[$clip->id]) }}" class="">{{ $media->name }}</a>
                        </h4>
                        <hr>
                        @endcan

                        <div id="video-container-{{ $media->id }}" class="embed-responsive embed-responsive-16by9">
                            <video id="video{{ $media->id }}" controls poster="{{ $clip->getFirstMediaUrl('screenshot-5sec') }}">
                                <source src="{{ $media->getUrl() }}" type="video/mp4">
                            </video>
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endif


    </div> --}}


    <div class="row">
        @if (count($clips) > 0)
            @foreach ($clips as $clip)
                @foreach($clip->getMedia('videos') as $media)
                    <div class="col-lg-4 col-md-6 col-sm-12">



            <div class="box box-solid">
                <div class="box-header with-border">
                  <h4 class="box-title" style="width:100%;">
                          <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $media->id }}">
                             {{ str_limit($media->name , $limit = 30, $end = '...') }}
                           </a>
                           @can('clip_edit') <a href="{{ route('admin.clips.edit',[$clip->id]) }}" class="btn btn-xs btn-info btn-flat btn-sm pull-right">@lang('global.app_edit')</a> @endcan
                  </h4>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  <div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <div class="panel box box-primary">
                      <div class="box-header with-border">




                        <div id="video-container-{{ $media->id }}" class="embed-responsive embed-responsive-16by9">
                            <video id="video{{ $media->id }}" controls poster="{{ $clip->getFirstMediaUrl('screenshot-5sec') }}">
                                <source src="{{ $media->getUrl() }}" type="video/mp4">
                            </video>
                        </div>
                      </div>
                      <div id="collapse{{ $media->id }}" class="panel-collapse collapse">
                        <div class="box-body ">
                            <div class="body">
                                <span>Collection Name: {{ $media->collection_name }}</span> <br>
                                <span>Mime Type: {{ $media->mime_type }}</span> <br>
                                <span>File Size: {{ $media->human_readable_size }}</span> <br>
                                <span>Filename: {{ $media->filename }}</span> <br>
                            </div>
                            <small>from upload</small>
                            <div class="d-flex flex-wrap">
                                @foreach($clip->getMedia('images')->slice(0, 3) as $media)
                                <span class="p-2 flex-fill bd-highlight">
                                    <a href="{{ $media->getUrl() }}" target="_blank">
                                        <img class=" " src="{{ $media->getUrl() }}"  title="SIZE {{ $media->size }} KB" width="80" height="35">
                                    </a>
                                </span>
                                @endforeach
                            </div>
@php

$mediaItems = $clip->getMedia('videos');
$mediaItems[0]->getUrl('screenshot-5sec');
$mediaItems[0]->getPath('screenshot-5sec');
// $mediaItems[0]->getTemporaryUrl(Carbon::now()->addMinutes(5), 'screenshot-5sec');

$urlToFirstListImage = $clip->getFirstMediaUrl('videos', 'screenshot-5sec');
// $urlToFirstTemporaryListImage = $clip->getFirstTemporaryUrl(Carbon::now()->addMinutes(5), 'images', 'screenshot-5sec');
$fullPathToFirstListImage = $clip->getFirstMediaPath('videos', 'screenshot-5sec');
// dd($urlToFirstListImage);
@endphp
                            <small>from video</small>
                            <div class="d-flex flex-wrap">
                                @foreach($clip->getMedia('videos')->slice(0, 3) as $screenshot)

                                <span class="p-2 flex-fill bd-highlight">
                                    <img src="{{ $urlToFirstListImage }}" alt="" width="80" height="35">

                                </span>
                                @endforeach
                            </div>

                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                <!-- /.box-body -->
              </div>


{{--
<div class="d-flex justify-content-start">...</div>
<div class="d-flex justify-content-end">...</div>
<div class="d-flex justify-content-center">...</div>
<div class="d-flex justify-content-between">...</div>
<div class="d-flex justify-content-around">...</div>
 --}}










                    </div>
                @endforeach
            @endforeach
        @endif


    </div>
</div>
@stop

@section('javascript')
    <script>
        @can('clip_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.clips.mass_destroy') }}'; @endif
        @endcan

        $('#modal1').on('hidden.bs.modal', function (e) {
          // do something...
          $('#modal1 iframe').attr("src", $("#modal1 iframe").attr("src"));
        });

        $('#modal6').on('hidden.bs.modal', function (e) {
          // do something...
          $('#modal6 iframe').attr("src", $("#modal6 iframe").attr("src"));
        });

        $('#modal4').on('hidden.bs.modal', function (e) {
          // do something...
          $('#modal4 iframe').attr("src", $("#modal4 iframe").attr("src"));
        });

    </script>
@endsection
