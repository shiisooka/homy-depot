<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inversion of control for plugin.
 *
 * @link       http://www.asanaplugins.com/
 * @since      1.0.0
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_IOC {

	private static $container = array();

	/**
	 * Binding value to key in container.
	 *
	 * @since 1.0.0
	 * @param  string $key
	 * @param  mixed $value
	 * @return mixed
	 */
	public static function bind( $key, $value ) {
		return self::$container[ $key ] = $value;
	}

	/**
	 * Getting value of key from container.
	 *
	 * @since 1.0.0
	 * @param  string $key
	 * @return mixed
	 */
	public static function make( $key ) {
		try {
			if ( isset( self::$container[ $key ] ) ) {
				return self::$container[ $key ];
			}
			throw new Exception( "Object {$key} not found exception" );
		} catch( Exception $e ) {
			die( $e->getMessage() );
		}
	}

}
