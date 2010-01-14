<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: jvAMF - AMF Connector for eZ Publish
// SOFTWARE RELEASE: @@@VERSION@@@
// COPYRIGHT NOTICE: Copyright (C) 2009 Jerome Vieilledent
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/**
 * Handle the AMF service call
 */

eZDebug::createAccumulator('Zend_AMF Configuration', 'jvAMF');
eZDebug::accumulatorStart('Zend_AMF Configuration');

// Zend lib must be in the include path to work
set_include_path('extension/jvamf/lib'.PATH_SEPARATOR.get_include_path());

$ini = eZINI::instance('jvamf.ini');
$aServicesDirs = $ini->variable('Services', 'ServicesDir');
$aServicesClasses = $ini->variable('Services', 'ServicesClasses');
$aClassMapping = $ini->variable('AMF', 'ClassMap');
$productionMode = $ini->variable('AMF', 'ProductionMode') == 'enabled' ? true : false;

$server = new Zend_Amf_Server();
$server->setProduction($productionMode);

// services directories
foreach($aServicesDirs as $dir)
{
	$server->addDirectory($dir);
}

// Services classes
foreach($aServicesClasses as $class)
{
	$server->setClass($class);
}

// ClassMapping
foreach($aClassMapping as $asClass => $phpClass)
{
	$server->setClassMap($asClass, $phpClass);
}

// Do we allow Service explorer ?
$allowServiceExplorer = $ini->variable('Services', 'AllowServiceExplorer') == 'true';
if($allowServiceExplorer)
{
	// *ZAMFBROWSER IMPLEMENTATION*
	// Add the ZendAmfServiceBrowser class to the list of available classes.
	$server->setClass('ZendAmfServiceBrowser');
	// Set this reference the class requires to the server object.
	ZendAmfServiceBrowser::$ZEND_AMF_SERVER = $server;
}

eZDebug::accumulatorStop('Zend_AMF Configuration');

$AMFresult = $server->handle();
echo $AMFresult;

// Clean exit to avoid debug to display
eZDB::checkTransactionCounter();
eZExecution::cleanExit();
