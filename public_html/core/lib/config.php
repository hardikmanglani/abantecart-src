<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011, 2012 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

final class AConfig {
	private $cnfg = array();
	private $registry;
	public $groups = array( 'details', 'general', 'checkout', 'appearance', 'mail', 'api', 'system' );

	public function __construct($registry) {
		$this->registry = $registry;
		$this->_load_settings();
	}

	/**
	 * get data from config
	 *
	 * @param $key - data key
	 * @return requested data; null if no data wit such key
	 */
	public function get($key) {
		return (isset($this->cnfg[ $key ]) ? $this->cnfg[ $key ] : NULL);
	}

	/**
	 * add data to config.
	 *
	 * @param $key - access key
	 * @param $value - data to store in config
	 * @return void
	 */
	public function set($key, $value) {
		$this->cnfg[ $key ] = $value;
	}

	/**
	 * check if data exist in config
	 *
	 * @param $key
	 * @return bool
	 */
	public function has($key) {
		return isset($this->cnfg[ $key ]);
	}

	/**
	 * load data from config and merge with current data set
	 *
	 * @throws AException
	 * @param $filename
	 * @return void
	 */
	public function load($filename) {
		$file = DIR_CONFIG . $filename . '.php';

		if (file_exists($file)) {
			$cfg = array();

			require($file);

			$this->data = array_merge($this->data, $cfg);
		} else {
			throw new AException(AC_ERR_LOAD, 'Error: Could not load config ' . $filename . '!');
		}
	}

	private function _load_settings() {
		$cache = $this->registry->get('cache');
		$db = $this->registry->get('db');

		// Load default store settings first
		$settings = $cache->force_get('settings', '', 0);
		if (empty($settings)) {
			// set global settings (without extensions settings)
			$sql = "SELECT se.*
					FROM " . DB_PREFIX . "settings se
					LEFT JOIN " . DB_PREFIX . "extensions e ON TRIM(se.`group`) = TRIM(e.`key`)
					WHERE se.store_id='0' AND e.extension_id IS NULL";
			$query = $db->query($sql);
			$settings = $query->rows;
			$cache->force_set('settings', $settings, '', 0);
		}

		foreach ($settings as $setting) {
			$this->cnfg[ $setting[ 'key' ] ] = $setting[ 'value' ];
		}

		//detect URL for the store
		$url = str_replace('www.', '', $_SERVER[ 'HTTP_HOST' ]) . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/';
		if (defined('INSTALL')) {
			$url = str_replace('install/', '', $url);
		}
		// if storefront and not default store
		// try to load setting for given url
		if (!($this->cnfg[ 'config_url' ] == 'http://' . $url || $this->cnfg[ 'config_url' ] == 'http://www.' . $url)) {
			$cache_name = 'settings.store.' . md5('http://' . $url);
			$store_settings = $cache->force_get($cache_name);
			if (empty($store_settings)) {
				$sql = "SELECT se.`key`, se.`value`, st.store_id
		   			  FROM " . DB_PREFIX . "settings se
		   			  RIGHT JOIN " . DB_PREFIX . "stores st ON se.store_id=st.store_id
		   			  LEFT JOIN " . DB_PREFIX . "extensions e ON TRIM(se.`group`) = TRIM(e.`key`)
		   			  WHERE se.store_id = (SELECT DISTINCT store_id FROM " . DB_PREFIX . "settings
		   			                       WHERE `group`='details' AND `key` = 'config_url'
		                                         AND (`value` = '" . $db->escape('http://www.' . $url) . "'
		                                               OR `value` = '" . $db->escape('http://' . $url) . "')
		                                   LIMIT 0,1)
		   					AND st.status = 1 AND e.extension_id IS NULL";

				$query = $db->query($sql);
				$store_settings = $query->rows;
				$cache->force_set($cache_name, $store_settings);
			}

			if ($store_settings) {
				//if(!IS_ADMIN){
				foreach ($store_settings as $row) {
					$this->cnfg[ $row[ 'key' ] ] = $row[ 'value' ];
				}
				//}
				$this->cnfg[ 'config_store_id' ] = $store_settings[ 0 ][ 'store_id' ];
			} else {
				$warning = new AWarning('Warning: Accessing store with unconfigured or unknown domain. Check setting of your store domain URL in System Settings . Loading default store configuration for now.');
				$warning->toLog()->toMessages();
				//set config url to current domain
				$this->cnfg[ 'config_url' ] = 'http://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/';
			}

			if (!$this->cnfg[ 'config_url' ]) {
				$this->cnfg[ 'config_url' ] = 'http://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/';
			}

		}

		if (is_null($this->cnfg[ 'config_store_id' ])) {
			$this->cnfg[ 'config_store_id' ] = 0;
			$this->_set_default_settings();
		}

		// load extension settings
		$cache_suffix = IS_ADMIN ? 'admin' : $this->cnfg[ 'config_store_id' ];
		$settings = $cache->force_get('settings.extension.' . $cache_suffix);
		if (empty($settings)) {
			// all settings of default store without extensions settings
			$sql = "SELECT se.*
					FROM " . DB_PREFIX . "settings se
					LEFT JOIN " . DB_PREFIX . "extensions e ON TRIM(se.`group`) = TRIM(e.`key`)
					WHERE se.store_id='" . (int)$this->cnfg[ 'config_store_id' ] . "' AND e.extension_id IS NOT NULL
					ORDER BY se.store_id ASC, se.group ASC";

			$query = $db->query($sql);
			$settings = $query->rows;
			$cache->force_set('settings.extension.' . $cache_suffix, $settings);
		}

		foreach ($settings as $setting) {
			$this->cnfg[ $setting[ 'key' ] ] = $setting[ 'value' ];
		}
	}

	private function _set_default_settings() {
		//we don't use cache here cause domain may by any and we can't to change cache from control panel
		$db = $this->registry->get('db');
		$sql = "SELECT se.`key`, se.`value`, st.store_id
					  FROM " . DB_PREFIX . "settings se
					  RIGHT JOIN " . DB_PREFIX . "stores st ON se.store_id=st.store_id
					  LEFT JOIN " . DB_PREFIX . "extensions e ON TRIM(se.`group`) = TRIM(e.`key`)
					  WHERE se.store_id = 0 AND st.status = 1 AND e.extension_id IS NULL";

		$query = $db->query($sql);
		$store_settings = $query->rows;
		foreach ($store_settings as $row) {
			if ($row[ 'key' ] != 'config_url') {
				$this->cnfg[ $row[ 'key' ] ] = $row[ 'value' ];
			}
		}
	}
}

?>