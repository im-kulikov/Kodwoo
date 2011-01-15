<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Kodwoo_View extends View {

	protected static $_dwoo;
	public $group;

	/**
	 * Kodwoo view factory
	 * @param string  view location
	 * @param array   view data
	 * @param string  configuration group
	 * @return Kodwoo_View
	 */
	public static function factory($file = NULL, array $data = NULL, $group='default')
	{
		return new Kodwoo_View($file, $data);
	}

	/**
	 * Kodwoo generic constructor
	 * @param string  view location
	 * @param array   view data
	 * @param string  configuration group
	 */
	public function __construct($file = NULL, array $data = NULL, $group='default')
	{
		$this->group = $group;
		parent::__construct($file, $data);
	}

	/**
	 * Get (and initialize) the dwoo instance.
	 * @return Dwoo
	 */
	public static function get_dwoo()
	{
		if (!isset(self::$_dwoo)) {
			$dwoo = new Dwoo;
			$config = Kohana::config('dwoo');
			if ($config) {
				foreach ($config as $key=>$value) {
					switch($key){
						case 'compile_dir':
							if ($value && self::writable_dir($value)) $dwoo->setCompileDir($value);
						break;
						case 'cache_dir':
							if ($value && self::writable_dir($value)) $dwoo->setCacheDir($value);
						break;
					}
				}
			}
			self::$_dwoo = $dwoo;
		}
		return self::$_dwoo;
	}

	/**
	 * Get the Dwoo compiler to use.
	 *   Override and customize if you need a different compiler.
	 * @return Dwoo_Compiler
	 */
	protected function get_compiler()
	{
		$config = Kohana::config("dwoo.$this->group");
		$compiler = new Dwoo_Compiler();
		$compiler->setAutoEscape(Arr::get($config,'auto_escape',TRUE));
		return $compiler;
	}

	/**
	 * Attempts to create a directory if it doesn't exist.
	 * @param string Path to create
	 * @return bool  True if directory exists and is writable
	 */
	public static function writable_dir($path)
	{
		if (is_dir($path)) {
			return is_writable($path);
		}
		if (file_exists($path)) {
			return FALSE; // blocked by file
		}
		return mkdir($path,0777, true);
	}

	/**
	 * Render a template using the Dwoo renderer.
	 * @param string template file
	 * @return string Render output
	 */
	public function render($file = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}
		
		if (empty($this->_file))
		{
			throw new Kohana_View_Exception('You must set the file to use within your view before rendering');
		}

		$data = $this->_data;
		if (View::$_global_data) {
			$data = array_marge(View::$_global_data, $this->_data);
		} else {
			$data = $this->_data;
		}

		$dwoo = self::get_dwoo();
		return $dwoo->get($this->_file,$data,$this->get_compiler());
	}

	/**
	 * Sets the view filename. Modified to use the default from configuration instead of .php
	 *
	 *     $view->set_filename($file);
	 *
	 * @param   string  view filename
	 * @return  View
	 * @throws  Kohana_View_Exception
	 */
	public function set_filename($file)
	{
		// Detect if there was a file extension
		$_file = explode('.', $file);

		// If there are several components
		if (count($_file) > 1)
		{
			// Take the extension
			$ext = array_pop($_file);
			$file = implode('.', $_file);
		}
		// Otherwise set the extension to the configured default
		else
		{
			$ext = Arr::get(Kohana::config("dwoo.$this->group"),'extension','tpl');
		}

		if (($path = Kohana::find_file('views', $file, $ext)) === FALSE)
		{
			throw new Kohana_View_Exception('The requested view :file could not be found', array(
				':file' => $file.'.'.$ext,
			));
		}

		// Store the file path locally
		$this->_file = $path;

		return $this;
	}
}
