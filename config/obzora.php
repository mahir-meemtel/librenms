<?php
 return [

     /*
     |--------------------------------------------------------------------------
     | User
     |--------------------------------------------------------------------------
     |
     | This value is the user ObzoraNMS runs as. It is used to secure permissions
     | and grant access to things needed. Defaults to obzora.
     */

     'user' => env('OBZORA_USER', 'obzora'),

     /*
     |--------------------------------------------------------------------------
     | Group
     |--------------------------------------------------------------------------
     |
     | This value is the group ObzoraNMS runs as. It is used to secure permissions
     | and grant access to things needed. Defaults to the same as OBZORA_USER.
     */

     'group' => env('OBZORA_GROUP', env('OBZORA_USER', 'obzora')),

     /*
     |--------------------------------------------------------------------------
     | Install
     |--------------------------------------------------------------------------
     |
     | This value sets if the install process needs to be run.
     | You may also specify which install steps to present with a comma separated list.
     */

     'install' => env('INSTALL', false),

     /*
     |--------------------------------------------------------------------------
     | NODE ID
     |--------------------------------------------------------------------------
     |
     | Unique value to identify this node. Primarily used for distributed polling.
     */

     'node_id' => env('NODE_ID'),

     /*
     |--------------------------------------------------------------------------
     | Config Cache TTL
     |--------------------------------------------------------------------------
     |
     | Amount of seconds to allow the config to be cached.  0 means no cache.
     */

     'config_cache_ttl' => env('CONFIG_CACHE_TTL', 300),
 ];
