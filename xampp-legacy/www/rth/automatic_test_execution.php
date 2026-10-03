<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# $RCSfile: automatic_test_execution.php,v $ $Revision: 1.2 $
# ------------------------------------
error_reporting(E_ALL);

echo "<h2>TCP/IP Connection</h2>". NEWLINE;

// Port
$port = 3456;

// IP ADDRESS
$address = "136.184.233.159";

// Create a TCP/IP socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket < 0) {
   echo "socket_create() failed: reason: " . socket_strerror($socket) . "". NEWLINE;
} else {
   echo "OK.". NEWLINE;
}

echo "Attempting to connect to '$address' on port '$service_port'...";
$result = socket_connect($socket, $address, $service_port);
if ($result < 0) {
   echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "". NEWLINE;
} else {
   echo "OK.". NEWLINE;
}

$in = "You are a WABSACK";

echo "Sending HTTP HEAD request...";
socket_write($socket, $in, strlen($in));
echo "OK.". NEWLINE;

/*echo "Reading response:\n". NEWLINE;
while ($out = socket_read($socket, 2048)) {
   echo $out;
}
*/
echo "Closing socket...";
socket_close($socket);
echo "OK.\n". NEWLINE;

# ------------------------------------
# $Log: automatic_test_execution.php,v $
# Revision 1.2  2006/08/05 22:07:58  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:00:56  gth2
# importing initial version - gth
#
# ------------------------------------
?>