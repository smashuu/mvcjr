<?php

namespace MVCJr;

/**
* An abstract class to define a Controller; extend with your own class
* @class Controller
* @constructor
* @param $application {Application} Reference to the main application
*/
abstract class Controller {
	protected $application;
	protected $view;
	protected $models = array();
	public function __construct($application) {
		// Setup app params
		$this->application = $application;
	}

	/**
	* An abstract method to produce output; extend the class to specify
	* @method execute
	* @return {string} The generated output
	*/
	abstract public function execute();

	/**
	* Adds a model to the controller
	* @method addModel
	* @param $name {string} The stored name of the model
	* @param $name {array|stdObj} The model to add
	* @final
	*/
	final public function addModel($name, $model) {
		$this->models[$name] = $model;
	}

	/**
	* Retrieve a single model by name
	* @method getModel
	* @param $name {string} The stored name of the model
	* @return {array|stdObj} The returned model
	* @final
	*/
	final public function getModel($name) {
		return $this->models[$name];
	}

	/**
	* Retrive all the added models, to pass to the view
	* @method getContext
	* @return {stdClass} The context to pass
	* @final
	*/
	final protected function getContext() {
		$context = new \stdClass;
		$context->routeLink = function($route) {
			return $this->application->urlFromRoute($route);
		};
		$context->timestamp = function() {
			return urlencode( date('r') );
		};
		$context->currentLink = $this->application->urlFromRoute($this->application->getCurrentRoute());
		$context->rootUrl = $this->application->getRootUrl();
		foreach ($this->models as $name=>$model) {
			$context->{$name} = $model;
		}
		return $context;
	}

	/**
	* Transforms a route string into a full URL, preserving query string items (via the Application)
	* @method urlFromRoute
	* @param $route {string} The route to transform
	* @return {string} The generated URL
	* @final
	*/
	final public function urlFromRoute($route) {
		return $this->application->urlFromRoute($route);
	}

	/**
	* Pass the models' context to the view, and return the rendered output
	* @method renderView
	* @return {string} The generated output
	* @final
	*/
	final protected function renderView() {
		$context = $this->getContext();
		return $this->view->render($context);
	}
}
