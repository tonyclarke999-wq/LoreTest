<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Bug Update Action Page
#
# $RCSfile: results_remote_execution_action.php,v $  
# $Revision    $
# ------------------------------------

include"./api/include_api.php";
auth_authenticate_user();
$err_message = "";
$port=6551;

if ($_POST['command'] != "FILE")
{

	$machineList = Array();

		foreach($_POST['machineList'] as $machineName)
		{
			$machineName = gethostbyname($machineName);
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

			if (!$socket)
			{
			   $err_message='socket%20create%20failed';
			}
			else
			{
				$result = socket_connect($socket, $machineName, $port);
			  	if (!$result)
			  	{
					 $err_message='socket%20connect%20failed';
				}
				else
				{
					if($_POST['command']  == "START")
					{
						sendData($socket,"PREPARE");
						$ret = receiveData($socket);
					}

					if($_POST['command'] == "CHANGE" || $_POST['command'] == "UPDATE")
					{
						if($_POST['project'] != "NOCHANGE")
						{
							$sendString="$_POST[command],$_POST[project]";
							sendData($socket,$sendString);
							$ret = receiveData($socket);
							sendData($socket, "END");
							$ret = receiveData($socket);
						}
					}
					else
					{
						sendData($socket,$_POST['command']);
						$ret = receiveData($socket);
						sendData($socket, "END");
						$ret = receiveData($socket);
					}
				}
			}
		}

}
else
{
	$lines = @file($_FILES['f'][tmp_name]);

	foreach ($lines as $line_num => $line)
	{
	#print "$line<br>";
		if (preg_match('/^Yes/', $line))
		{

			$properties = split(",",$line);

			$name = $properties[2];
			$path = $properties[3];
			$params = $properties[4];
			$type = $properties[6];
			$machine = $properties[7];
			$priority = $properties[5];
			$tempestID = $properties[1];

			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

			if (!$socket)
			{
			   $err_message='socket%20create%20failed';
			}
			else
			{
			  $result = @socket_connect($socket, $machine, $port);
			  if (!$result)
			  {
				 $err_message='socket%20connect%20failed';
			  }
			  else
			  {
				$data="SEND,".$name.",".$path.",".$params.",".$tempestID.",".$priority.",".$type;
				sendData($socket,$data);
				$ret = receiveData($socket);
				sendData($socket, "END");
				$ret = receiveData($socket);
			  }
			}
		}
	}

}

if($err_message == "")
{
	header('Location: index.php');
}
else
{
	header("Location: error.php?err=$err_message");
}

function sendData($socket, $d)
{
	socket_write($socket, $d . "\n", strlen($d)+1);
}

function receiveData($socket)
{
	return socket_read($socket, 2048);
}

# ------------------------------------------------------------------
# $Log: results_remote_execution_action.php,v $
# Revision 1.1  2006/05/03 20:23:13  gth2
# no message
#
# ------------------------------------------------------------------
?>