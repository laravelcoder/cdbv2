<?php
$ver = (float)phpversion();
echo "PHPVERSION : ". $ver . " <br/>\n";;
echo "==============================================================================   <br/>\n";
echo "|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX|   <br/>\n";
echo "==============================================================================   <br/>\n";
echo "               <STRONG>CURRENT LOADED PHP INFO  PHP V: ". $ver ."</STRONG>   <br/>\n";
echo "==============================================================================   <br/>\n";
echo "|XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX|   <br/>\n";
echo "==============================================================================   <br/>\n";
echo "<br/>\n";
if (version_compare(phpversion(), '7.0.0', '<')) {
    echo 'Current PHP version: ' . phpversion() . " <br/><br/>\n";
    // echo "<strong>PHP VERSION ISN'T HIGH ENOUGH</strong> <br/><br/>";
}
else {
    echo 'Current PHP version: ' . phpversion() . " <br/><br/>\n";
    // echo "<strong>PHP VERSION IS HIGH ENOUGH</strong> <br/><br/>";
}
echo "==============================================================================   <br/>\n";
echo "<br/>\n";
echo '<strong>DISPLAY_ERRORS</strong> = ' . ini_get('display_errors') . "<br/>\n";
echo '<strong>MEMORY_LIMIT</strong> = ' . ini_get('memory_limit') . "<br/>\n";
echo '<strong>POST_MAX_SIZE</strong> = ' . ini_get('post_max_size') . "<br/>\n";
echo '<strong>MAX_EXECUTION_TIME</strong> = ' . ini_get('max_execution_time') . "<br/>\n";
echo '<strong>POST_MAX_SIZE</strong> = ' . ini_get('post_max_size') . "<br/>\n";
echo '<strong>UPLOAD_MAX_FILESIZE</strong> = ' . ini_get('upload_max_filesize') . "<br/>\n";
echo '<strong>MAX_FILE_UPLOADS</strong> = ' . ini_get('max_file_uploads') . "<br/>\n";
echo "<br/>\n";
echo "==============================================================================   <br/>\n";



foreach (get_loaded_extensions() as $i => $ext)
{
   echo '<strong>' .$ext .'</strong> => '. phpversion($ext). '<br/>';
}
echo "==============================================================================   <br/>\n";


// if ($ver > 7.0) {
//  echo $ver;
// echo "       DO SOMETHING FOR PHP7.1 AND ABOVE.\n";
// } elseif ($ver === 7.0) {
//  echo $ver;
// echo "       DO SOMETHING FOR PHP 7.0\n";
// } else {
//  echo $ver;
// echo "\n       DO SOMETHING FOR PHP 5.6 OR LOWER.\n";
// }
echo "==============================================================================   <br/>\n";

