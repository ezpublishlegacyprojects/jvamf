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

class eZContentAMFService extends JVAMFService
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function fetchContentNode(stdClass $params)
	{
		$aParams = (array)$params;
		$result = eZFunctionHandler::execute('content', 'node', $aParams);
		if(!$result)
			throw new JVAMFNoResultException('[fetch content/node] No result has been found with params '.json_encode($params));
		
		$aParamsSimplify = array(
			'dataMap'			=> array('all'),
			'dataMapType'		=> array('ezstring'),
			'imageDataTypes'	=> array('ezimage'),
			'loadImages'		=> true,
			'imagePreGenerateSizes'	=> array('small', 'medium', 'large')
		);
		$resultSimplified = JVAMFContentHandler::simplify($result, $aParamsSimplify);
		
		$nodeVO = new eZNodeVO();
		$nodeVO->fromArray($resultSimplified);
		
		return $nodeVO;
	}
}