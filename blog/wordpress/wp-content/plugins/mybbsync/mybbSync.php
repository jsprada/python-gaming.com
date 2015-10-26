<?php
/*
Plugin Name: mybbSync
Plugin URI: http://pctricks.ir/
Description: This Plugin Sync Wordpress User With Mybb.
Version: 1.0.2
Author: <a href="http://pctricks.ir/">Mostafa Shiraali</a>
Author URI: http://pctricks.ir/
License: You Can Not Change or Sell.
Text Domain: mybbSync
Domain Path: /languages/
 */
 function mybbSync_active()
 {
 add_option('mbsync_host',"localhost","host of Mybb");
 add_option('mbsync_db',"","Database Name");
 add_option('mbsync_db_username',"","Database User Name");
 add_option('mbsync_db_password',"","Database Password");
 add_option('mbsync_tableprefix',"mybb_","Table Prefix");
 add_option('mbsync_mybbroot',"community","Mybb Root");
 }
 function mybbSync_init()
 {
 register_setting('mbsync_opt','mbsync_host');
 register_setting('mbsync_opt','mbsync_db');
 register_setting('mbsync_opt','mbsync_db_username');
 register_setting('mbsync_opt','mbsync_db_password');
 register_setting('mbsync_opt','mbsync_tableprefix');
 register_setting('mbsync_opt','mbsync_mybbroot');
 }
  function mybbSync_deactivate()
 {
 delete_option('mbsync_host');
 delete_option('mbsync_db');
 delete_option('mbsync_db_username');
 delete_option('mbsync_db_password');
 delete_option('mbsync_tableprefix');
 delete_option('mbsync_mybbroot');
 }
 if ( ! function_exists ( 'mybbSync_lang_init' ) ) {
 function mybbSync_lang_init()
 {
   load_plugin_textdomain( 'mybbSync', false,dirname( plugin_basename( __FILE__ ) ) .'/languages/' );
 }
 }
 function mybbSync_menu() {
	add_options_page(__("Mybb User Sync","mybbSync"), __("Mybb Sync","mybbSync"), 10, __FILE__,"mybbSync_display_options");
}
function mybbSync_display_options()
{
?>
	<div class="wrap">
	<h2><?php _e("Mybb User Sync Options","mybbSync")?></h2>        
	<form method="post" action="options.php">
	<?php settings_fields('mbsync_opt'); ?>
	<table class="form-table">
	    <tr valign="top">
            <th scope="row"><label><?php _e("Host of Mybb","mybbSync");?></label></th>
			<td><span class="description"><?php _e("Insert Mybb host .Default : localhost","mybbSync")?></span></td>
			<td><input type="text" name="mbsync_host" value="<?php echo get_option('mbsync_host'); ?>" /> </td>
        </tr>
		<tr valign="top">
            <th scope="row"><label><?php _e("Database name","mybbSync");?></label></th>
			<td><input type="text" name="mbsync_db" value="<?php echo get_option('mbsync_db'); ?>" /> </td>
        </tr>
		<tr valign="top">
            <th scope="row"><label><?php _e("Database Username","mybbSync");?></label></th>
			<td><input type="text" name="mbsync_db_username" value="<?php echo get_option('mbsync_db_username'); ?>" /> </td>
        </tr>
		<tr valign="top">
            <th scope="row"><label><?php _e("Database Password","mybbSync");?></label></th>
			<td><input type="password" name="mbsync_db_password" value="<?php echo get_option('mbsync_db_password'); ?>" /> </td>
        </tr>
		<tr valign="top">
            <th scope="row"><label><?php _e("Mybb Table Prefix","mybbSync");?></label></th>
			<td><input type="text" name="mbsync_tableprefix" value="<?php echo get_option('mbsync_tableprefix'); ?>" /> </td>
        </tr>	
		<tr valign="top">
            <th scope="row"><label><?php _e("Mybb Root Folder","mybbSync");?></label></th>
			<td><input type="text" name="mbsync_mybbroot" value="<?php echo get_option('mbsync_mybbroot'); ?>" /> </td>
        </tr>
	</table>
<?php submit_button(); ?>
		</form>
	</div>
<?php
}

function mybbSync_login($user_login,$user)
{
/******************DATABASE Connecting ****************************/
$conecting=array(
'DBName'=>'poppreoject',
'DBUser'=>'root',
'DBPassword'=>'',
);
$dbcon=mysql_connect(get_option('mbsync_host'),get_option('mbsync_db_username'),get_option('mbsync_db_password'));
mysql_select_db(get_option('mbsync_db'));
/******************DATABASE Connecting ****************************/

$ms_username=$user->user_login;
$ms_email=$user->user_email;
$ms_password=$_POST['pwd'];
$query = mysql_query("SELECT * FROM ".get_option('mbsync_tableprefix')."settings WHERE `name`='bburl'");
$url_fetch=mysql_fetch_array($query);
$webroot='';
if(substr($url_fetch['bburl'], -1)=='/')
{
$webroot=substr($url_fetch['bburl'], 0, -1);
}
else
{
$webroot=$url_fetch['bburl'];
}
$query = mysql_query("SELECT * FROM ".get_option('mbsync_tableprefix')."users WHERE `username`='{$ms_username}' OR `email`='{$ms_email}'");
if(mysql_num_rows($query)==0)
{
/****************************Hash password*******************************/
	function generate_salt()
	{
	$possible = '0123456789abcdefghijklmnopqrstuvwxyz';
		$newsalt = '';
		$i = 0;
		while ($i < 8) { 
			$newsalt .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $newsalt;
	}

function salt_password($password, $salt)
{
	return md5(md5($salt).$password);
}
$salt=generate_salt();
$hashed_password=salt_password(md5($ms_password),$salt);
/****************************Hash password*******************************/
$regdate=time();
$query = mysql_query("INSERT INTO ".get_option('mbsync_tableprefix')."users(username,password,salt,email,receivepms,allownotices,pmnotify,usergroup,regdate) 
VALUES('$ms_username','$hashed_password','$salt','$ms_email',1,1,1,2,'$regdate')");


/****************************CURL Method*******************************/
}

}
add_action('admin_init', 'mybbSync_init' );
add_action('init', 'mybbSync_lang_init');
add_action('admin_init', 'mybbSync_lang_init');
add_action('admin_menu', 'mybbSync_menu');
add_action('wp_login', 'mybbSync_login',10,2);

register_activation_hook( __FILE__, 'mybbSync_active' );
register_deactivation_hook( __FILE__, 'mybbSync_deactivate' );

?>