<?
	$path = realpath(dirname($_SERVER['SCRIPT_FILENAME'])."/../");
	if ($path == "/var/www/www.mysite.com")
	{
		define("ENV", "production");
	}
	unset($path);
	
	if (!defined("ENV"))
	{
		die("Invalid site profile");
	}
	
	switch (ENV)
	{
		case "production":
			define('BASE_PATH', 		"/var/www/www.mysite.com/");
            define('SITE_LIVE',         true);
            define('API_URL',         	"http://mysite.com/api/");
            define('BASE_URL',         	"http://mysite.com/");

			define("TWITTER_OAUTH_CONSUMER_KEY", 	"YOUR_KEY");
			define("TWITTER_OAUTH_CONSUMER_SECRET", "YOUR_KEY");
			define("TWITTER_OAUTH_CALLBACK", 		"http://mysite.com/action/twitter_oauth_callback/");
			
			define('DB_HOST', '127.0.0.1');
			define('DB_USER', 'MY_USER');
			define('DB_PASS', 'MY_PASS');
			define('DB_NAME', 'MY_DB');
			break;
		default:
			die("");
	}
	
	require_once 'Zend/Db.php';
	require_once(BASE_PATH . "lib/twitteroauth/twitterOAuth.php");

	$GLOBALS['db'] = Zend_Db::factory('Pdo_Pgsql', array(
	    'host'     => DB_HOST,
	    'username' => DB_USER,
	    'password' => DB_PASS,
	    'dbname'   => DB_NAME
	));
	
	$GLOBALS['twitter'] = new TwitterOAuth(TWITTER_OAUTH_CONSUMER_KEY, TWITTER_OAUTH_CONSUMER_SECRET);

	$db->setFetchMode(Zend_Db::FETCH_ASSOC);
	
	// Auto-load any helpers.
	foreach (glob(BASE_PATH."/helpers/*.php") as $filename) {
	   require_once($filename);
	}
	
?>
