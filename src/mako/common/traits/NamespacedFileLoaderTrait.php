<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\common\traits;

use RuntimeException;

use function array_unshift;
use function explode;
use function str_replace;
use function strpos;
use function vsprintf;

/**
 * Namespaced file loader trait.
 */
trait NamespacedFileLoaderTrait
{
	/**
	 * Default path.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * File extension.
	 *
	 * @var string
	 */
	protected $extension = '.php';

	/**
	 * Namespaces.
	 *
	 * @var array
	 */
	protected $namespaces = [];

	/**
	 * Sets the default path.
	 *
	 * @param string $path Path
	 */
	public function setPath(string $path): void
	{
		$this->path = $path;
	}

	/**
	 * Sets the extension.
	 *
	 * @param string $extension Extension
	 */
	public function setExtension(string $extension): void
	{
		$this->extension = $extension;
	}

	/**
	 * Registers a namespace.
	 *
	 * @param string $namespace Namespace name
	 * @param string $path      Namespace path
	 */
	public function registerNamespace(string $namespace, string $path): void
	{
		$this->namespaces[$namespace] = $path;
	}

	/**
	 * Returns the path to the file.
	 *
	 * @param  string      $file      Filename
	 * @param  string|null $extension File extension
	 * @param  string|null $suffix    Path suffix
	 * @return string
	 */
	protected function getFilePath(string $file, ?string $extension = null, ?string $suffix = null): string
	{
		if(strpos($file, '::') === false)
		{
			// No namespace so we'll just use the default path

			$path = $this->path;
		}
		else
		{
			// The file is namespaced so we'll use the namespace path

			[$namespace, $file] = explode('::', $file, 2);

			if(!isset($this->namespaces[$namespace]))
			{
				throw new RuntimeException(vsprintf('The [ %s ] namespace does not exist.', [$namespace]));
			}

			$path = $this->namespaces[$namespace];
		}

		// Append suffix to path if needed

		if($suffix !== null)
		{
			$path .= DIRECTORY_SEPARATOR . $suffix;
		}

		// Return full path to file

		return $path . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $file) . ($extension ?? $this->extension);
	}

	/**
	 * Returns an array of cascading file paths.
	 *
	 * @param  string      $file      Filename
	 * @param  string|null $extension File extension
	 * @param  string|null $suffix    Path suffix
	 * @return array
	 */
	protected function getCascadingFilePaths(string $file, ?string $extension = null, ?string $suffix = null): array
	{
		$paths = [];

		if(strpos($file, '::') === false)
		{
			// No namespace so we'll just have add a single file

			$paths[] = $this->getFilePath($file, $extension, $suffix);
		}
		else
		{
			// Add the namespaced file first

			$paths[] = $this->getFilePath($file, $extension, $suffix);

			// Prepend the cascading file

			[$package, $file] = explode('::', $file);

			$suffix = 'packages' . DIRECTORY_SEPARATOR . $package . (($suffix !== null) ? DIRECTORY_SEPARATOR . $suffix : '');

			array_unshift($paths, $this->getFilePath($file, $extension, $suffix));
		}

		return $paths;
	}
}
