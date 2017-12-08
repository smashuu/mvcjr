<?php

namespace MVCJr;

/**
* An abstract class to define a View; extend with your own class
* @class View
* @constructor
* @param $application {Controller} Reference to the controller
*/
abstract class View {
	protected $controller;
	public function __construct($controller) {
		// Setup app params
		$this->controller = $controller;
	}

	/**
	* An abstract method to produce output; extend the class to specify
	* @method render
	* @return {string} The generated output
	*/
	abstract public function render($context);
}
