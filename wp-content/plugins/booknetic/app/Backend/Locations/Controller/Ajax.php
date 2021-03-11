<?php

namespace BookneticApp\Backend\Locations\Controller;

use BookneticApp\Backend\Locations\Model\Location;
use BookneticApp\Providers\Backend;
use BookneticApp\Providers\DB;
use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Permission;

class Ajax extends \BookneticApp\Providers\Ajax
{

	public function add_new()
	{
		$lid = Helper::_post('id', '0', 'integer');

		if( $lid > 0 )
		{
			$locationInfo = Location::get( $lid );
		}
		else
		{
			if( Helper::isSaaSVersion() && ( $permissionAlert = Permission::tenantInf()->checkPermission('locations_count') ) !== true )
			{
				return $this->modalView('Base*permission_denied', [
					'text'  => $permissionAlert
				]);
			}

			$locationInfo = [
				'id'                =>  null,
				'name'              =>  null,
				'image'             =>  null,
				'address'           =>  null,
				'phone_number'      =>  null,
				'notes'             =>  null,
				'latitude'          =>  null,
				'longitude'         =>  null,
				'is_active'         =>  null
			];
		}

		$this->modalView('add_new', ['location' => $locationInfo]);
	}

	public function save_location()
	{
		$id				=	Helper::_post('id', '0', 'integer');
		$location_name	=	Helper::_post('location_name', '', 'string');
		$address		=	Helper::_post('address', '', 'string');
		$phone			=	Helper::_post('phone', '', 'string');
		$note			=	Helper::_post('note', '', 'string');
		$latitude		=	Helper::_post('latitude', '', 'string');
		$longitude		=	Helper::_post('longitude', '', 'string');

		if( !( $id > 0 ) && Helper::isSaaSVersion() && ( $permissionAlert = Permission::tenantInf()->checkPermission('locations_count') ) !== true )
		{
			Helper::response( false, $permissionAlert );
		}

		if( empty($location_name) )
		{
			Helper::response(false, bkntc__('Please fill in all required fields correctly!'));
		}

		$image = '';

		if( isset($_FILES['image']) && is_string($_FILES['image']['tmp_name']) )
		{
			$path_info = pathinfo($_FILES["image"]["name"]);
			$extension = strtolower( $path_info['extension'] );

			if( !in_array( $extension, ['jpg', 'jpeg', 'png'] ) )
			{
				Helper::response(false, bkntc__('Only JPG and PNG images allowed!'));
			}

			$image = md5( base64_encode(rand(1,9999999) . microtime(true)) ) . '.' . $extension;
			$file_name = Helper::uploadedFile( $image, 'Locations' );

			move_uploaded_file( $_FILES['image']['tmp_name'], $file_name );
		}

		$sqlData = [
			'name'			=>	$location_name,
			'address'		=>	$address,
			'phone_number'	=>	$phone,
			'notes'			=>	$note,
			'image'			=>	$image,
			'latitude'		=>	$latitude,
			'longitude'		=>	$longitude
		];

		if( $id > 0 )
		{
			if( empty( $image ) )
			{
				unset( $sqlData['image'] );
			}
			else
			{
				$getOldInf = Location::get( $id );

				if( !empty( $getOldInf['image'] ) )
				{
					$filePath = Helper::uploadedFile( $getOldInf['image'], 'Locations' );

					if( is_file( $filePath ) && is_writable( $filePath ) )
					{
						unlink( $filePath );
					}
				}
			}

			Location::where( 'id', $id )->update( $sqlData );
		}
		else
		{
			$sqlData['is_active'] = 1;
			Location::insert( $sqlData );
		}

		Helper::response(true );
	}

	public function hide_location()
	{
		$location_id	= Helper::_post('location_id', '', 'int');

		if( !( $location_id > 0 ) )
		{
			Helper::response(false);
		}

		$location = Location::get( $location_id );

		if( !$location )
		{
			Helper::response( false );
		}

		$new_status = $location['is_active'] == 1 ? 0 : 1;

		Location::where('id', $location_id)->update(['is_active' => $new_status]);

		Helper::response( true );
	}

}
