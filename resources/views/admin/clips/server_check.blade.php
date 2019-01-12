@extends('layouts.app')

@section('page-styles')
    {{-- <link rel="stylesheet" href="{{ asset('editor/public/lib/bootstrap/dist/css/bootstrap.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('editor/public/lib/jquery-ui/themes/smoothness/jquery-ui.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('editor/public/assets/css/icomoon/style.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('editor/public/assets/css/styles.css') }}">

    <script type="text/javascript">
    function expandCollapse(id) {
        if (document.getElementById(id).style.display == 'none') {
            document.getElementById(id).style.display = 'block';
        } else {
            document.getElementById(id).style.display = 'none';
        }
    }
    </script>

@stop

@section('content')



    <div class="card card-default">
        <div class="card-block">

@php
$is_windows = strpos(php_uname(), "Windows") !== false;
$ffmpeg_path = !empty($_POST['ffmpeg_path']) && strpos($_POST['ffmpeg_path'], 'ffmpeg') !== false
    ? trim($_POST['ffmpeg_path'])
    : '';
if (!$ffmpeg_path && !$is_windows) {
    $ffmpeg_path = trim(shell_exec('which ffmpeg'));
}

function getCodecs($ffmpeg_path = '')
{

    $lines = array();
    $encoders = array();
    exec("{$ffmpeg_path} -codecs", $lines);

    foreach ($lines as $line) {

        if (preg_match('/^\s+([A-Z .]+)\s+(\w{2,})\s+(.*)$/', $line, $m)) {
            $type = trim($m[1]);
            if (strpos($type, 'E') !== false) {
                $encoder = trim($m[2]);
                if (strpos($encoder, ',') !== false) {
                    foreach (split(',', $encoder) as $e) {
                        $encoders[] = $e;
                    }
                } else {
                    $encoders[] = $encoder;
                }
            }
        }
    }
    sort($encoders);

    return $encoders;
}

function getPHPPath()
{

    $is_windows = strpos(strtolower(php_uname()), 'windows') !== false;

    if ($is_windows) {
        $output = dirname(ini_get('extension_dir')) . "/php.exe";
    } else {
        $output = trim(shell_exec("which php"));
    }

    return $output;
}

$info = array();

$info['php_version'] = array('name' => 'PHP version', 'value' => phpversion());
$info['php_path'] = array('name' => 'PHP path', 'value' => getPHPPath());
$info['web_server'] = array('name' => 'Web server', 'value' => $_SERVER['SERVER_SOFTWARE']);
$info['ffmpeg_path'] = array('name' => 'FFMPEG path', 'value' => $ffmpeg_path);
$info['ffprobe_path'] = array('name' => 'FFPROBE path', 'value' => dirname($ffmpeg_path) . DIRECTORY_SEPARATOR . 'ffprobe');

$info['ffmpeg_version'] = array('name' => 'FFMPEG version', 'value' => '');
if ($ffmpeg_path) {
    $ffmpeg_ver = shell_exec("{$ffmpeg_path} -version");
    preg_match('/.+version.+/', $ffmpeg_ver, $matches);
    if (!empty($matches)) {
        $info['ffmpeg_version']['value'] = $matches[0];
    }
    $ffprobe_chk = shell_exec("{$info['ffprobe_path']['value']} -version");
    if(!$ffprobe_chk || strpos($ffprobe_chk, 'ffprobe') === false){
        $info['ffprobe_path']['value'] = '';
    }
}
$info['yamdi_path'] = array('name' => 'Yamdi path', 'value' => !$is_windows ? trim(shell_exec('which yamdi')) : '');
$info['mp4box_path'] = array(
    'name' => 'MP4Box (GPAC) path',
    'value' => !$is_windows ? trim(shell_exec('which MP4Box')) : ''
);
$info['qtfaststart_path'] = array(
    'name' => 'qt-faststart path',
    'value' => !$is_windows ? trim(shell_exec('which qt-faststart')) : ''
);
$info['flvtool2_path'] = array(
    'name' => 'flvtool2 path',
    'value' => !$is_windows ? trim(shell_exec('which flvtool2')) : ''
);

$info['ffmpeg_codecs'] = array('name' => 'FFMPEG codecs', 'value' => array());
if ($ffmpeg_path) {
    $info['ffmpeg_codecs']['value'] = getCodecs($ffmpeg_path);
}

if (empty($info['ffmpeg_codecs']['value'])) {
    $info['ffmpeg_path']['value'] = '';
}

ksort($info);

@endphp




        <div>
            <form action="{{ route('admin.servercheck') }}" method="post">
                <label>
                    FFmpeg path:
                    <input type="text" name="ffmpeg_path" value="<?php echo $ffmpeg_path; ?>">
                </label>
                <button type="submit">Submit</button>
            </form>
            <br>
        </div>


<table cellpadding="5" cellspacing="0" border="1">
    <colgroup>
        <col width="30%"/>
        <col width="70%"/>
    </colgroup>
    <thead>
    <tr>
        <th>Property</th>
        <th>Value</th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($info as $key => $opt): ?>

        <tr>
            <td><?php echo $opt['name']; ?>:</td>
            <td>

                <?php if (!empty($opt['value'])): ?>

                    <?php
                    if (!is_array($opt['value'])):
                        echo $opt['value'];
                    else: ?>

                        <a href="#" style="display: inline-block; margin: 5px 0;"
                           onclick="expandCollapse('<?php echo $key; ?>');return false;">[Expand/Collapse]</a>
                        <div id="<?php echo $key; ?>" style="display: none;">

                            <?php foreach ($opt['value'] as $val): ?>

                                <div><?php echo $val; ?></div>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                <?php else: ?>
                    <span style="color:red;">[Not found]</span>
                <?php endif; ?>

            </td>
        </tr>

    <?php endforeach; ?>

    </tbody>
</table>








        </div>
    </div>

{{-- @include('editor.templates.default') --}}


@stop

@section('javascript')
    @parent

        <script src="{{ asset('editor/public/lib/tether/dist/js/tether.min.js') }}"></script>

        <script src="{{ asset('editor/public/lib/underscore/underscore-min.js') }}"></script>

        <script src="{{ asset('editor/public/assets/js/webvideoedit.js') }}"></script>
        {{-- <script src="{{ asset('editor/public/assets/js/app.min.js') }}"></script> --}}

    <script>
        var webVideoEditor = new WebVideoEditor({
            baseUrl: '/',
            requestHandler: '/'
        });
    </script>
@stop
