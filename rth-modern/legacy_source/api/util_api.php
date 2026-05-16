<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Utility API
#
# $RCSfile: util_api.php,v $ $Revision: 1.5 $
# ------------------------------------

#------------------------------------------------------------------------------------------
# Returns true if $var is whitespace string or empty string
#------------------------------------------------------------------------------------------
function util_is_blank( $var ) {

    if ( strlen( trim($var) ) == 0 ) {
        return true;
    }

    return false;
}

function util_pad_id( $id ) {

	$padded_id = sprintf("%05s",trim($id));

	return $padded_id;
}

function util_set_page_number( &$page_number, &$options ) {

    if( !empty($options['page_number']) ) {

    	if( $options['page_number']=='First' ) {

    		$page_number = $options['first_page_number'];
    	} elseif( $options['page_number']=='Previous' ) {

    		$page_number = $options['previous_page_number'];

    	} elseif( $options['page_number']=='Next' ) {

    		$page_number = $options['next_page_number'];

    	} elseif( $options['page_number']=='Last' ) {

    		$page_number = $options['last_page_number'];

    	} else {

    		$page_number = $options['page_number'];
    	}
    }
}

function util_set_filter( $filter_name, $filter, $options ) {

	# check that filter has been submitted
    if( !empty($options[$filter_name]) ) {

    	$filter = $options[$filter_name];
    }
}

function util_set_order_by( $order_by, $options ) {

	if( !empty($options['change_order_by']) ) {

		$new_order_by = $options['change_order_by'];

		$new_order_by_hidden = str_replace(" ", "_", $new_order_by);
		if( !empty($options[$new_order_by_hidden]) ) {
			$order_by = $options[$new_order_by_hidden];
		}
//print$new_order_by;exit;
	} else {
		if( !empty($options['order_by']) ) {
			$order_by = $options['order_by'];
		}
	}
}

#------------------------------------------------------------------------------------------
# Returns the opposite order by direction of $direction
#------------------------------------------------------------------------------------------
function util_set_order_dir( $order_dir, $options ) {

	if( !empty($options['change_order_by']) ) {

		switch( $options['order_dir'] ) {
			case( "ASC" ):
				$new_order_dir = "DESC";
				break;
			case( "DESC" ):
				$new_order_dir = "ASC";
				break;
		}

		$order_dir = $new_order_dir;
	} else {
		if( !empty($options['order_dir']) ) {
			$order_dir = $options['order_dir'];
		}
	}

}
#------------------------------------------------------------------------------------------
# Searches an array for a given value.
#
# INPUT:
#	Array
# OUTPUT:
#	True or False
#------------------------------------------------------------------------------------------
function util_array_value_search($search_value, $stack) {

	foreach($stack as $value) {

		if( $search_value == $value ) {
			return true;
		}
	}

	return false;
}

#------------------------------------------------------------------------------------------
# Searches an array for a given key.
#
# INPUT:
#	Array
# OUTPUT:
#	True or False
#------------------------------------------------------------------------------------------
function util_array_key_search($search_key, $stack) {

	foreach($stack as $key => $value) {

		if($search_key == $key) {
			return true;
		}
	}

	return false;
}

#-----------------------------------------------------------------------------
# Returns cleaned version of a _POST variable, or if variable does not exist,
# returns a default value.
#
# INPUT:
#	_POST variable name
#	default value for variable
# OUTPUT:
#	cleaned version of _POST variable or the default value
#-----------------------------------------------------------------------------
function util_clean_post_vars( $var, $default=null ) {

    $result = '';
    if( isset( $_POST[$var] ) && !empty($_POST[$var]) ) {

        $result = trim( $_POST[$var] );
        $result = stripslashes( $result );
		$result = str_replace( "'", "\'", "$result");
		$result = htmlentities($result, ENT_QUOTES);

    } elseif( func_num_args() > 1 ) {
        $result = $default;
    }

    return $result;
}

#-----------------------------------------------------------------------------
# Returns cleaned version of a _GET variable, or if variable does not exist,
# returns a default value.
#
# INPUT:
#	_GET variable name
#	default value for variable
# OUTPUT:
#	cleaned version of _GET variable or the default value
#-----------------------------------------------------------------------------
function util_clean_get_vars( $var, $default=null ) {

	$result = '';
	if( isset($_GET[$var]) && !empty($_GET[$var]) ) {

		$result = trim( $_GET[$var] );
		$result = stripslashes( $result );
	} elseif( !is_null($default) ) {

		$result = $default;
    }

	return $result;
}

