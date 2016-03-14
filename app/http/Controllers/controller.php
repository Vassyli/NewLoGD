<?php
/**
 * app\http\controllers\controller.php - Default Controller
 * 
 * @author Basilius Sauter
 * @package App
 * @subpackage Http\Controllers
 */
declare(strict_types=1);

namespace App\Http\Controllers;

use NewLoGD\Application;
use NewLoGD\Auth;
use NewLoGD\HttpResponse;

/**
 * Default Controller class
 *
 * @param Application $app Reference to main application instance
 * @param HttpResponse $response Reference to HttpResponse that gets send after executing the controller
 */
class Controller {
	/** @var Application reference to application object */
	protected $app = NULL;
	/** @var HttpResponse reference to httpresponse object */
	protected $response = NULL;
    
    protected $allow_anonymous = true;
	
	/**
	 * Constructor
	 *
	 * @param Application $app Reference to main application instance
	 * @param HttpResponse $response Reference to HttpResponse that gets send after executing the controller
	 */
	public function __construct(Application $app, HttpResponse $response) {
		$this->app = $app;
		$this->response = $response;
	}
	
	/** 
	 * Calls the given callback with given arguments
	 *
	 * This function either directly calls $callback with default arguments or it 
	 * calls a custom function provided by $callback with the arguments in $args
	 * @param mixed $callback either a closure or a valid $method for the subclass that calls this method.
	 * @param array $args arguments to give the callback
	 */
	public function call($callback, array $args = []) {
		if(is_callable($callback)) {
			if(empty($args)) {
				$args = array_merge([$this, $callback], $args);
			}
			$answer = call_user_func_array($callback, $args);
			//$answer = $callback($this->app, $this->response);
		}
		else {
			$answer = call_user_func_array([$this, $callback], $args);
		}
        
        if($this->response->isFinalized()) {
            return false;
        }
		
		// Answer is an array
		if(is_array($answer)) {
			$ignore_json = false;
			// Check if there are any special returns
			if(count($answer) === 2) {
				$ignore_json = true;
				
				if(isset($answer[0]) and isset($answer[1]) and is_int($answer[0])) {
					$callback = [
						HttpResponse::NOTFOUND => "notFound",
						HttpResponse::MOVEDPERMANENTLYA => "redirectPermanentlyA",
					];
					
					if(isset($callback[$answer[0]])) {
						$this->response->{$callback[$answer[0]]}($answer[1]);
					}
					else {
						$ignore_json = false;
					}
				}
				else {
					$ignore_json = false;
				}
			}
			
			if(!$ignore_json) {
				$this->response->json($answer);
			}
		}
        elseif(is_object($answer) and $answer instanceof \JsonSerializable) {
            $this->response->jsonFromObject($answer);
        }
        elseif(is_object($answer)) {
            $this->response->plain((string)$answer);
        }
		else {
			$this->response->plain($answer);
		}
	}
	
	/**
	 * Test function for showing @-Syntax in app/http/routes.php
	 */
	public function test() {
		return "Hello World.";
	}
    
    public function checkAccess() : bool {
        if(Auth::getLoginState() == Auth::OFFLINE) {
            return $this->allow_anonymous;
        }
        
        return true;
    }
    
    /**
     * Returns the current character
     * @return type
     */
    protected function getCurrentCharacter() {
        if(Auth::getActiveUser() === NULL) {
            throw new \Expcetion("[App\Http\Controllers\controller] Return of current character is not possible.");
        }
        
        return Auth::getActiveUser()->getCurrentCharacter();
    }
}