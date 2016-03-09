<?php
/**
 * newlogd/httpresponse.php
 *
 * @author Basilius Sauter
 * @package NewLoGD
 */
declare(strict_types=1);

namespace NewLoGD;

/**
 * Object for writing and sending a response over HTTP
 *
 * This object manages the http response and provides constants
 * for most http response codes.
 * @param string $path Path that got called.
 */
class HttpResponse {
	const OK = 200;
	const CREATED = 201;
	const ACCEPTED = 202;
	const NOCONTENT = 204;
	const RESETCONTENT = 205;
	
	const MOVEDPERMANENTLYA = 301;
	const MOVEDTEMPORARELYA = 302;
	const SEEOTHER = 303;
	const NOTMODIFIED = 304;
	const MOVEDTEMPORARELYB = 307;
	const MOVEDPERMANENTLYB = 308;
	
	const BADREQUEST = 400;
	const UNAUTHORIZED = 401;
	const FORBIDDEN = 403;
	const NOTFOUND = 404;
	const METHODNOTALLOWED = 405;
	const TEAPOT = 418;
    const UNPROCESSABLEENTITY = 422;
	const TOOMANYREQUESTS = 429;
	const CENSORED = 451;
	
	const INTERNALERROR = 500;
	const NOTIMPLEMENTED = 501;
	const SERVICEUNAVAILABLE = 503;
	
	const CUSTOMS = [
		418 => "I'm a Teapot", 
		451 => "Unavailable For Legal Reasons",
	];
	
	const CONTENT_PLAIN = "text/plain";
	const CONTENT_JSON = "application/json";
	
	/** @var int http status code (default: 200 for OK)*/
	protected $status;
	/** @var string body of the http response */
	protected $body = "";
    /** @var string $contenttype */
    protected $contenttype = "text/plain";
    /** @var string $path Called path */
    protected $path = "";
    /** @var bool $sent True if http response already been sent to the browser */
    protected $sent = false;
    /** @var bool $finalized True if http response is already finalized and cannot be edited anymore */
    protected $finalized = false;
	
	public function __construct(string $path) {
		// Start output buffering
		ob_start();
		
		// Set defaults
		$this->status = self::OK;
        $this->path = $path;
	}
    
    public function getField() {
        
    }
    
    /**
     * Returns the requested path
     * @return string $path
     */
    public function getPath() : string {
        return $this->path;
    }
    
    /**
     * Returns whether the http response already bas been sent or not.
     * @return bool True if http response already has been sent
     */
    public function isSent() : bool {
        return $this->sent;
    }
    
    /**
     * Returns true if the http response is already finalized 
     * @return bool
     */
    public function isFinalized() : bool {
        return $this->finalized;
    }
	
    /**
     * Finalizes the http response
     * @throws \Exception if already finalized
     */
    protected function finalize() {
        if($this->isFinalized()) {
            throw new \Exception("[HttpResponse] Already finalized");
        }
        
        $this->finalized = true;
    }
    
	/**
	 * Sets the http response body to a specific string
	 * @param string $body http response body gets set to this string
	 */
	public function setBody(string $body) {
		$this->body = $body;
	}
	
	/**
	 * Sets the http response status to a integer. Use of class constants recommended
	 * @param int http response status
	 */
	public function setStatus(int $status) {
		$this->status = $status;
    }
	
	/**
	 * Sends the http response with output buffer attached at the end of the message
     * @throws \Exception
	 */
	public function send() {
        if($this->isSent()) {
            throw new \Exception("[HttpResponse] Cannot resend httpresponse since it was already sent.");
        }
        
        $this->sendHeaders();
        $this->sendBody();
        
        $this->sent = true;
	}
    
    /**
     * Sends http headers
     */
    protected function sendHeaders() {
        // Send the HTTP Response Code
		if(isset(self::CUSTOMS[$this->status])) {
            // Custom HTTP Response Code - we need to write it manually
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			\header(implode(" ", [$protocol, $this->status, self::CUSTOMS[$this->status]]));
		}
		else {
			// Send Status Code
			\http_response_code($this->status);
			
			// Remove default headers
			\header_remove("X-Powered-By");
            
            // Send other headers
            \header("Content-Type: ".$this->contenttype."; charset=utf-8");
		}
    }
    
    /**
     * Sends http body 
     */
    protected function sendBody() {
        // Sends the body of the message
        print($this->body);
        // Empties the output buffer and sends it as well
        ob_end_flush();
    }
	
	/**
	 * Sets the content type
     * @param string $contenttype The content type
	 */
	protected function setContentType(string $contenttype) {
        $this->contenttype = $contenttype;
	}
    
    /**
	 * Sets the http response body to the given string and sends a http-encoding header for plain text
	 * @param string $body http response body gets set to this string
	 */
	public function plain(string $body) {
        $this->finalize();
        
		$this->setContentType(self::CONTENT_PLAIN);
		$this->setBody($body);
	}
	
	/**
	 * Sets the http response body from an array and sends a http-encoding header for json
	 * @param array $body http response body gets set to a json-encoded string derived from this argument
	 */
	public function json(array $body) {
        $this->finalize();
        
		$this->setContentType(self::CONTENT_JSON);
		$this->setBody(\json_encode($body, JSON_PRETTY_PRINT));
	}
    
    public function jsonFromObject(\JsonSerializable $body) {
        $this->finalize();
        
		$this->setContentType(self::CONTENT_JSON);
		$this->setBody(\json_encode($body, JSON_PRETTY_PRINT));
    }
	
	/**
	 * Sends an error 404
	 * @param string $message Message
	 */
	public function notFound(string $message = "") {
        $this->setStatus(self::NOTFOUND);
		$this->plain("Error 404 - Not Found\r\n".$message);
    }
    
    public function forbidden(string $message = "") {
        $this->setStatus(self::FORBIDDEN);
        $this->plain("Error 403 - Forbidden\r\n".$message);
    }
    
    public function invalidData($message) {
        $this->setStatus(self::UNPROCESSABLEENTITY);
        
        if(is_array($message)) {
            $this->json($message);
        }
        else {
            $this->plain($message);
        }
    }
}