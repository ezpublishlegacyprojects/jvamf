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

class JVAMFContentHandler
{
	/**
	 * Function for simplifying a content object or node
	 * Taken from ezjscore ezjscAjaxContent class. Improved by implementing content attribute handlers
	 *
	 * @param mixed $obj
	 * @param array $params
	 * @return array
	 */
	public static function simplify( $obj, array $params = array() )
	{
		if ( !$obj )
		{
			return array();
		}
		else if ( $obj instanceof eZContentObject)
		{
			$node          = $obj->attribute( 'main_node' );
			$contentObject = $obj;
		}
		else if ( $obj instanceof eZContentObjectTreeNode || $obj instanceof eZFindResultNode )
		{
			$node          = $obj;
			$contentObject = $obj->attribute( 'object' );
		}
		else if( isset( $params['fetchNodeFunction'] ) && method_exists( $obj, $params['fetchNodeFunction'] ) )
		{
			// You can supply fetchNodeFunction parameter to be able to support other node related classes
			$node = call_user_func( array( $obj, $params['fetchNodeFunction'] ) );
			if ( !$node instanceof eZContentObjectTreeNode )
			{
				return '';
			}
			$contentObject = $node->attribute( 'object' );
		}
		else if ( is_array( $obj ) )
		{
			return $obj; // Array is returned as is
		}
		else
		{
			return ''; // Other passed objects are not supported
		}

		$ini = eZINI::instance( 'site.ini' );
		$jvAMFINI = eZINI::instance('jvamf.ini');
		$params = array_merge( array(
                            'dataMap' => array(), // collection of identifiers you want to load, load all with array('all')
                            'fetchPath' => false, // fetch node path
                            'fetchChildrenCount' => false,
                            'dataMapType' => array(), //if you want to filter datamap by type
                            'loadImages' => false,
                            'imagePreGenerateSizes' => array('small') //Pre generated images, loading all can be quite time consuming
		), $params );

		if ( !isset( $params['imageSizes'] ) )// list of available image sizes
		{
			$imageIni = eZINI::instance( 'image.ini' );
			$params['imageSizes'] = $imageIni->variable( 'AliasSettings', 'AliasList' );
		}

		if ( $params['imageSizes'] === null || !isset( $params['imageSizes'][0] ) )
		$params['imageSizes'] = array();

		if (  !isset( $params['imageDataTypes'] ) )
		$params['imageDataTypes'] = $ini->variable( 'ImageDataTypeSettings', 'AvailableImageDataTypes' );

		$ret                     = array();
		$attrtibuteArray         = array();
		$ret['name']             = $contentObject->attribute( 'name' );
		$ret['contentobject_id'] = $ret['id'] = (int) $contentObject->attribute( 'id' );
		$ret['main_node_id']     = (int)$contentObject->attribute( 'main_node_id' );
		$ret['modified']         = $contentObject->attribute( 'modified' );
		$ret['published']        = $contentObject->attribute( 'published' );
		$ret['section_id']       = (int) $contentObject->attribute( 'section_id' );
		$ret['current_language'] = $contentObject->attribute( 'current_language' );
		$ret['owner_id']         = (int) $contentObject->attribute( 'owner_id' );
		$ret['class_id']         = (int) $contentObject->attribute( 'contentclass_id' );
		$ret['class_name']       = $contentObject->attribute( 'class_name' );

		if ( $node )
		{
			// optimization for eZ Publish 4.1 (avoid fetching class)
			if ( $node->hasAttribute( 'is_container' ) )
			{
				$ret['class_identifier'] = $node->attribute( 'class_identifier' );
				$ret['is_container']     = (int) $node->attribute( 'is_container' );
			}
			else
			{
				$class                   = $contentObject->attribute( 'content_class' );
				$ret['class_identifier'] = $class->attribute( 'identifier' );
				$ret['is_container']     = (int) $class->attribute( 'is_container' );
			}

			$ret['node_id']        = (int) $node->attribute( 'node_id' );
			$ret['parent_node_id'] = (int) $node->attribute( 'parent_node_id' );
			$ret['url_alias']      = $node->attribute( 'url_alias' );
			$ret['depth']          = (int) $node->attribute( 'depth' );

			if ( $params['fetchPath'] )
			{
				$ret['path'] = array();
				foreach ( $node->attribute( 'path' ) as $n )
				{
					$ret['path'][] = self::simplify( $n );
				}
			}
			else
			{
				$ret['path'] = false;
			}

			if ( $params['fetchChildrenCount'] )
			{
				$ret['children_count'] = $ret['is_container'] ? (int) $node->attribute( 'children_count' ) : 0;
			}
			else
			{
				$ret['children_count'] = false;
			}
		}
		else
		{
			$class                   = $contentObject->attribute( 'content_class' );
			$ret['class_identifier'] = $class->attribute( 'identifier' );
			$ret['is_container']     = (int) $class->attribute( 'is_container' );
		}

		$ret['image_attributes'] = array();

		if ( is_array( $params['dataMap'] ) && is_array(  $params['dataMapType'] ) )
		{
			$dataMap = $contentObject->attribute( 'data_map' );
			foreach( $dataMap as $key => $atr )
			{
				$dataTypeString = $atr->attribute( 'data_type_string' );
				//if ( in_array( $dataTypeString, $params['imageDataTypes'], true) !== false )

				if ( !in_array( 'all' ,$params['dataMap'], true )
				&& !in_array( $key ,$params['dataMap'], true )
				&& !in_array( $dataTypeString, $params['dataMapType'], true )
				&& !( $params['loadImages'] && in_array( $dataTypeString, $params['imageDataTypes'], true ) )
				) continue;
				
				// Get the content handlers
				$contentHandlers = $jvAMFINI->variable('ContentSettings', 'ContentHandlersList');
				$defaultContentHandlerClass = $jvAMFINI->variable('ContentSettings', 'DefaultContentHandler');
				
				$attrtibuteArray[ $key ]['id']         = $atr->attribute( 'id' );
				$attrtibuteArray[ $key ]['type']       = $dataTypeString;
				$attrtibuteArray[ $key ]['identifier'] = $key;
				
				// Define which content handler to use for "formatting" content
				if(isset($contentHandlers[$dataTypeString]))
					$contentHandlerClass = $contentHandlers[$dataTypeString];
				else
					$contentHandlerClass = $defaultContentHandlerClass;
					
				try
				{
					$attrtibuteArray[ $key ]['content']    = call_user_func($contentHandlerClass.'::handleContent', $atr);
				}
				catch(Exception $e)
				{
					$errMsg = $key.'/'.$contentHandlerClass.' : '.$e->getMessage();
					eZDebug::writeError($errMsg);
					eZLog::write('[jvAMF] '.$errMsg);
					continue;
				}
			}
		}
		$ret['data_map'] = $attrtibuteArray;
		return $ret;
	}
}