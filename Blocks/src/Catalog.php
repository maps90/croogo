<?php

namespace Croogo\Blocks;

use Cake\Core\Configure;
use Cake\Utility\Hash;

class Catalog
{

	const PLUGIN_SEPARATOR = '|';

	public static function register($cell, array $config = [])
	{
		static::config($cell, $config);
	}

	public static function info($cell)
	{
		return static::config($cell);
	}

	public static function regionInfo($cell, $region)
	{
		if (!static::config($cell)) {
			return false;
		}

		$config = Hash::merge(
			static::config($cell),
			static::regionConfig($cell, $region)
		);

		unset($config['regions'], $config['regionAliases']);

		return $config;
	}

	public static function regions($cell)
	{
		$config = static::config($cell);

		$regions = [];
		foreach ($config['regions'] as $regionAlias => $regionConfig) {
			$regions[$regionAlias] = static::regionInfo($cell, $regionAlias);
		}

		foreach ($config['regionAliases'] as $regionAliasName => $regionTarget) {
			$regions[$regionTarget]['aliases'][] = $regionAliasName;
		}

		return $regions;
	}

	public static function getItems()
	{
		$items = Configure::read('Croogo.Blocks.cells');
		if (!is_array($items)) {
			return [];
		}

		foreach ($items as $identifier => $config) {
			unset($items[$identifier]);

			$config['regions'] = static::regions(static::fromConfigIdentifier($identifier));

			$items[static::fromConfigIdentifier($identifier)] = (object) $config;
		}

		return $items;
	}

	protected static function config($cell, array $config = null)
	{
		$key = 'Croogo.Blocks.cells.' . static::toConfigIdentifier($cell);

		if (is_null($config)) {
			return Configure::read($key);
		}

		if (is_array(Configure::read($key))) {
			$existingConfig = Configure::read($key);
		} else {
			$existingConfig = [
				'title' => '',
				'className' => $cell,
				'description' => '',
				'regions' => [],
			];
		}
		$config = Hash::merge($existingConfig, $config);

		Configure::write($key, $config);
	}

	protected static function regionConfig($cell, $region, $originalRegion = null)
	{
		$config = static::config($cell);

		if (isset($config['regionAliases'][$region])) {
			if (is_null($originalRegion)) {
				$originalRegion = $region;
			}

			return static::regionConfig($cell, $config['regionAliases'][$region], $originalRegion);
		}

		$regionConfig = (isset($config['regions'][$region])) ? $config['regions'][$region] : [];
		$regionConfig['original'] = $originalRegion;
		$regionConfig['method'] = $region;

		return $regionConfig;
	}

	protected static function toConfigIdentifier($cell)
	{
		return implode(static::PLUGIN_SEPARATOR, explode('.', $cell));
	}

	protected static function fromConfigIdentifier($identifier)
	{
		return implode('.', explode(static::PLUGIN_SEPARATOR, $identifier));
	}

}
