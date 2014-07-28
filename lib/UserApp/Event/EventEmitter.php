<?php

	/*
	Copyright (C) 2013-2014 fruux GmbH (https://fruux.com/)

	All rights reserved.

	Redistribution and use in source and binary forms, with or without modification,
	are permitted provided that the following conditions are met:

	* Redistributions of source code must retain the above copyright notice,
	  this list of conditions and the following disclaimer.
	* Redistributions in binary form must reproduce the above copyright notice,
	  this list of conditions and the following disclaimer in the documentation
	  and/or other materials provided with the distribution.
	* Neither the name Sabre nor the names of its contributors
	  may be used to endorse or promote products derived from this software
	  without specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
	ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
	LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
	CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.*/

	/*Modified a part of Sabre\Event instead of linking due to PHP 5.3 compatibility (traits nor short arrays does not exist in 5.3). */

	namespace UserApp\Event;

	/**
	 * Event Emitter
	 *
	 * This contains all the basic functions of an event emitter.
	 *
	 * @copyright Copyright (C) 2013-2014 fruux GmbH. All rights reserved.
	 * @author Evert Pot (http://evertpot.com/)
	 * @license http://sabre.io/license/
	 */
	class EventEmitter {
	    /**
	     * The list of listeners
	     *
	     * @var array
	     */
	    protected $listeners = array();

	    /**
	     * Subscribe to an event.
	     *
	     * @param string $event_name
	     * @param callable $callback
	     * @param int $priority
	     * @return void
	     */
	    public function on($event_name, $callback, $priority = 100) {
	    	if(!is_callable($callback)){
	    		throw new \InvalidArgumentException("Argument 'callback' is not callable.");
	    	}

	        if (!isset($this->listeners[$event_name])) {
	            $this->listeners[$event_name] = array(
	                true,  // If there's only one item, it's sorted
	                array($priority),
	                array($callback)
	            );
	        } else {
	            $this->listeners[$event_name][0] = false; // marked as unsorted
	            $this->listeners[$event_name][1][] = $priority;
	            $this->listeners[$event_name][2][] = $callback;
	        }
	    }

	    /**
	     * Emits an event.
	     *
	     * This method will return true if 0 or more listeners were succesfully
	     * handled. false is returned if one of the events broke the event chain.
	     *
	     * If the continue_callback is specified, this callback will be called every
	     * time before the next event handler is called.
	     *
	     * If the continue_callback returns false, event propagation stops. This
	     * allows you to use the eventEmitter as a means for listeners to implement
	     * functionality in your application, and break the event loop as soon as
	     * some condition is fulfilled.
	     *
	     * Note that returning false from an event subscriber breaks propagation
	     * and returns false, but if the continue-callback stops propagation, this
	     * is still considered a 'successful' operation and returns true.
	     *
	     * Lastly, if there are 5 event handlers for an event. The continue_callback
	     * will be called at most 4 times.
	     *
	     * @param string $event_name
	     * @param array $arguments
	     * @param callback $continue_callback
	     * @return bool
	     */
	    public function emit($event_name, array $arguments = array(), $continue_callback = null) {
	    	if($continue_callback !== null && !is_callable($continue_callback)){
	    		throw new \InvalidArgumentException("Argument 'continue_callback' is not callable.");
	    	}

	        if (is_null($continue_callback)) {
	            foreach($this->listeners($event_name) as $listener) {
	                $result = call_user_func_array($listener, $arguments);

	                if ($result === false) {
	                    return false;
	                }
	            }
	        } else {
	            $listeners = $this->listeners($event_name);
	            $counter = count($listeners);

	            foreach($listeners as $listener) {
	                $counter--;
	                $result = call_user_func_array($listener, $arguments);

	                if ($result === false) {
	                    return false;
	                }

	                if ($counter>0) {
	                    if (!$continue_callback()) break;
	                }
	            }
	        }

	        return true;
	    }

	    /**
	     * Returns the list of listeners for an event.
	     *
	     * The list is returned as an array, and the list of events are sorted by
	     * their priority.
	     *
	     * @param string $event_name
	     * @return callable[]
	     */
	    public function listeners($event_name) {
	        if (!isset($this->listeners[$event_name])) {
	            return array();
	        }

	        // The list is not sorted
	        if (!$this->listeners[$event_name][0]) {
	            // Sorting
	            array_multisort($this->listeners[$event_name][1], SORT_NUMERIC, $this->listeners[$event_name][2]);

	            // Marking the listeners as sorted
	            $this->listeners[$event_name][0] = true;
	        }

	        return $this->listeners[$event_name][2];
	    }

	    /**
	     * Removes a specific listener from an event.
	     *
	     * If the listener could not be found, this method will return false. If it
	     * was removed it will return true.
	     *
	     * @param string $event_name
	     * @param callable $listener
	     * @return bool
	     */
	    public function removeListener($event_name, $listener) {
	    	if(!is_callable($listener)){
	    		throw new \InvalidArgumentException("Argument 'listener' is not callable.");
	    	}

	        if (!isset($this->listeners[$event_name])) {
	            return false;
	        }

	        foreach($this->listeners[$event_name][2] as $index => $check) {
	            if ($check === $listener) {
	                unset($this->listeners[$event_name][1][$index]);
	                unset($this->listeners[$event_name][2][$index]);
	                return true;
	            }
	        }

	        return false;
	    }

	    /**
	     * Removes all listeners.
	     *
	     * If the event_name argument is specified, all listeners for that event are
	     * removed. If it is not specified, every listener for every event is
	     * removed.
	     *
	     * @param string $event_name
	     * @return void
	     */
	    public function removeAllListeners($event_name = null) {
	        if (!is_null($event_name)) {
	            unset($this->listeners[$event_name]);
	        } else {
	            $this->listeners = array();
	        }
	    }

	}