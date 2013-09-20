<?php //-->
/*
 * This file is part a custom application package.
 */

/**
 * The base class for any class that defines a view.
 * A view controls how templates are loaded as well as 
 * being the final point where data manipulation can occur.
 *
 * @package    Eden
 */
require dirname(__FILE__).'/../app.php';

/* Get Application
-------------------------------*/
print app()

/* Set Debug
-------------------------------*/
//->setDebug(0, false)
->setDebug(E_ALL, true)

/* Set Autoload
-------------------------------*/
->setLoader(NULL, '/model')

/* Set Autoload
-------------------------------*/
->setLoader(NULL, '/tool')

/* Set Autoload
-------------------------------*/
->setLoader(NULL, '../library')

/* Set Paths
-------------------------------*/
->setTimezone('Asia/Manila')

->setPaths()

->routeClasses(true)

->routeMethods(true)

/* Start Filters
-------------------------------*/
->setFilters(array('App_Handler', 'Email_Handler'))

/* Trigger Init Event
-------------------------------*/
->trigger('init')

/* Set Database
-------------------------------*/
->addDatabase(include(dirname(__FILE__).'/../../config/database.php'))

/* Trigger Init Event
-------------------------------*/
->trigger('config')

/* Start Session
-------------------------------*/
->startSession()

/* Trigger Session Event
-------------------------------*/
->trigger('session')

->setPages(array())

/* Set Request
-------------------------------*/
->setRequest()

/* Trigger Request Event
-------------------------------*/
->trigger('request')

/* Set Response
-------------------------------*/
->setResponse('App_Page_Index')

/* Trigger Response Event
-------------------------------*/
->trigger('response')

/* Get the Response
-------------------------------*/

->getResponse();
