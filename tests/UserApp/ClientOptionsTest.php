<?php

    namespace UserApp\Tests;

    use \PHPUnit_Framework_TestCase;
    use \UserApp\ClientOptions;
    
    require_once(__DIR__ . "/../bootstrap.php");

    class ClientOptionsTest extends PHPUnit_Framework_TestCase
    {
        private $_options;

        public function setup(){
            $this->_options = new ClientOptions();
        }

        public function testGetDefaultAppId(){
            $this->assertEmpty($this->_options->app_id);
        }

        public function testGetDefaultToken(){
            $this->assertEmpty($this->_options->token);
        }

        public function testSetAppId(){
            $this->_options->app_id = 123;
            $this->assertEquals($this->_options->app_id, "123");
        }

        public function testGetDefaultBaseAddress(){
            $this->assertEquals($this->_options->base_address, "api.userapp.io");
        }

        public function testCreateCarbonCopy(){
            $this->_options->app_id = 123;
            
            $copy = $this->_options->createCopy();
            $this->_options->app_id = 321;

            $this->assertEquals($this->_options->app_id, "321");
            $this->assertEquals($copy->app_id, "123");
        }

        public function testCanPassOptionsInConstructor(){
            $options = new ClientOptions(array(
                "app_id" => "abc",
                "token" => "def"
            ));

            $this->assertEquals($options->app_id, "abc");
            $this->assertEquals($options->token, "def");
        }

        public function testGetDefaultValues(){
            $this->assertEquals($this->_options->app_id, null);
            $this->assertEquals($this->_options->token, null);
            $this->assertEquals($this->_options->debug, false);
            $this->assertEquals($this->_options->secure, true);
            $this->assertEquals($this->_options->version, 1);
            $this->assertEquals($this->_options->base_address, "api.userapp.io");
            $this->assertEquals($this->_options->throw_errors, true);
        }

        public function testGetGlobalSingleton(){
            $options = ClientOptions::getGlobal();
            $this->assertNotEmpty($options);
            $this->assertTrue($options instanceof ClientOptions);
        }

        /**
          * @expectedException \InvalidArgumentException
          * @expectedExceptionMessage Unable to set option. Option 'unknown' does not exist.
          */
        public function testSettingInvalidOption(){
            $this->_options->unknown = "123";
        }
    }

?>