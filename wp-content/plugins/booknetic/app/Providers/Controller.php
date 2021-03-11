<?php

namespace BookneticApp\Providers;

class Controller
{

	public function __construct()
	{
		if( !$this->checkPermission() )
		{
			print bkntc__('Permission denied!');
			exit();
		}
	}

	public function view( $name, $parameters = [], $extends = 'index' )
	{
		// get current module name...
		$moduleNamespace = get_called_class();
		$moduleNamespace = explode('\\' , $moduleNamespace );

		if( !( $moduleNamespace[0] == 'BookneticApp' && isset( $moduleNamespace[2] ) ) )
		{
			exit();
		}

		$moduleName	= ucfirst( $moduleNamespace[2] );
		$viewsPath	= Backend::MODULES_DIR . $moduleName . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;

		// check if called view exists
		if( !file_exists( $viewsPath . str_replace('.', DIRECTORY_SEPARATOR, $name) . '.php' ) )
		{
			print htmlspecialchars( $name ) . ' - view not exists!';
			exit;
		}

		$childViewFile = $viewsPath . $name . '.php';

		require_once Backend::MODULES_DIR . 'Base' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $extends . '.php';
	}

	private function checkPermission()
	{
		$class = get_called_class();
		preg_match( '/BookneticApp\\\\Backend\\\\(.+)\\\\/iU', $class, $module );
		$module = $module[1];

		return Permission::canAccessTo( $module );
	}

}
