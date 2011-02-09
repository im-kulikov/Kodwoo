<?php defined('SYSPATH') or die('No direct script access.');
/* Copyright (c) 2011, Tyler Larson. All rights reserved.
 * Use and distribution permitted under the terms of the MIT license.
 * See LICENSE.txt for details.
 */

class Kohana_Kodwoo_Internal_Template extends Dwoo_Template_File {

	/**
	 * Find template file based on Kohana's find_file mechanism
	 * @param string file name
	 * @param string configuration group (NULL for defaults)
	 * @return string file path if found, NULL if not
	 */
	protected static function find_local_file($file, $group) {
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
			if ($group==null) $ext = 'tpl';
			$ext = Arr::get(Kohana::config("kodwoo.$group"),'extension','tpl');
		}
		if (($path = Kohana::find_file('views', $file, $ext)) === FALSE)
		{
			return null;
		}
		$path = strtr($path, '\\', '/');
		return $path;
	}

	/**
	 * Get template loader identifier
	 * @return loader name
	 */
	public function getResourceName()
	{
		return 'kodwoo';
	}

	/**
	 * returns a new template object from the given include name, null if no include is
	 * possible (resource not found), or false if include is not permitted by this resource type
	 *
	 * This function is modified from Dwoo_Template_File to search using the Kohana file include method if the
	 * file name begins with a "~". E.g.: "~errors/404" will search for "./views/errors/404.tpl" in
	 * the application, modules, and system path.
	 *
	 * @param Dwoo $dwoo the dwoo instance requiring it
	 * @param mixed $resourceId the filename (relative to this template's dir) of the template to include
	 * @param int $cacheTime duration of the cache validity for this template,
	 * 						 if null it defaults to the Dwoo instance that will
	 * 						 render this template
	 * @param string $cacheId the unique cache identifier of this page or anything else that
	 * 						  makes this template's content unique, if null it defaults
	 * 						  to the current url
	 * @param string $compileId the unique compiled identifier, which is used to distinguish this
	 * 							template from others, if null it defaults to the filename+bits of the path
	 * @param Dwoo_ITemplate $parentTemplate the template that is requesting a new template object (through
	 * 											an include, extends or any other plugin)
	 * @return Dwoo_Template_File|null
	 */
	public static function templateFactory(Dwoo $dwoo, $resourceId, $cacheTime = null, $cacheId = null, $compileId = null, Dwoo_ITemplate $parentTemplate = null)
	{
		if (DIRECTORY_SEPARATOR === '\\') {
			$resourceId = str_replace(array("\t", "\n", "\r", "\f", "\v"), array('\\t', '\\n', '\\r', '\\f', '\\v'), $resourceId);
		}
		$resourceId = strtr($resourceId, '\\', '/');

		$includePath = null;

		// Kohana cascading path
		if (substr($resourceId,0,1)=='~') {
			if (property_exists($dwoo, "_group")) $group = $dwoo->_group;
			else $group = 'default';
			$resourceId = self::find_local_file(substr($resourceId,1), $group);
			if ($resourceId===null) return null;
		}

		if (file_exists($resourceId) === false) {
			if ($parentTemplate === null) {
				$parentTemplate = $dwoo->getTemplate();
			}
			if ($parentTemplate instanceof Dwoo_Template_File) {
				if ($includePath = $parentTemplate->getIncludePath()) {
					if (strstr($resourceId, '../')) {
						throw new Dwoo_Exception('When using an include path you can not reference a template into a parent directory (using ../)');
					}
				} else {
					$resourceId = dirname($parentTemplate->getResourceIdentifier()).DIRECTORY_SEPARATOR.$resourceId;
					if (file_exists($resourceId) === false) {
						return null;
					}
				}
			} else {
				return null;
			}
		}

		if ($policy = $dwoo->getSecurityPolicy()) {
			while (true) {
				if (preg_match('{^([a-z]+?)://}i', $resourceId)) {
					throw new Dwoo_Security_Exception('The security policy prevents you to read files from external sources : <em>'.$resourceId.'</em>.');
				}

				if ($includePath) {
					break;
				}

				$resourceId = realpath($resourceId);
				$dirs = $policy->getAllowedDirectories();
				foreach ($dirs as $dir=>$dummy) {
					if (strpos($resourceId, $dir) === 0) {
						break 2;
					}
				}
				throw new Dwoo_Security_Exception('The security policy prevents you to read <em>'.$resourceId.'</em>');
			}
		}

		$class = 'Kodwoo_Internal_Template';
		if ($parentTemplate) {
			$class = get_class($parentTemplate);
		}
		return new $class($resourceId, $cacheTime, $cacheId, $compileId, $includePath);
	}
}