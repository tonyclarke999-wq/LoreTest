<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# LDAP API
#
# $RCSfile: ldap_api.php,v $ $Revision: 1.2 $
# ------------------------------------

# ----------------------------------------------------------------------
#  Attempt to authenticate the user against the LDAP directory
# INPUT:
#   userid and password on LDAP directory  
# OUTPUT:
#   True if user details match those in LDAP directory, otherwise False
# ----------------------------------------------------------------------
function ldap_authenticate( $user_id, $password ) {

    $ldap_server  = LDAP_SERVER;
    $ldap_port    = LDAP_PORT;
    $ldap_id      = LDAP_ID;
    $ldap_pwd     = LDAP_PWD;
    $ldap_root_dn = LDAP_DN;
    $ldap_proto   = LDAP_PROTOCOL;
    
    //$ldap_user = '(&(lmaccessstatusid=active)(uid=' . $user_id . '))';
    $ldap_user = '(uid=' . $user_id . ')';

    
    $ldapconn = ldap_connect($ldap_server, $ldap_port);

    if ( !$ldapconn) { 
        error_report_show("login.php", LDAP_CONNECTION_FAILED);
    }
    
    if (!ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, $ldap_proto)) {
		error_report_show("login.php", LDAP_CONNECTION_FAILED);
    }
    
    $ldapbind = ldap_bind($ldapconn, $ldap_id, $ldap_pwd);
    
    if ( !$ldapbind) { 
        error_report_show("login.php", INVALID_LOGIN );
    }    
    
    $ldapsearch = ldap_search($ldapconn, $ldap_root_dn, $ldap_user);  
    $ldapentries = ldap_get_entries($ldapconn, $ldapsearch);

    $authenticated = false;
    if ( $ldapentries ) {
        # Try to authenticate to each until we get a match
        for ( $i = 0 ; $i < $ldapentries['count'] ; $i++ ) {
            $dn = $ldapentries[$i]['dn'];

            # Attempt to bind with the DN and password
            if ( @ldap_bind( $ldapconn, $dn, $password  ) ) {
                $authenticated = true;
                break;
            } 
            
        }
    }

   ldap_close($ldapconn);
   ldap_free_result( $ldapsearch ) ;
   
   return $authenticated; 
}    

# ------------------------------------
# $Log: ldap_api.php,v $
# Revision 1.2  2006/08/05 22:08:13  gth2
# adding NEWLINE constant to support multiple OS newline chars - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:12  gth2
# importing initial version - gth
#
# ------------------------------------
?>