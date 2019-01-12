<?php

$target_path = "/var/www/uploads/";

$max_size = 74411;

$extensions = array('.cai');

$api_licenses = [
    "MOVIML0itMgjzJW3VKScrhMvnYWWjKhiuj4Bsmdk",
    "SOTVML5zbP7wxDHDH5QHCyDlM3KqfnJQHqwkjlsb",
    "WILDML5tfY4goi9rJAdZzqCSQ5XnrBYWVcHmTLty",
    "8kjh3ioo4yt89df9347yh5k4jk54j6nmvbcfhcff",
    "lkgj323l5khet9dfjjn4mk3qnhhpa9gusfd4n3ig",
    "solj323l5khet9dfjjn4mk3qnhhpa9gusfd4n313",
    "CYCLML1fvpJyYDbbdtp75a99SHwCI/9P6d1oic9T",
    "NXMSML1iO0UdnHIoaGkewK3rfwKvLdfio2uwAUTf",
    "INTEML11EKC3Pq3U6fy4TGNCo9PbwD0lr6POvtax"
];

// analyze POST
$file = basename($_FILES['uploadfile']['name']);
$tmpfileandpath = $_FILES['uploadfile']['tmp_name'];
$size = filesize($_FILES['uploadfile']['tmp_name']);
$extension = strrchr($_FILES['uploadfile']['name'], '.');
$apilic = isset($_POST["APILIC"]) ? $_POST["APILIC"] : "";

$time = date('YmdHms');


// security checks
if ($size > $max_size) {
    //$error = 'expecting max. 62 sec cai-file *.cai with samplerate 40Hz, precision 5';
    $error = 'invalid file length';

}
if (!in_array($extension, $extensions))  // unknown extension
{
    //$error = 'Only file types accepted: cai';
    $error = 'invalid file';
}
$i = count($api_licenses);
while ($i > 0) {
    $i--;
    if (!(strncmp($apilic, $api_licenses[$i], 40)))
        $i = -100;
}
if ($i != -100) //API license invalid
{
    $error = 'invalid API license' . $apilic;
}

if (!isset($error)) //no error on upload
{
    $error = '';

    // read, increment, update nr_uploads
    $fh = fopen("/var/www/nr_server_uploads_log", 'r') or die("can't open file");
    $read = fscanf($fh, "%d\n");
    fclose($fh);
    $nr = $read[0];
    $nr++;
    $fh = fopen("/var/www/nr_server_uploads_log", 'w') or die("can't open file");
    fwrite($fh, strval($nr));
    fclose($fh);

    // make the upload file unique
    $file = $nr . "-" . $file;

    // cleanup and trim the upload file to the allowed
    $file = strtr($file,
        'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
        'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
    $file = preg_replace('/([^.a-z0-9]+)/i', '-', $file);

    $target_path = $target_path . $file;

    if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $target_path)) {

        // call the backend DedupServer
        $host = "127.0.0.1";
        $port = 82;  // default DedupPort

        $DedupServer_query_string = str_replace("\r\n", '', "CHECK: $time,$target_path,$size,$apilic,$tmpfileandpath") . "\n";
        // create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

        // connect to server
        $result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");

        // send string to server
        socket_write($socket, $DedupServer_query_string, strlen($DedupServer_query_string)) or die("Could not send data to server\n");

        // get server response
        $result = socket_read($socket, 1024) or die("Could not read server response\n");

        // close socket
        socket_close($socket);

        // clean up result
        $result = trim($result);
        $result = substr($result, 0, strlen($result));

        // check if we got No Match from the backend
        if (strncmp($result, "No Match", 8)) {

            $tag = $apilic . "," . $result;

        } else {
            $error = "No Match";
        }
    } else {
        $error = "invalid file";
    }
}


// send response
if (isset($tag)) //No error, or "No Match"
{
    // No error, send XML:

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n<response>" . $tag . "</response>\n";

    // log it
    $log_string = str_replace("\r\n", '', "DEDUP: $time,$nr,$file,$size,$apilic,$tag") . "\n";
    error_log($log_string, 3, "/var/www/logs/dedup.log");
} else {
    // Error send the reason
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n<response>\n" . "\t<error>" . $error . "</error>\n" . "</response>\n";

    // log it
    $log_string = str_replace("\r\n", '', "DEDUP: $time,$nr,$file,$size,$apilic,ERROR,$error") . "\n";
    error_log($log_string, 3, "/var/www/logs/caipy.log");
}
