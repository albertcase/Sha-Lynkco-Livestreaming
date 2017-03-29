<?php

function __autoload($class) {
	$class_dir = array(
		'^Lib\\' => '',
		'^Core\\' => '',
		'Bundle\\' => 'bundle/',
		);
	foreach($class_dir as $base => $dir) {
		if(preg_match("/$base\/", $class)) {
			$class = str_replace( '\\', DIRECTORY_SEPARATOR, $class);
			require_once(dirname(__FILE__) . '/../' . $dir . $class . '.php' ); 
		}
	}
}

class Core {
	static public function Response() {

		include_once dirname(__FILE__) . "/../config/config.php";
		include_once dirname(__FILE__) . "/../config/router.php";

		$current_router = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
		
		if(isset($routers[$current_router])) {
			$callback = $routers[$current_router];
			self::sendResponse($callback, array());
		}
		foreach($routers as $router => $callback) {
			$pattern = '/' . preg_replace(array('/\//', '/%/'), array('\/', '(.*)'), $router) . '$/';
			if(preg_match($pattern, $current_router, $matches)  && $router != '/') {
				unset($matches[0]);
				self::sendResponse($callback, $matches);
			}			
		}
		echo '<center><h1>404 page not found!</h1></center>';
	}

	static private function sendResponse($callback, $arg) {
		$class = $callback[0] . 'Controller';
		$method = $callback[1] . 'Action';
		$response = call_user_func_array(array(new $class, $method), $arg);
		$response->send();
		exit;
	}
}
