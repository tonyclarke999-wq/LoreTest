<HTML>
<HEAD>
<TITLE>Title</TITLE>
<?php include ".//WebCalendar/includes/styles.inc"; ?>
</HEAD>
<BODY BGCOLOR="<?php echo $BGCOLOR; ?>">

<H2><FONT COLOR="<?php echo $H2COLOR;?>">Help: Preferences</FONT></H2>

<H3>Settings</H3>
<TABLE BORDER=0>

<TR><TD VALIGN="top"><B>Language:</B></TD>
  <TD>language-help</TD></TR>
<TR><TD VALIGN="top"><B>Preferred view:</B></TD>
  <TD>preferred-view-help</TD></TR>
<TR><TD VALIGN="top"><B>Time format:</B></TD>
  <TD>time-format-help</TD></TR>
<TR><TD VALIGN="top"><B>Display unapproved:</B></TD>
  <TD>display-unapproved-help</TD></TR>
<!--
<TR><TD VALIGN="top"><B>Display icons:</B></TD>
  <TD>display-icons-help</TD></TR>
-->
<TR><TD VALIGN="top"><B>Display week number:</B></TD>
  <TD>display-week-number-help</TD></TR>
<TR><TD VALIGN="top"><B>Week starts on:</B></TD>
  <TD>display-week-starts-on</TD></TR>
<TR><TD VALIGN="top"><B>Work hours:</B></TD>
  <TD>work-hours-help
      </TD></TR>

</TABLE>
<P>

<H3>Email</H3>
<TABLE BORDER=0>
<TR><TD VALIGN="top"><B>Event reminders:</B></TD>
  <TD>email-event-reminders-help</TD></TR>
<TR><TD VALIGN="top"><B>Events added to my calendar:</B></TD>
  <TD>email-event-added</TD></TR>
<TR><TD VALIGN="top"><B>Events updated on my calendar:</B></TD>
  <TD>email-event-updated</TD></TR>
<TR><TD VALIGN="top"><B>Events removed from my calendar:</B></TD>
  <TD>email-event-deleted</TD></TR>
<TR><TD VALIGN="top"><B>Event rejected by participant:</B></TD>
  <TD>email-event-rejected</TD></TR>
</TABLE>

<?php if ( $allow_color_customization ) { ?>
<H3>Colors</H3>
colors-help
<P>
<?php } // if $allow_color_customization ?>

<?php include ".//WebCalendar/includes/help_trailer.inc"; ?>

</BODY>
</HTML>
