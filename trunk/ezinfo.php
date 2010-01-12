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

class jvAMFInfo
{
    static function info()
    {
        return array( 'Name'      => 'jvAMF',
                      'Version'   => '@@@VERSION@@@',
                      'Copyright' => 'Copyright © 2009 Jérôme Vieilledent',
                      'Info'      => '<a href="http://projects.ez.no/jvamf" target="_blank">http://projects.ez.no/jvamf</a>'
                      'License'   => 'GNU General Public License v2.0',
                      'Includes the following library'              => array( 'Name' => 'Zend_AMF (part of Zend Framework)',
                                                                              'Version' => '1.9.7',
                                                                              'Copyright' => 'Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)',
                                                                              'License' => 'New BSD License : http://framework.zend.com/license/new-bsd',),
                    );
    }
}

?>