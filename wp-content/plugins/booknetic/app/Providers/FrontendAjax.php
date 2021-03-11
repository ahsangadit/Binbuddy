<?php

namespace BookneticApp\Providers;

class FrontendAjax
{

	protected static function view( $name, $parameters = [], $response_data = [] )
	{
		$viewsPath	= Frontend::VIEW_DIR . str_replace('.', DIRECTORY_SEPARATOR, basename( $name )) . '.php';

		// check if called view exists
		if( !file_exists( $viewsPath ) )
		{
			Helper::response(false, htmlspecialchars( $name ) . ' - view not exists!');
		}

		ob_start();
		require $viewsPath;
		$viewOutput = ob_get_clean();

		$response_data['html'] = htmlspecialchars( $viewOutput );

		Helper::response(true, $response_data);
	}

}
