<?php

namespace BookneticApp\Providers;

class Middleware
{

	public function __construct()
	{
		if( !$this->checkPermission() )
			return false;

		if( method_exists( $this, 'boot' ) )
		{
			$this->boot();
		}
	}

	public static function handle()
	{
		return true;
	}

	public function createMenu( $menuName = null )
	{
		if( !$this->checkPermission() )
			return false;

		// get current module name...
		$moduleNamespace = get_called_class();
		$moduleNamespace = explode('\\' , $moduleNamespace );

		if( !( $moduleNamespace[0] == 'BookneticApp' && isset( $moduleNamespace[1] ) ) )
		{
			exit();
		}

		$moduleName	= $moduleNamespace[2];

		$menu = new Menu( $menuName );
		$menu->setModule( $moduleName );

		return $menu;
	}

	public function createParentMenu ( $menuName = null )
	{
		if( !$this->checkPermission() )
			return false;

		return new Menu( $menuName );
	}

	private function checkPermission()
	{
		return static::handle();
	}

}