<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Email API
#
# $RCSfile: email_api.php,v $ $Revision: 1.8 $
# ------------------------------------

	# --------------------
	# Return a perl compatible regular expression that will
	#  match a valid email address as per RFC 822 (approximately)
	#
	# The regex will provide too matched groups: the first will be the
	#  local part (or mailbox name) and the second will be the domain
	function email_get_rfc822_regex() {
		# Build up basic RFC 822 BNF definitions.

		# list of the special characters: ( ) < > @ , ; : \ " . [ ]
		$t_specials = '\(\)\<\>\@\,\;\:\\\"\.\[\]';
		# the space character
		$t_space    = '\040';
		# valid characters in a quoted string
		$t_char     = '\000-\177';
		# control characters
		$t_ctl      = '\000-\037\177';

		# a chunk of quoted text (anything except " \ \r are valid)
		$t_qtext_re = '[^"\\\r]+';
		# match any valid character preceded by a backslash ( mostly for \" )
		$t_qpair_re = "\\\\[$t_char]";

		# a complete quoted string - " characters with valid characters or
		#  backslash-escaped characters between them
		$t_quoted_string_re = "(?:\"(?:$t_qtext_re|$t_qpair_re)*\")";

		# an unquoted atom (anything that isn't a control char, a space, or a
		#  special char)
		$t_atom_re  = "(?:[^$t_ctl$t_space$t_specials]+)";

		# a domain ref is an atom
		$t_domain_ref_re = $t_atom_re;

		# the characters in a domain literal can be anything except: [ ] \ \r
		$t_dtext_re = "[^\\[\\]\\\\\\r]";
		# a domain-literal is a sequence of characters or escaped pairs inside
		#  square brackets
		$t_domain_literal_re = "\\[(?:$t_dtext_re|$t_qpair_re)*\\]";
		# a subdomain is a domain ref or a domain literal
		$t_sub_domain_re = "(?:$t_domain_ref_re|$t_domain_literal_re)";
		# a domain is at least one subdomain, with optional further subdomains
		#  separated by periods.  eg: '[1.2.3.4]' or 'foo.bar'
		$t_domain_re = "$t_sub_domain_re(?:\.$t_sub_domain_re)*";

		# a word is either quoted string or an atom
		$t_word_re = "(?:$t_atom_re|$t_quoted_string_re)";

		# the local part of the address spec (the mailbox name)
		#  is one or more words separated by periods
		$t_local_part_re = "$t_word_re(?:\.$t_word_re)*";

		# the address spec is made up of a local part, and @ symbol,
		#  and a domain
		$t_addr_spec_re = "/^($t_local_part_re)\@($t_domain_re)$/";

		return $t_addr_spec_re;
	}
	# --------------------
	# check to see that the format is valid and that the mx record exists
	function email_is_valid( $p_email ) {
		# if we don't validate then just accept
		if ( OFF == config_get( 'validate_email' ) ) {
			return true;
		}

		if ( is_blank( $p_email ) && ON == config_get( 'allow_blank_email' ) ) {
			return true;
		}

		# Use a regular expression to check to see if the email is in valid format
		#  x-xx.xxx@yyy.zzz.abc etc.
		if ( preg_match( email_get_rfc822_regex(), $p_email, $t_check ) ) {
			$t_local = $t_check[1];
			$t_domain = $t_check[2];

			# see if we're limited to one domain
			if ( ON == config_get( 'limit_email_domain' ) ) {
				if ( 0 != strcasecmp( $t_limit_email_domain, $t_domain ) ) {
					return false;
				}
			}

			if ( preg_match( '/\\[(\d+)\.(\d+)\.(\d+)\.(\d+)\\]/', $t_domain, $t_check ) ) {
				# Handle domain-literals of the form '[1.2.3.4]'
				#  as long as each segment is less than 255, we're ok
				if ( $t_check[1] <= 255 &&
					 $t_check[2] <= 255 &&
					 $t_check[3] <= 255 &&
					 $t_check[4] <= 255 ) {
					return true;
				}
			} else if ( ON == config_get( 'check_mx_record' ) ) {
				# Check for valid mx records
				if ( getmxrr( $t_domain, $temp ) ) {
					return true;
				} else {
					$host = $t_domain . '.';

					# for no mx record... try dns check
					if (checkdnsrr ( $host, 'ANY' ))
						return true;
				}
			} else {
				# Email format was valid but did't check for valid mx records
				return true;
			}
		}

		# Everything failed.  The email is invalid
		return false;
	}
	# --------------------
	# Check if the email address is valid
	#  return true if it is, trigger an ERROR if it isn't
	function email_ensure_valid( $p_email ) {
		if ( ! email_is_valid( $p_email ) ) {
			trigger_error( ERROR_EMAIL_INVALID, ERROR );
		}
	}

	function email_send( $recipients, $subject, $message, $from='' ) {

		# short-circuit if no emails should be sent
		if ( !SEND_EMAIL_NOTIFICATION ) {
			return;
		}
		# short-circuit if no recipient is defined
		if ( empty( $recipients ) ) {
			return;
		}


		// Create new phpMailer object
		require_once("./phpmailer/class.phpmailer.php" );
		$mail = new PHPMailer();
		
		// Set the from value if it's not set
		if( empty($from) ) {

			$sender_details = user_get_current_user_name();
			$sender_email	= $sender_details[USER_EMAIL];
			$from = $sender_email;
		}

		# Use SMTP Authentication
		if( SMTP_USERNAME != '' ) {   

			$mail->SMTPAuth = true;
			$mail->Username = SMTP_USERNAME;
			$mail->Password = SMTP_PASSWORD;
		}

		$str_email_to = "";

		
		if( is_array( $recipients ) ) {
			
			foreach($recipients as $recipient) {

				# If $recipents is a recordset, take the user email from the record.
				if( is_array($recipient) ) {

					$recipient = $recipient[USER_EMAIL];
				}

				# check if email address already in recipients string
				# before adding email address
				if( !empty($recipient) && !strpos($str_email_to, $recipient) ) {
					
					$str_email_to .= $recipient .",";
					$mail->AddAddress( $recipient ); 
				}
				
			}
		}
		else {

			$mail->AddAddress( $recipients ); 
		}
		
		$mail->IsSMTP();
		$mail->SMTPKeepAlive = true;
		$mail->IsHTML(true);              # set email format to plain text
		$mail->WordWrap		= 80;              # set word wrap to 50 characters
		$mail->Priority		= 5;               # Urgent = 1, Not Urgent = 5, Disable = 0
		$mail->CharSet		= 'uft-8';
		$mail->Host			= SMTP_HOST;
		$mail->From			= $from;
		$mail->FromName		= $from;		
		$mail->Sender		= ADMIN_EMAIL;
		$mail->Subject		= $subject;
		$mail->Body			= $message;

		/*
		print"host = $mail->Host <br>";
		print"from = $mail->From <br>";
		print"sender = $mail->Sender <br>";
		print"fromName = $mail->FromName <br>";
		*/

		$mail_send_ok = $mail->Send();
		
		if( !$mail_send_ok ) {
			print"<p class='error'>PROBLEMS SENDING MAIL TO: $str_email_to<br>";
			print"PLEASE CHECK YOUR EMAIL PREFERENCES IN properties_inc.php<br>";
			print"Mailer Error: " . $mail->ErrorInfo ."</p>";
			//exit;
		}

	}


	# --------------------
	# this function sends the actual email
	function email_send2( $recipients, $subject, $message, $headers='' ) {

		# short-circuit if no emails should be sent
		if ( !SEND_EMAIL_NOTIFICATION ) {
			return;
		}


		# short-circuit if no recipient is defined
		if ( empty( $recipients ) ) {
			return;
		}

		$subject		= trim( $subject );
		$message		= trim( $message );

		$str_email_to = "";
		if( is_array( $recipients ) ) {
			foreach($recipients as $recipient) {

				# If $recipents is a recordset, take the user email from the record.
				if( is_array($recipient) ) {

					$recipient = $recipient[USER_EMAIL];
				}

				# check if email address already in recipients string
				# before adding email address
				if( !empty($recipient) && !strpos($str_email_to, $recipient) ) {
					$str_email_to .= $recipient.",";
				}
			}
		}
		else {
			$str_email_to = $recipients;
		}

		$str_email_to = trim($str_email_to, ",");

		# short-circuit if $str_email_to is empty
		if ( empty($str_email_to) ) {
			return;
		}

		# for debugging only
		#echo $recipients.'<br>'.$t_subject.'<br>'.$t_message.'<br>'.$t_headers;
		#exit;
		#echo '<br>xxxRecipient ='.$recipients.'<br>';
		#echo 'Headers ='.nl2br($t_headers).'<br>';
		#echo $t_subject.'<br>';
		#echo nl2br($t_message).'<br>';
		#exit;

		# Visit http://www.php.net/manual/function.mail.php
		# if you have problems with mailing

		if( empty($headers) ) {
			$sender_details = user_get_current_user_name();
			$sender_email	= $sender_details[USER_EMAIL];

			$headers = "From: $sender_email". NEWLINE;
		}

		# Temporarily shut off error reporting so we can handle email errors on our own
		error_reporting(0);

		# set the SMTP host... only used on window though
		ini_set( 'SMTP', SMTP_HOST );

		/*
		if ( !is_blank( config_get( 'smtp_username' ) ) ) {     # Use SMTP Authentication
			$mail->SMTPAuth = true;
			$mail->Username = config_get( 'smtp_username' );
			$mail->Password = config_get( 'smtp_password' );
		}
		*/


		$mail_send_ok = mail( $str_email_to, $subject, $message, $headers );

		if ( !$mail_send_ok ) {
			PRINT"<p class=error>PROBLEMS SENDING MAIL TO: $str_email_to</p><br>";
			PRINT"PLEASE CHECK YOUR EMAIL PREFERENCES IN properties_inc.php<br>";
			PRINT "To: ". htmlspecialchars($str_email_to).'<br>';
			PRINT nl2br(htmlspecialchars($headers));
			PRINT "Subject: ". htmlspecialchars($subject).'<br><br>';
			PRINT "Message: <br>";
			PRINT nl2br(htmlspecialchars($message)).'<br>';
			exit;
		}
	}


# --------------------------------------------------------
# $Log: email_api.php,v $
# Revision 1.8  2007/02/03 11:58:37  gth2
# no message
#
# Revision 1.7  2006/12/05 05:29:19  gth2
# updates for 1.6.1 release
#
# Revision 1.6  2006/10/18 12:57:40  gth2
# don't exit the page when email fails - gth
#
# Revision 1.5  2006/10/11 02:41:11  gth2
# adding phpMailer - gth
#
# Revision 1.4  2006/08/05 22:31:46  gth2
# adding NEWLINE constant to support mulitple OS - gth
#
# Revision 1.3  2006/02/27 17:24:56  gth2
# added email functionality to bug tracker - gth
#
# Revision 1.2  2006/02/24 11:33:31  gth2
# minor bug fixes and enhancements for 1.5.1 release - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# --------------------------------------------------------
?>