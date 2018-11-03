<?php
session_start();

function authorize($authparams)
{
  $i=0;
  $username = '';
  $password = '';
  $opassword = '';
  $npassword = '';
  //echo $authparams;
  $params = explode('&',$authparams);
  foreach($params as $p)
  {
	  if($i==0)
		$username = explode('=',$p)[1];
	  if($i==1)
		$password = explode('=',$p)[1];
	  if($i==2)
		$opassword = explode('=',$p)[1];
	  if($i==3)
		$npassword = explode('=',$p)[1];
	  $i++;
  }
  
  //normally this info would be pulled from a database.
  //build JSON array
  $users = file_get_contents('users.json');
  $users  = json_decode($users);

  if(array_key_exists($username,$users))
  {
	  /*echo "User exist";
	  echo $password;
	  echo $users->$username;*/
	  if($password  == $users->$username)
	  {
		  if(strlen($npassword)>0)
		  {
			if($opassword != $password)
				$status = array("status" => "failure"); 
			else
			{
		      $status = array("status" => "success"); 
		      $encstr = base64_encode($username.'&'.$password);
		      $_SESSION['token'] = $encstr; 
			  $users->$username = $npassword;
			  $jsonusers = json_encode($users);
			  file_put_contents('users.json',$jsonusers);
			}
		  }
          else
          {
			  $status = array("status" => "success"); 
		      $encstr = base64_encode($username.'&'.$password);
		      $_SESSION['token'] = $encstr;
		  }
	  }
	  else
		  $status = array("status" => "failure"); 
  }
  else
	  $status = array("status" => "failure"); 
 
  
  return $status;
}

$possible_params = array("authorization", "test");
$value = "An error has occurred";
if (isset($_POST["action"]) && in_array($_POST["action"], $possible_params))
{
	switch ($_POST["action"])
	{
		case "authorization":
		$value = authorize($_POST['formData']);
		break;
	}
}
exit(json_encode($value));

//return JSON array

?>