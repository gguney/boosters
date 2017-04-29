<?php
return [
	/*
    |--------------------------------------------------------------------------
    | Admin Route Prefix
    |--------------------------------------------------------------------------
    | 
    | This is a prefix for admin dashboard. For example 'admin' will be the prefix for routes like /admin/users
    | 
    */
	'prefix' => '',

    /*
    |--------------------------------------------------------------------------
    | Image Properties
    |--------------------------------------------------------------------------
    | 
    | Properties about image storing.
    | 
    */
    'disk' => 's3',
    'photos_sub_path' => 'photos',
    'documents_sub_path' => 'documents',

	/*
    |--------------------------------------------------------------------------
    | Partial Views
    |--------------------------------------------------------------------------
    | 
    | Table partial is for index action.
    | Form partial is for create and edit actions.
    | Show partial is for show action.
    | 
    */

    'table_component_path' => 'vendor.boosters.components.table',
    'form_component_path' => 'vendor.boosters.components.form',
    'show_component_path' => 'vendor.boosters.components.show',

];