#-----------------------------------------------------------------------------
# Returns cleaned version of a passed string, or if variable does not exist,
# returns a default value.
#
# INPUT:
#	string
#	default value of string
# OUTPUT:
#	string stripped of leading white spaces, trailing white spaces, slashes,
#	and replaces characters with a html entitity code
#	or the default value
#-----------------------------------------------------------------------------
function util_clean_var_html( $var, $default=null ) {
    $result = '';
    if( isset($var) && !empty($var) && is_string($var) ) {

        $result = trim($var);
        $result = stripslashes($result);
		$result = str_replace("'", "\'", "$result");
		$result = htmlentities($result, ENT_QUOTES);
    } elseif( !is_null($default) ) {

        $result = $default;
    }

    return $result;
}

#-----------------------------------------------------------------------------
# Returns cleaned version of a passed string, or if variable does not exist,
# returns a default value.
#
# INPUT:
#	string
#	default value of string
# OUTPUT:
#	string stripped of leading white spaces, trailing white spaces and slashes
#	or the default value
#-----------------------------------------------------------------------------
function util_clean_var_strip( $var, $default=null ) {

	$result = '';
	if( isset($var) && !empty($var) ) {

		$result = trim( $var );
		$result = stripslashes( $result );
	} elseif( !is_null($default) ) {

		$result = $default;
    }

	return $result;
}

#-----------------------------------------------------------------------------
# Retrieve a cookie variable
#  You may pass in any variable as a default (including null) but if
#  you pass in *no* default then an error will be triggered if the cookie
#  cannot be found
#-----------------------------------------------------------------------------
function util_get_cookie( $p_var_name, $p_default=null ) {
    $t_result = '';

    if ( isset( $_COOKIE[$p_var_name] ) ) {

    	$t_result = $_COOKIE[$p_var_name];
    #check for a default passed in (allowing null)
    } else if ( func_num_args() > 1 ) {

        $t_result = $p_default;
    }

    return $t_result;
}

#------------------------------------------------------------------------
# Set a cookie variable
# If $p_expire is false instead of a number, the cookie will expire when
#  the browser is closed; if it is true, the default time from the config
#  file will be used
# If $p_path or $p_domaain are omitted, defaults are used
#------------------------------------------------------------------------

function util_set_cookie( $p_name, $p_value, $p_path=null, $p_domain=null ) {

    return setcookie( $p_name, $p_value, time() + COOKIE_EXPIRE_LENGTH, COOKIE_PATH, COOKIE_DOMAIN );
}


# ----------------------------------------------------------------------
# Prepare text for exporting to csv file
# INPUT:
#   text to prepare
# OUTPUT:
#   text without embedded commas, tags and carriage return/linefeeds
# ----------------------------------------------------------------------
function util_prepare_text_for_export($text) {

    $text = str_replace(",", ":", $text);
    $text = strip_tags($text);
    $text = str_replace("\r", " ", $text);
    $text = str_replace("\n", " ", $text);

    return $text;
}


# ----------------------------------------------------------------------
# Get file type
# INPUT:
#   file name
# OUTPUT:
#   file extension
# ----------------------------------------------------------------------
function util_get_filetype($file_name) {
    $extension_start = strrpos($file_name, '.');
    if ($extension_start === false) {
        return '';
    }
    else {
        return substr ( $file_name, $extension_start+1);
    }
 }

# ----------------------------------------------------------------------
# Validate date
#
# INPUT:
# 	Date to verify
# OUTPUT:
#   True if date is valid, false if date is invalid
# ----------------------------------------------------------------------
function util_date_isvalid($date) {

    $date_isvalid = true;

    if (!empty($date)) {
        # split date into parts
        $year = substr($date, 0, 4);
        $separator1 = substr($date, 4, 1);
        $month = substr($date, 5, 2);
        $separator2 = substr($date, 7, 1);
        $day = substr($date, 8, 2);

        if ( strlen($date) == 10 &&
             is_numeric($year) && is_numeric($month) && is_numeric($day) &&
             $separator1 == '-' && $separator2 == '-') {
                return checkdate($month, $day, $year);
             }
        else {
            $date_isvalid = false;
        }
    }

    return $date_isvalid;
}

