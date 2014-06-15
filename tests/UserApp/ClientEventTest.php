<?php

    namespace UserApp\Tests;

    use \UserApp\ClientProxy;
    use \PHPUnit_Framework_TestCase;
    use \UserApp\Tests\Core\TestTransport;

    require_once(__DIR__ . "/../bootstrap.php");

    class ClientProxyTest extends PHPUnit_Framework_TestCase
    {
        private $_proxy;
        private $_transport;

        public function setup(){
            $proxy = $this->_proxy = new ClientProxy(array(
                "app_id" => "123",
                "throw_errors" => false
            ));

            $transport = $this->_transport = new TestTransport($this);
            $proxy->setTransport($transport);
        }

        public function testDefaultVersionedMethodCall(){
            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v1/user.get");
            $this->_proxy->user->get();
        }

        public function testMethodCall(){
            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v1/user.get");
            $this->_proxy->v1->user->get();
        }

        public function testDeepServiceCall(){
            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v1/user.invoice.get");
            $this->_proxy->v1->user->invoice->get();
        }

        public function testInsecureMethodCall(){
            $this->_proxy->setOption("secure", false);
            $this->_transport->assertNextRequest("POST", "http://api.userapp.io/v1/user.get");
            $this->_proxy->v1->user->get();
        }

        public function testBaseAddressMethodCall(){
            $this->_proxy->setOption("base_address", "10.0.0.1");
            $this->_transport->assertNextRequest("POST", "https://10.0.0.1/v1/user.get");
            $this->_proxy->v1->user->get();
        }

        /**
          * @expectedException \UserApp\Exceptions\NotSupportedException
          * @expectedExceptionMessage Unable to call method on base service.
          */
        public function testInvalidMethodCall(){
            $this->_proxy->setOption("secure", false);
            $this->_proxy->get();
        }

        public function testVersionedMethodCall(){
            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v2/user.get");
            $this->_proxy->v2->user->get();
        }

        public function testMethodCallWithArguments(){
            $arguments = array("user_id" => "abc");
            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v1/user.get", null, json_encode($arguments));
            $this->_proxy->user->get($arguments);
        }

        public function testJsonMethodCall(){
            $arguments = array("user_id" => "abc");
            
            $header_test = function($test, $headers){
                $test->assertEquals($headers["Content-Type"], "application/json");
            };

            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v1/user.get", $header_test, json_encode($arguments));
            $this->_proxy->user->get($arguments);
        }

        public function testAppIdAuthentication(){
            $arguments = array("user_id" => "abc");
            
            $header_test = function($test, $headers){
                $test->assertEquals($headers["Authorization"], "Basic " . base64_encode("123:"));
            };

            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v1/user.get", $header_test);
            $this->_proxy->user->get($arguments);
        }

        public function testTokenAuthentication(){
            $arguments = array("user_id" => "abc");

            $this->_proxy->setOption('token', '321');
            
            $header_test = function($test, $headers){
                $test->assertEquals($headers["Authorization"], "Basic " . base64_encode("123:321"));
            };

            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v1/user.get", $header_test);
            $this->_proxy->user->get($arguments);
        }

        public function testFullAuthentication(){
            $arguments = array("user_id" => "abc");

            $this->_proxy->setOptions(array(
                'app_id' => '123',
                'token' => '321'
            ));
            
            $header_test = function($test, $headers){
                $test->assertEquals($headers["Authorization"], "Basic " . base64_encode("123:321"));
            };

            $this->_transport->assertNextRequest("POST", "https://api.userapp.io/v1/user.get", $header_test);
            $this->_proxy->user->get($arguments);
        }

        public function teardown(){
            $this->_transport->assertEmptyQueue();
        }
    }

?>