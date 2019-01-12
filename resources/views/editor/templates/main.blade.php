<?php

//use App\Controller\BaseControllerClass as BaseController;

/** @var array $config */
/** @var array $user */
/** @var array $lang */

//$userOptions = array(
//    'files_size_total' => BaseController::sizeFormat( $user['files_size_total'] ),
//    'files_size_max' => BaseController::sizeFormat( $user['files_size_max'] ),
//    'files_size_percent' => floor( $user['files_size_total'] / $user['files_size_max'] * 100 ),
//    'show_log' => !empty( $config['users_restrictions'][ $user['role'] ] )
//        && isset( $config['users_restrictions'][ $user['role'] ]['show_log'] )
//            ? $config['users_restrictions'][ $user['role'] ]['show_log']
//            : true
//);

?>
<div class="row">
    <div class="col-md-2 push-md-10 text-right">

        <div class="dropdown display-inline-block">
          {{--   <button class="btn btn-lg btn-outline-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="icon-menu"></span>
            </button> --}}
      {{--       <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#" data-toggle="action" data-action="profile">
                    <span class="icon-user-tie"></span>

                </a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="?action=users">
                        <span class="icon-users"></span>

                    </a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="action" data-action="log">
                        <span class="icon-file-text"></span>

                    </a>


                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="index.php?action=logout">
                        <span class="icon-exit"></span>

                    </a>

            </div> --}}
        </div>

    </div>
    <div class="col-md-6 pull-md-2 text-sm-left text-center">
        <h2 class="logo">
            <img src="{{ asset('editor/public/assets/img/logo.png') }}" alt="">
        </h2>
    </div>
    <div class="col-md-4 pull-md-2">
        <div id="wve-user-stat">
            <div class="progress mt-3">
         {{--        <div class="progress-bar  bg-danger bg-success" role="progressbar" style="width:  %" aria-valuenow=" " aria-valuemin="0" aria-valuemax="100"></div> --}}
            </div>
            <div class="text-center small mb-3">

            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-md-4 push-md-8">

        <div class="form-group">
            <button type="button" class="btn btn-lg btn-smp btn-outline-primary btn-block" data-toggle="action" data-action="import">
                <span class="icon-download"></span>

            </button>
        </div>

        <div class="mb-3" style="max-height: 338px; overflow: auto;">
            <ul class="list-group" id="wve-list_input">

            </ul>
        </div>

    </div>
    <div class="col-md-8 pull-md-4">

        <div class="card mb-3">
            <div class="card-block">

                <div class="wve-editor-player">
                    <video src="" width="432" height="320" id="wve-video"></video>
                    <div class="wve-editor-player-panel" style="display: none;">
                        <div class="time" id="wve-editor-player-time"></div>
                        <div class="time time-current" id="wve-editor-player-time-current"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<div class="clearfix"></div>

<!-- Timeline slider -->
<div class="card mb-3">
    <div class="card-block">

        <div class="editor-timeline-wrapper">
            <div id="wve-timeline"></div>
            <div id="wve-timeline-range"></div>
        </div>

        <div class="clearfix"></div>
        <hr>

        <!-- buttons -->
        <div class="row">
            <div class="col-lg-6">
                <div class="btn-group btn-group-justified btn-group-lg my-2" role="group">
                    <button type="button" class="btn btn-outline-primary toggle-tooltip" data-toggle="action" data-action="stepback_main" title=" ">
                        <span class="icon-arrow-left2"></span>
                    </button>
                    <button type="button" class="btn btn-outline-primary toggle-tooltip" data-toggle="action" data-action="play_main" title=" ">
                        <span class="icon-play3"></span>
                    </button>
                    <button type="button" class="btn btn-outline-primary toggle-tooltip" data-toggle="action" data-action="stepforward_main" title=" ">
                        <span class="icon-arrow-right2"></span>
                    </button>
                    <button type="button" class="btn btn-outline-primary toggle-tooltip" data-toggle="action" data-action="play_selected" title="">
                        <span class="icon-play2"></span>
                    </button>
                </div>
            </div>

            <div class="clearfix hidden-md-up"></div>

            <div class="col-lg-2 col-sm-6">

                <div class="my-2">
                    <div class="btn-group btn-group-justified btn-group-lg margin-bottom-md" role="group">
                        <button class="btn btn-outline-primary toggle-tooltip" data-toggle="action" data-action="take-episode" title=" ">
                            <span class="icon-plus"></span>
                        </button>
                        <button class="btn btn-outline-primary toggle-tooltip" data-toggle="action" data-action="cut-fast" title=" ">
                            <span class="icon-scissors"></span>
                        </button>
                    </div>
                </div>

            </div>

            <div class="col-lg-4 col-sm-6">

                <div class="my-2">
                    <button type="button" class="btn btn-block btn-lg btn-smp btn-outline-primary" data-toggle="action" data-action="render">
                        <span class="icon-checkmark"></span>

                    </button>
                </div>

            </div>

        </div>
        <!-- /buttons -->

        <!-- episode-container -->
        <div class="episode-container" id="wve-episode-container" style="display: none;">
            <hr class="mb-0">
            <div class="row wve-episode-container" id="wve-episode-container-inner"></div>
            <div class="clearfix"></div>
        </div>
        <!-- /episode-container -->

    </div>
</div>
<!-- /Timeline slider -->

<!-- Output list -->
<div class="card">
    <div class="card-block">

        <div class="bottom-list-container">

            <table class="table table-bordered table-hover no-margin">
                <colgroup>
                    <col width="40%">
                    <col width="20%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                </colgroup>
                <tbody id="wve-list_output"></tbody>
            </table>

        </div>

    </div>
</div>
<!-- /Output list -->
