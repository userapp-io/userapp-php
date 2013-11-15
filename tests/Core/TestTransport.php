<?php

	namespace UserApp\Tests\Core;

	use \stdClass;
	use \UserApp\Http\Response;
	use \UserApp\Http\ITransport;

	class TestTransport implements ITransport {
		private $_test;
		private $_assertion_queue;

		public function __construct($test){
			$this->_test = $test;
			$this->_assertion_queue = array();
		}

		public function request($method, $url, $headers = null, $body = null){
			if(count($this->_assertion_queue) > 0){
				$assert = array_shift($this->_assertion_queue);

				$this->_test->assertEquals($assert["method"], $method);
				$this->_test->assertEquals($assert["url"], $url);

				if($assert["headers"] != null){
					if(is_callable($assert["headers"])){
						$assert["headers"]($this->_test, $headers);
					}else{
						$this->_test->assertEquals($assert["headers"], $headers);
					}
				}
				if($assert["body"] != null){
					if(is_callable($assert["body"])){
						$assert["body"]($this->_test, $body);
					}else{
						$this->_test->assertEquals($assert["body"], $body);
					}
				}
			}

			return $this->getMockResponse();
		}

		public function assertNextRequest($method, $url, $headers = null, $body = null){
			$this->_assertion_queue[] = array(
				"method" => $method,
				"url" => $url,
				"headers" => $headers,
				"body" => $body
			);
		}

		public function assertEmptyQueue(){
			$this->_test->assertEmpty($this->_assertion_queue);
		}

		private function getMockResponse(){
			$response = new Response();

			$status = new stdClass();
			$status->protocol = "HTTP/1.FAKE";
			$status->code = 200;
			$status->message = "OK";

			$response->status = $status;
			$response->headers = array("Content-Type" => "application/json");
			$response->body = json_encode(array("error_code" => "FAKE_RESULT", "message" => "This is a fake result."));

			return $response;
		}
	}

?>