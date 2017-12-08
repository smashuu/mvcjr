<?php

namespace MVCJr;

/**
* An abstract class to define an Application; extend with your own class
* @class Application
*/
abstract class Application {
	/**
	* The root URL of the application
	* @property rootUrl
	* @type {string}
	* @default ""
	*/
	protected $rootUrl = '';
	/**
	* The application's default route, if none is provided
	* @property defaultRoute
	* @type {string}
	* @default "home"
	*/
	protected $defaultRoute = 'home';
	/**
	* Whether to use a query string paramter to pass the route
	* @property routeByParam
	* @type {boolean}
	* @default false
	*/
	protected $routeByParam = false;
	/**
	* The query string parameter that holds the route
	* @property routeParam
	* @type {string}
	* @default "p"
	*/
	protected $routeParam = 'p';
	/**
	* The current route, as a string
	* @property route
	* @type {string}
	*/
	protected $route;
	/**
	* The current scheme (HTTP/HTTPS), as a string
	* @property scheme
	* @type {string}
	*/
	protected $scheme;
	/**
	* The current GET query, as a array
	* @property queryParams
	* @type {array}
	*/
	protected $queryParams;

	/**
	* Set the application's default route, if none is provided
	* @method setDefaultRoute
	* @param route {string} The route to set
	* @final
	*/
	final public function setDefaultRoute($route) {
		$this->defaultRoute = $route;
	}
	
	/**
	* Tells the application to use the URL as the route (default)
	* @method routeIsUrl
	* @final
	*/
	final public function routeByUrl() {
		$this->routeByParam = false;
	}

	/**
	* Tells the application to use a query string paramter to pass the route
	* @method routeByParam
	* @param queryParam {string} The query string parameter that holds the route
	* @final
	*/
	final public function routeByParam($queryParam) {
		$this->routeByParam = true;
		$this->routeParam = $queryParam;
	}

	/**
	* Code to execute before the application runs; extend the class to override
	* @method setup
	* @protected
	*/
	protected function setup() {
		// Setup app params
		$this->setScheme();
		$this->setRoute();
		$this->setQueryParams();
	}
	/**
	* Set the query parameters; if the route is not specified, it will be determined from the current URL's query string
	* @method setQueryParams
	* @param [$params] {array} The route to set
	* @final
	*/
	final public function setQueryParams($params = null) {
		if (!$params) {
			$params = $_GET;
		}
		$this->queryParams = $params;
	}
	/**
	* Set the application route; if the route is not specified, it will be determined from the current URL
	* @method setRoute
	* @param [$route] {string} The route to set
	* @final
	*/
	final public function setRoute($route = null) {
		if ($route === null) {
			$route = isset($_GET[$this->routeParam]) ? $_GET[$this->routeParam] : $this->defaultRoute;
		}
		$this->route = $route;
	}
	/**
	* Set the URL scheme; if the scheme is not specified, it will be determined from the current URL
	* @method setScheme
	* @param [scheme] {string} The scheme to set
	* @final
	*/
	final public function setScheme($scheme = null) {
		if ($scheme === null) {
			$scheme = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		}
		$this->scheme = $scheme . '://';
	}
	
	/**
	* Optional, code to execute after the application runs; extend the class to override
	* @method cleanup
	* @protected
	*/
	protected function cleanup() {}

	/**
	* Execute any setup code, run the route handler, then execute any cleanup code
	* @method run
	* @return {string} The generated output
	* @final
	*/
	final public function run() {
		// Object init hooks
		$this->setup();
		$output = $this->doRoute($this->route);
		$this->cleanup();
		return $output;
	}

	/**
	* An abstract method to handle routing; extend the class to specify
	* @method doRoute
	* @protected
	* @param route {string} The route to transform
	* @return {string} The generated output
	*/
	abstract protected function doRoute($route);

	/**
	* Transforms a route string into a full URL, preserving query string items
	* @method urlFromRoute
	* @param route {string} The route to transform
	* @return {string} The generated URL
	* @final
	*/
	final public function urlFromRoute($route) {
		$url = $this->scheme;
		if ($this->routeByParam) {
			$url .=  $this->rootUrl . '/?' . $this->routeParam . '=' . urlencode($route) . (count($this->queryParams) !== 0 ? '&' : '');
		} else {
			$url .= $this->rootUrl . '/' . $route . (count($this->queryParams) !== 0 ? '?' : '');
		}
		if (count($this->queryParams) !== 0) {
			$newParams = $this->params;
			unset($newParams[$this->routeParam]);
			$url .= http_build_query($newParams);
		}
		return $url;
	}

	/**
	* Getter for the current route
	* @method getCurrentRoute
	* @return {string} The generated route
	* @final
	*/
	final public function getCurrentRoute() {
		return $this->route;
	}

	/**
	* Getter for the application's root URL
	* @method getRootUrl
	* @return {string} The root URL
	* @final
	*/
	final public function getRootUrl() {
		return $this->rootUrl;
	}
}
