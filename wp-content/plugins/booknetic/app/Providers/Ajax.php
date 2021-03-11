<?php

namespace BookneticApp\Providers;

class Ajax
{

	public function __construct()
	{
		if( !$this->checkPermission() )
		{
			Helper::response(false, bkntc__('Permission denied!'));
		}
	}

	public function modalView( $name, $parameters = [] )
	{
		$_mn = Helper::_post('_mn', '0', 'integer');

		if( strpos( $name, '*' ) !== false )
		{
			$name = explode( '*', $name );
			$moduleName = $name[0];
			$name = $name[1];
		}
		else
		{
			// get current module name...
			$moduleNamespace = get_called_class();
			$moduleNamespace = explode('\\' , $moduleNamespace );

			if( !( $moduleNamespace[0] == 'BookneticApp' && isset( $moduleNamespace[1] ) ) )
			{
				exit();
			}

			$moduleName	= ucfirst( $moduleNamespace[2] );
		}

		$viewsPath	= Backend::MODULES_DIR . $moduleName . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'modal' . DIRECTORY_SEPARATOR;

		// check if called view exists
		if( !file_exists( $viewsPath . str_replace('.', DIRECTORY_SEPARATOR, $name) . '.php' ) )
		{
			Helper::response(false, htmlspecialchars( $name ) . ' - view not exists!');
		}

		ob_start();
		require $viewsPath . $name . '.php';
		$viewOutput = ob_get_clean();

		Helper::response(true, [
			'html' => htmlspecialchars( $viewOutput )
		]);
	}

	private function checkPermission()
	{
		$class = get_called_class();
		preg_match( '/BookneticApp\\\\Backend\\\(.+)\\\\/iU', $class, $module );
		$module = $module[1];

		return Permission::canAccessTo( $module );
	}

}
