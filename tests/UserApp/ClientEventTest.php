<?php

    namespace UserApp\Tests;

    use \UserApp\ClientProxy;
    use \PHPUnit_Framework_TestCase;
    use \UserApp\Tests\Core\TestTransport;

    require_once(__DIR__ . "/../bootstrap.php");

    class ClientEventTest extends PHPUnit_Framework_TestCase
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

        public function testThatEventIsEmittedOnError(){
            $received_events = array();

            $this->_proxy->on('success', function($sender, $call_context, $result) use (&$received_events){
                $received_events[] = array('name' => 'success', 'result' => $result);
            });

            $this->_proxy->on('error', function($sender, $call_context, $error) use (&$received_events){
                $received_events[] = array('name' => 'error', 'error' => $error);
            });
            
            $result = $this->_proxy->user->get();

            $this->assertTrue(count($received_events) == 1);
            $this->assertTrue($received_events[0]['name'] == 'error');
            $this->assertTrue($received_events[0]['error']->error_code == 'FAKE_RESULT');
        }


        public function testThatEventIsEmittedOnSuccess(){
            $received_events = array();

            $this->_proxy->on('success', function($sender, $call_context, $result) use (&$received_events){
                $received_events[] = array('name' => 'success', 'result' => $result);
            });

            $this->_proxy->on('error', function($sender, $call_context, $error) use (&$received_events){
                $received_events[] = array('name' => 'error', 'error' => $error);
            });

            $this->_transport->forRequestRespondWith('https://api.userapp.io/v1/user.get', array(
                'data' => 'test'
            ));
            
            $result = $this->_proxy->user->get();

            $this->assertTrue(count($received_events) == 1);
            $this->assertTrue($received_events[0]['name'] == 'success');
            $this->assertTrue($received_events[0]['result']->data == 'test');
        }
    }

?>
