<?
	class Controller_action
	{
		static public function main($method)
		{
			switch($method)
			{
				case "login":
					/* Create TwitterOAuth object and get request token */
					$connection = new TwitterOAuth(TWITTER_OAUTH_CONSUMER_KEY, TWITTER_OAUTH_CONSUMER_SECRET);
				
					/* Get request token */
					$request_token = $connection->getRequestToken(TWITTER_OAUTH_CALLBACK);

					/* Save request token to session */
					$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
					$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

					/* If last connection fails don't display authorization link */
					switch ($connection->http_code)
					{
					  case 200:
					    /* Build authorize URL */
					    $url = $connection->getAuthorizeURL($token);
					    header('Location: ' . $url); 
					    break;
					  default:
					    echo 'Could not connect to Twitter. Refresh the page or try again later.';
						die();
					    break;
					}
					break;
				
				case "twitter_oauth_callback":
					/* If the oauth_token is old redirect to the connect page. */
					if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
					  //$_SESSION['oauth_status'] = 'oldtoken';
					  header('Location: /action/logout/');
					}

					/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
					$connection = new TwitterOAuth(TWITTER_OAUTH_CONSUMER_KEY, TWITTER_OAUTH_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

					/* Request access tokens from twitter */
					$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

					/* Save the access tokens. Normally these would be saved in a database for future use. */
					$_SESSION['access_token'] = $access_token;

					/* Remove no longer needed request tokens */
					unset($_SESSION['oauth_token']);
					unset($_SESSION['oauth_token_secret']);
					
					/* If HTTP response is 200 continue otherwise send to connect page to retry */
					if (200 == $connection->http_code) {
					  /* The user has been verified and the access tokens can be saved for future use */
					
					  $user_data = TWITTER::call('account/verify_credentials');
					
					  $_SESSION['twitter']['user_id']   = $user_data->id;
					  $_SESSION['twitter']['username']  = $user_data->screen_name;
					  $_SESSION['twitter']['full_name'] = $user_data->full_name;
					  $_SESSION['status'] = 'verified';
					
					  header('Location: /home');
					} else {
					  /* Save HTTP status for error dialog on connnect page.*/
					  header('Location: /action/logout/');
					}
					
					
					break;
				case "logout":
					SECURITY::logout();
					URL::redirect("/");
					break;
					
				case "proxy":
					self::proxy();
					break;
			}
		}
		
		static public function proxy()
		{
			if ($_SERVER['REQUEST_METHOD'] == "POST")
			{
				$params = $_POST;
			}
			else
			{
				$params = $_GET;
			}
			
			$class 	= $params['class'];
			$method = $params['method'];
			unset($params['class']);
			unset($params['method']);
			$response = API::post($class, $method, $params);
			
			header("Content-type: application/json");
			echo json_encode($response);
		}
	}
?>