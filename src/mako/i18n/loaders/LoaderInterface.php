<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\i18n\loaders;

/**
 * Loader interface.
 *
 * @author Frederic G. Østby
 */
interface LoaderInterface
{
	/**
	 * Returns inflection rules or NULL if they doesn't exist.
	 *
	 * @param  string     $language Name of the language pack
	 * @return array|null
	 */
	public function loadInflection(string $language): ?array;

	/**
	 * Loads and returns language strings.
	 *
	 * @param  string                             $language Name of the language pack
	 * @param  string                             $file     File we want to load
	 * @throws \mako\i18n\loaders\LoaderException
	 * @return array
	 */
	public function loadStrings(string $language, string $file): array;
}
