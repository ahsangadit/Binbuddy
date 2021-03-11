<?php

namespace BookneticApp\Providers;

class Menu
{

	const MENU_TYPE_LEFT    = 1;
	const MENU_TYPE_TOP     = 2;

	private $name;
	private $link;
	private $icon;
	private $badge;
	private $order;
	private $module;
	private $parent;
	private $parent_id;
	private $type = Menu::MENU_TYPE_LEFT;


	public function __construct( $name )
	{
		if( !is_string( $name ) )
			$name = '';

		$this->name = $name;
	}

	public function setModule( $module )
	{
		$this->module = strtolower( $module );

		$this->link = 'admin.php?page=' . Backend::MENU_SLUG . '&module=' . $this->module;

		return $this;
	}

	public function setType( $type )
	{
		$this->type = $type;

		return $this;
	}

	public function setLink( $link )
	{
		if( !is_string( $link ) )
			$link = '';

		$this->link = $link;

		return $this;
	}

	public function setIcon( $icon )
	{
		if( !is_string( $icon ) )
			$icon = '';

		$this->icon = $icon;

		return $this;
	}

	public function setParent( $id, $icon, $name )
	{
		$id = ! is_string( $id ) ? '' : $id;
		$icon = ! is_string( $icon ) ? '' : $icon;
		$name = ! is_string( $name ) ? '' : $name;

		$this->parent = [
			'id' => $id,
			'icon' => $icon,
			'name' => $name
		];

		$this->setParentID( $id );

		return $this;
	}

	public function setParentID ( $parent_id )
	{
		$parent_id = ! is_string( $parent_id ) ? '' : $parent_id;

		$this->parent_id = $parent_id;

		return $this;
	}

	public function setBadge( $badge )
	{
		if( !is_string( $badge ) )
			$badge = '';

		$this->badge = $badge;

		return $this;
	}

	public function setOrder( $order )
	{
		if( !is_numeric( $order ) )
			$order = 1;

		$this->order = $order;

		return $this;
	}

	public function show()
	{
		Backend::addMenu( $this );

		return true;
	}


	public function getName()
	{
		return $this->name;
	}

	public function getLink()
	{
		return $this->link;
	}

	public function getIcon()
	{
		return $this->icon;
	}

	public function getParent( $key )
	{
		if ( ! isset( $this->parent ) || ! array_key_exists( $key, $this->parent ) )
			return '';

		return $this->parent[ $key ];
	}

	public function getParentID()
	{
		return $this->parent_id;
	}

	public function getBadge()
	{
		return $this->badge;
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function getType()
	{
		return $this->type;
	}

	public function isActive()
	{
		return ucfirst($this->module) == Backend::$currentModule ? true : false;
	}

	public function hasParent()
	{
		static $parentIDs;

		if( isset( $this->parent ) && !isset( $parentIDs[ $this->parent_id ] ) )
		{
			$parentIDs[ $this->parent_id ] = true;

			return true;
		}

		return false;
	}

	public function hasParentID()
	{
		return isset( $this->parent_id );
	}

}
