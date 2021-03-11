<?php

namespace BookneticApp\Backend\Customers\Controller;

use BookneticApp\Backend\Customers\Model\Customer;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\Date;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function add_new()
	{
		$cid = Helper::_post('id', '0', 'integer');

		if( $cid > 0 )
		{
			$customerInfo = Customer::where('id', $cid)->fetch();
		}
		else
		{
			$customerInfo = [
				'id'                =>  null,
				'user_id'           =>  null,
				'first_name'        =>  null,
				'last_name'         =>  null,
				'phone_number'      =>  null,
				'email'             =>  null,
				'birthdate'         =>  null,
				'notes'             =>  null,
				'profile_image'     =>  null,
				'gender'            =>  null
			];
		}

		$emailIsRequired = Helper::getOption('set_email_as_required', 'on');
		$phoneIsRequired = Helper::getOption('set_phone_as_required', 'off');

		$users = DB::DB()->get_results('SELECT * FROM `'.DB::DB()->base_prefix.'users`', ARRAY_A);

		$this->modalView('add_new', [
			'customer'			=> $customerInfo,
			'email_is_required'	=> $emailIsRequired,
			'phone_is_required'	=> $phoneIsRequired,
			'users'             => $users,

			'show_only_name'    => Helper::getOption('separate_first_and_last_name', 'on') == 'off'
		]);
	}

	public function import()
	{
		$this->modalView('import' );
	}

	public function save_customer()
	{
		$id			                = Helper::_post('id', '0', 'integer');
		$wp_user	                = Helper::_post('wp_user', '0', 'integer');
		$first_name	                = Helper::_post('first_name', '', 'string');
		$last_name	                = Helper::_post('last_name', '', 'string');
		$gender		                = Helper::_post('gender', 'male', 'string', ['male', 'female']);
		$birthday	                = Helper::_post('birthday', '', 'string');
		$phone		                = Helper::_post('phone', '', 'string');
		$email		                = Helper::_post('email', '', 'email');
		$allow_customer_to_login	= Helper::_post('allow_customer_to_login', '0', 'int', ['0', '1']);
		$wp_user_use_existing	    = Helper::_post('wp_user_use_existing', 'yes', 'string', ['yes', 'no']);
		$wp_user_password		    = Helper::_post('wp_user_password', '', 'string');
		$note		                = Helper::_post('note', '', 'string');

		$show_only_name = Helper::getOption('separate_first_and_last_name', 'on') == 'off';

		if( empty($first_name) || (empty($last_name) && !$show_only_name) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$isEdit = $id > 0;

		if( $isEdit )
		{
			$getOldInf = Customer::get( $id );
			if( !$getOldInf )
			{
				Helper::response(false, bkntc__('Customer not found or permission denied!'));
			}
		}

		$emailIsRequired = Helper::getOption('set_email_as_required', 'on');
		$phoneIsRequired = Helper::getOption('set_phone_as_required', 'off');

		if( $emailIsRequired == 'on' && empty( $email ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		if( $phoneIsRequired == 'on' && empty( $phone ) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		if( !Permission::isAdministrator() )
		{
			$wp_user = $isEdit ? $getOldInf->user_id : 0;
		}
		else if( $allow_customer_to_login == 1 )
		{
			if( $wp_user_use_existing == 'yes' && !( $wp_user > 0 ) )
			{
				Helper::response( false, bkntc__('Please select WordPress user!') );
			}
			else if( $wp_user_use_existing == 'no' )
			{
				if( !($isEdit && $getOldInf->user_id > 0) && empty( $wp_user_password ) )
				{
					Helper::response( false, bkntc__('Please type the password of the WordPress user!') );
				}
				else if( (!$isEdit || $email != $getOldInf->email) && (email_exists( $email ) !== false || username_exists( $email ) !== false) )
				{
					Helper::response( false, bkntc__('The WordPress user with the same email address already exists!') );
				}

				if( $isEdit && $getOldInf->user_id > 0 )
				{
					$wp_user = $getOldInf->user_id;
					$updateData = [];

					if( $email != $getOldInf->email )
					{
						$updateData['user_login'] = $email;
						$updateData['user_email'] = $email;
					}

					if( $first_name != $getOldInf->first_name || $last_name != $getOldInf->last_name )
					{
						$updateData['display_name'] = trim( $first_name . ' ' . $last_name );
						$updateData['first_name'] = $first_name;
						$updateData['last_name'] = $last_name;
					}

					if( !empty( $wp_user_password ) )
					{
						$updateData['user_pass'] = $wp_user_password;
					}

					if( !empty( $updateData ) )
					{
						$updateData['ID'] = $getOldInf->user_id;
						$user_data = wp_update_user( $updateData );

						if( isset( $updateData['user_login'] ) )
						{
							DB::DB()->update( DB::DB()->users, ['user_login' => $email], ['ID' => $updateData['ID']] );
						}

						if( is_wp_error( $user_data ) )
						{
							Helper::response( false, $user_data->get_error_message() );
						}
					}
				}
				else
				{
					$wp_user = wp_insert_user( [
						'user_login'	=>	$email,
						'user_email'	=>	$email,
						'display_name'	=>	trim( $first_name . ' ' . $last_name ),
						'first_name'	=>	$first_name,
						'last_name'		=>	$last_name,
						'role'			=>	'booknetic_customer',
						'user_pass'		=>	$wp_user_password
					] );

					if( is_wp_error( $wp_user ) )
					{
						Helper::response( false, $wp_user->get_error_message() );
					}
				}
			}
		}
		else
		{
			if( $isEdit && $getOldInf->user_id > 0 )
			{
				$userData = get_userdata( $getOldInf->user_id );
				if( $userData && in_array( 'booknetic_customer', $userData->roles ) )
				{
					require_once ABSPATH.'wp-admin/includes/user.php';
					wp_delete_user( $getOldInf->user_id );
				}
			}

			$wp_user = 0;
		}

		$profile_image = '';

		if( isset($_FILES['image']) && is_string($_FILES['image']['tmp_name']) )
		{
			$path_info = pathinfo($_FILES["image"]["name"]);
			$extension = strtolower( $path_info['extension'] );

			if( !in_array( $extension, ['jpg', 'jpeg', 'png'] ) )
			{
				Helper::response(false, bkntc__('Only JPG and PNG images allowed!'));
			}

			$profile_image = md5( base64_encode(rand(1,9999999) . microtime(true)) ) . '.' . $extension;
			$file_name = Helper::uploadedFile( $profile_image , 'Customers' );

			move_uploaded_file( $_FILES['image']['tmp_name'], $file_name );
		}

		$sqlData = [
			'user_id'		=>	$wp_user > 0 ? $wp_user : 0,
			'first_name'	=>	trim($first_name),
			'last_name'		=>	trim($last_name),
			'phone_number'	=>	$phone,
			'email'			=>	$email,
			'birthdate'		=>	Date::dateSQL( $birthday ),
			'notes'			=>	$note,
			'profile_image'	=>	$profile_image,
			'gender'		=>	$gender
		];

		if( $isEdit )
		{
			if( empty( $profile_image ) )
			{
				unset( $sqlData['profile_image'] );
			}
			else
			{
				if( !empty( $getOldInf['profile_image'] ) )
				{
					$filePath = Helper::uploadedFile( $getOldInf['profile_image'] , 'Customers' );

					if( is_file( $filePath ) && is_writable( $filePath ) )
					{
						unlink( $filePath );
					}
				}
			}

			Customer::where('id', $id)->update( $sqlData );
		}
		else
		{
			Customer::insert( $sqlData );
		}

		Helper::response(true );
	}

	public function import_customers()
	{
		$delimiter	= Helper::_post('delimiter', ';', 'string', [';', ',']);
		$fields		= Helper::_post('fields', '', 'string');

		$fields1 = [];

		foreach( explode(',', $fields) AS $fieldName )
		{
			if( in_array( $fieldName, ['first_name', 'last_name', 'email', 'phone_number', 'gender', 'birthdate', 'notes'] ) )
			{
				$fields1[] = $fieldName;
			}
		}

		if( empty($fields1) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$fieldsCount = count($fields1);

		if( !( isset($_FILES['csv']) && is_string($_FILES['csv']['tmp_name']) ) )
		{
			Helper::response(false, bkntc__('Please select CSV file!'));
		}

		$csvFile = $_FILES['csv']['tmp_name'];

		$csvArray = [];

		$file = fopen($csvFile, 'r');
		while ( ($result = fgetcsv($file, 0, $delimiter)) !== false)
		{
			if( count( $result ) > $fieldsCount )
			{
				Helper::response(false, bkntc__('Too many fields detected on CSV file!'));
			}

			$csvArray[] = $result;
		}
		fclose($file);
		unset($file);

		foreach( $csvArray AS $rows )
		{
			$insertData = [];

			foreach ($rows as $fieldNum => $data)
			{
				$fieldName = $fields1[$fieldNum];

				$insertData[ $fieldName ] = $data;
			}

			// check if email is correct...
			if( isset( $insertData[ 'email' ] ) && !empty( $insertData[ 'email' ] ) && !filter_var( $insertData[ 'email' ], FILTER_VALIDATE_EMAIL ) )
			{
				continue;
			}

			Customer::insert( $insertData );
		}

		Helper::response(true );
	}

}
