<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Language (Internationalization) API
#
# $RCSfile: lang_api.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

# Cache of localization strings in the language specified by the last
# lang_load call
$g_lang_strings = array();

# Currently loaded language
$g_lang_current = '';

# ------------------
# Loads the specified language and stores it in $g_lang_strings,
# to be used by lang_get
function lang_load( $p_lang ) {
	global $g_lang_strings, $g_lang_current;

	if ( $g_lang_current == $p_lang ) {
		return;
	}

	// define current language here so that when custom_strings_inc is
	// included it knows the current language
	$g_lang_current = $p_lang;

	$t_lang_dir = 'lang/';

	require_once( $t_lang_dir . 'strings_'.$p_lang.'.txt' );

	$t_vars = get_defined_vars();

	foreach ( array_keys( $t_vars ) as $t_var ) {
		$t_lang_var = ereg_replace( '^l_', '', $t_var );
		if ( $t_lang_var != $t_var || 'MANTIS_ERROR' == $t_var ) {
			$g_lang_strings[$t_lang_var] = $$t_var;
		}
	}
}

# -----------------------------------------------------------------------------------
# Loads the user's language or, if the database is unavailable, the default language
# -----------------------------------------------------------------------------------
function lang_load_default() {

	$t_active_language = "english";

	lang_load( $t_active_language );
}

# -----------------------------------------------------------------------------------
# Ensures that a language file has been loaded
# -----------------------------------------------------------------------------------
function lang_ensure_loaded() {
	global $g_lang_current;

	# Load the language, if necessary
	if ( '' == $g_lang_current ) {
		lang_load_default();
	}
}

# -----------------------------------------------------------------------------------
# Retrieves an internationalized string
#  This function will return one of (in order of preference):
#    1. The string in the current user's preferred language (if defined)
#    2. The string in English
# -----------------------------------------------------------------------------------
function lang_get( $p_string ) {

	global $g_lang_strings;

	lang_ensure_loaded();

	# note in the current implementation we always return the same value
	#  because we don't have a concept of falling back on a language.  The
	#  language files actually *contain* English strings if none has been
	#  defined in the correct language
	//print_r($g_lang_strings);
	if ( lang_exists( $p_string ) ) {

		return $g_lang_strings[$p_string];
	} else {

		//trigger_error( ERROR_LANG_STRING_NOT_FOUND, E_USER_WARNING );
		return '';
	}
}

# -----------------------------------------------------------------------------------
# Check the language entry, if found return true, otherwise return false.
# -----------------------------------------------------------------------------------
function lang_exists( $p_string ) {
	global $g_lang_strings;

	lang_ensure_loaded();

	return ( isset( $g_lang_strings[$p_string] ) );
}

# ------------------------------------
# $Log: lang_api.php,v $
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------
?>