<?php
/**
 * Appearance of email messages
 *
 * This template can be overridden by copying it to yourtheme/wpstore/email/email.php.
 *
 * HOWEVER, on occasion wpStore will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
  <html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<body style="-webkit-text-size-adjust: none; margin: 0;padding: 0px; width: 100% !important; box-sizing: border-box; background-color: #f1f1f1;">
	  <table border="0" cellpadding="0" cellspacing="0" style="width: 90% !important; max-width:530px; padding: 20px 0 0; margin: 0 auto;">
		<tr>
		  <td align="center" valign="top" style="height: 10px; border-top-left-radius: 3px; border-top-right-radius: 3px; -webkit-border-top-left-radius: 3px; -webkit-border-top-right-radius: 3px; background: #0073af; background: repeating-linear-gradient(-45deg, #0073af, #0073af 10px, #ffffff 10px, #ffffff 20px, #e51d1f 20px, #e51d1f 30px); background: -webkit-repeating-linear-gradient(-45deg, #0073af, #0073af 10px, #ffffff 10px, #ffffff 20px, #e51d1f 20px, #e51d1f 30px);">
		  </td>
		</tr>
		<tr>
		  <td align="center" valign="top" style="background: white;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:530px; padding: 0 30px;">
			  <tr>
				<td style="padding: 15px 0; width: 100%;">
				  <h1 style="font-size: 25px; margin: 0; color: #0073af; width: 100%; font-family: Tahoma, Arial, sans-serif; display: inline-block; text-align: center;"><a style="font-weight: 900; text-decoration: none;" href="<?php echo get_site_url(); ?>" target="_blank" ><?php echo wpsl_opt( 'email_from_name' ); ?></a></h1>
				  <span style="color: #000; width: 100%; font-family: Tahoma, Arial, sans-serif;font-size: 11px; display: inline-block; text-align: center;"><?php echo get_bloginfo( 'description' ); ?></span>
				</td>
			  </tr>
			  <tr>
				<td valign="top" colspan="2">
				  <div style="padding: 15px 0; color: #000000;font-family: Tahoma, Arial, sans-serif;font-size: 13px;line-height: 150%;text-align: left;border-top: 1px solid #f1f1f1; border-bottom: 1px solid #f1f1f1;">
					[mail-content]
					<br>
					<br><?php _e( 'Yours faithfully', 'wpsl' ); ?>,
					<br><span style="color: #000;display: block;font-family: Tahoma, Arial, sans-serif;font-size: 16px;font-weight: bold;line-height: 100%;margin-bottom: 10px;text-align: left;"><?php echo wpsl_opt( 'email_from_name' ); ?></span>
				  </div>
				</td>
			  </tr>
			</table>
		  </td>
		</tr>
		<tr>
		  <td valign="middle" style="background-color: #fff;color: #000;">
			<div style="font-family: Tahoma, Arial, sans-serif;font-size: 13px;line-height: 165%;text-align: center; padding: 30px 30px 0;">
				[mail-schedule]
				[mail-address]
				<span style="color: #000;font-family: Tahoma, Arial, sans-serif;font-size: 13px;line-height: 145%; display: inline-block; text-align: center; ">[mail-phone]</span>
			</div>
		  </td>
		</tr>
		<tr style="background-color: #f1f1f1;">
		  <td valign="top" style="height: 0px; background: radial-gradient(transparent, transparent 4px, white 5px,white); background-size: 18px 18px; background-position: 0px 0px; padding: 5px;">
		  </td>
		</tr>
	  </table>
	  <table border="0" cellpadding="10" cellspacing="0" style="width: 100% !important; max-width:530px; padding: 10px 25px; margin: 0 auto;">
		<tbody>
		  <tr>
			<td valign="top">
			  <div style="color: #404040; font-family: Tahoma, Arial, sans-serif; font-size: 11px; line-height: 145%; text-align: center;">[mail-support]</div>
			</td>
		  </tr>
		</tbody>
	  </table>
	</body>
  </html>