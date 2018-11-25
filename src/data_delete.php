<?php 
require_once("common.php");
require_once("session.php");
if($app->admin != 1)
{
	$app->response->error[] = "Unauthorized Access";
	SendResponse();
}
$params = ParseParams();
if(!isset($params['path']))
{
	$app->response->error[] = "Path variable not set";
	SendResponse();
}
unlink ($params['path']);
if(dir_is_empty(dirname($params['path'])))
	rmdir(dirname($params['path']));

SendResponse();

function dir_is_empty($dir) {
  $handle = opendir($dir);
  while (false !== ($entry = readdir($handle))) {
    if ($entry != "." && $entry != "..") {
      closedir($handle);
      return FALSE;
    }
  }
  closedir($handle);
  return TRUE;
}