# ----------------------------------------------------------------------
# Prints table row with whitespace in td field
# ----------------------------------------------------------------------
function util_add_spacer() {

	print"<tr><td>&nbsp;</td></tr>";
}

# ----------------------------------------------------------------------
# Returns Timestamp
# OUTPUT:
#   Current unix timestamp with microseconds
# ----------------------------------------------------------------------
 function util_getmicrotime() {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

# ----------------------------------------------------------------------
# Returns a delete message
#
# delete messages are defined in properties_inc.php and strings_english.txt
# ----------------------------------------------------------------------
function util_get_delete_msg( $get_var ) {

	$del_array = lang_get( 'del_msg' );
	$message = $del_array[$get_var];

    return $message;
}

# ----------------------------------------------------------------------
# Next version of the version given
# ----------------------------------------------------------------------
function util_increment_version( $v ) {

	$x = explode(".", $v);
	$m = $x[1] + 1;

	if( $m == 100 ) {

		$x[0]++;
		$m = 0;
	}

	$version = $x[0] . "." . $m;

	return $version;
}

function util_get_display_options( $options, $default_order_by, $default_order_dir, $default_page_number ) {

	$order_by 		= $default_order_by;
	$order_dir 		= $default_order_dir;
	$page_number 	= $default_page_number;

    if( !empty($options['order_by']) ) {

    	$order_by 			= $options['order_by'];
		$order_by_hidden 	= str_replace(" ", "_", $order_by);
		$order_by 			= $options[$order_by_hidden];

		$order_dir 			= $options['order_dir'];
    }

    if( !empty($options["page_number"]) ){
    	$page_number = util_set_page_number($options, $default_page_number);
    }

	return array(	"order_by"=>$order_by,
					"order_dir"=>$order_dir,
					"page_number"=>$page_number );
}

function util_strip_slashes($value)
{
	if( get_magic_quotes_gpc() ) {

		$value = stripslashes($value);
	}

	return $value;
}

function util_strip_html_tags( $str ) {

	return preg_replace("/<[^>]*>/", "", $str);
}

function util_nl2p($str) {

	return '<p align=left>' . preg_replace('#\n|\r#', "</p>\n<p align=left>", $str) . "</p>". NEWLINE;
}

function util_space2nbsp($str) {

	return str_replace('  ', ' &nbsp;', $str);
}
/*
function util_string_insert_href($p_string) {

		# Find any URL in a string and replace it by a clickable link
		$p_string = preg_replace( '/([http|irc|ftp|https]{2,}:\/\/([a-z0-9_-]|\/|\@|:{0,1}\.{0,1}){1,})/i',
									'<a href="\1">\1</a> [<a href="\1" target="blank">^</a>]',
									$p_string);

		# Set up a simple subset of RFC 822 email address parsing
		#  We don't allow domain literals or quoted strings
		#  We also don't allow the & character in domains even though the RFC
		#  appears to do so.  This was to prevent &gt; etc from being included.
		#  Note: we could use email_get_rfc822_regex() but it doesn't work well
		#  when applied to data that has already had entities inserted.
		#
		# bpfennig: '@' doesn't accepted anymore
		$t_atom = '[^\'@\'](?:[^()<>@,;:\\\".\[\]\000-\037\177 &]+)';

		# In order to avoid selecting URLs containing @ characters as email
		#  addresses we limit our selection to addresses that are preceded by:
		#  * the beginning of the string
		#  * a &lt; entity (allowing '<foo@bar.baz>')
		#  * whitespace
		#  * a : (allowing 'send email to:foo@bar.baz')
		#  * a \n, \r, or > (because newlines have been replaced with <br />
		#    and > isn't valid in URLs anyway
		#
		# At the end of the string we allow the opposite:
		#  * the end of the string
		#  * a &gt; entity
		#  * whitespace
		#  * a , character (allowing 'email foo@bar.baz, or ...')
		#  * a \n, \r, or <
		$p_string = preg_replace( '/(?<=^|&lt;|[\s\:\>\n\r])('.$t_atom.'(?:\.'.$t_atom.')*\@'.$t_atom.'(?:\.'.$t_atom.')*)(?=$|&gt;|[\s\,\<\n\r])/s',
								'<a href="mailto:\1" target="_new">\1</a>',
								$p_string);

		return $p_string;
}
*/
function util_string_insert_href( $p_string ) {

	# Find any URL in a string and replace it by a clickable link
	$p_string = preg_replace( '/(([[:alpha:]][-+.[:alnum:]]*):\/\/(%[[:digit:]A-Fa-f]{2}|[-_.!~*\';\/?%^\\\\:@&={\|}+$#\(\),\[\][:alnum:]])+)/se',
															 "'<a href=\"'.rtrim('\\1','.').'\">\\1</a> [<a href=\"'.rtrim('\\1','.').'\" target=\"_blank\">^</a>]'",
															 $p_string);
	# Set up a simple subset of RFC 822 email address parsing
	#  We don't allow domain literals or quoted strings
	#  We also don't allow the & character in domains even though the RFC
	#  appears to do so.  This was to prevent &gt; etc from being included.
	#  Note: we could use email_get_rfc822_regex() but it doesn't work well
	#  when applied to data that has already had entities inserted.
	#
	# bpfennig: '@' doesn't accepted anymore
	$t_atom = '[^\'@\'](?:[^()<>@,;:\\\".\[\]\000-\037\177 &]+)';

	# In order to avoid selecting URLs containing @ characters as email
	#  addresses we limit our selection to addresses that are preceded by:
	#  * the beginning of the string
	#  * a &lt; entity (allowing '<foo@bar.baz>')
	#  * whitespace
	#  * a : (allowing 'send email to:foo@bar.baz')
	#  * a \n, \r, or > (because newlines have been replaced with <br />
	#    and > isn't valid in URLs anyway
	#
	# At the end of the string we allow the opposite:
	#  * the end of the string
	#  * a &gt; entity
	#  * whitespace
	#  * a , character (allowing 'email foo@bar.baz, or ...')
	#  * a \n, \r, or <

	$p_string = preg_replace( '/(?<=^|&quot;|&lt;|[\s\:\>\n\r])('.$t_atom.'(?:\.'.$t_atom.')*\@'.$t_atom.'(?:\.'.$t_atom.')*)(?=$|&quot;|&gt;|[\s\,\<\n\r])/s',
							'<a href="mailto:\1" target="_new">\1</a>',
							$p_string);

	return $p_string;
}

function util_html_encode_string($str) {

	$return_value = $str;
	$return_value = util_space2nbsp($return_value);
	$return_value = util_nl2p($return_value);
	$return_value = util_string_insert_href($return_value);

	return $return_value;
}

# Remove slashes added by PHP, and convert special
# characters to HTML character codes.
function util_html_special_chars_string($str) {

	return htmlspecialchars(util_strip_slashes($str), ENT_QUOTES);
}

function util_unhtmlentities($string) {

   $trans_tbl = array(	"&amp;"	=>	"&",
   						"&quot;"=>	'"',
   						"&#039;"=>	"'",
   						"&lt;"	=>	"<",
   						"&gt;"	=>	">" );

   return strtr($string, $trans_tbl);
}

function util_page_number($page_number, $row_count, $per_page) {

	# Make sure page count is at least 1
	$page_count = ceil($row_count / $per_page );
	if( $page_count < 1 ) {
		$page_count = 1;
	}

	# Make sure page_number isn't past the last page.
	if( $page_number > $page_count ) {
		$page_number = $page_count;
	}

	return $page_number;
}

function util_strip_get($remove_keys, $get) {

	$return_value = array();

	foreach($get as $key => $value) {

		if( !util_array_value_search($remove_keys, $key) ) {

			$return_value[$key] = $value;
		}
	}

	return $return_value;
}

# ----------------------------------------------------------------------
# Convert the number of results to display per page into an acceptable
# range.
# INPUT:
#   Number of results to display per page
# OUTPUT:
#   Validated number of results to display per page
# ----------------------------------------------------------------------
function util_per_page( $per_page ) {

    if( empty( $per_page ) ) {
        $per_page = 25;
    }
    if( $per_page > 999 ) {
        $per_page = 999;
    }
    if( $per_page < 15 ) {
        $per_page = 15;
    }
    return $per_page;
}

# ------------------------------------
# $Log: util_api.php,v $
# Revision 1.5  2009/01/28 07:59:01  cryobean
# Now 999 items per page are allowed
#
# Revision 1.4  2008/01/22 09:50:12  cryobean
# bugfixes - paging should work fine now
#
# Revision 1.3  2007/03/14 17:45:53  gth2
# removing code that passes varables by reference - gth
#
# Revision 1.2  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:13  gth2
# importing initial version - gth
#
# ------------------------------------
?>