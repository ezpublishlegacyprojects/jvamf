<?php

try
{
	$aParams = array(
		'node_path' => 'blog/ez-publish/test-de-post'
	);
	$service = new eZContentAMFService();
	$res = $service->fetchContentNode((object)$aParams);
	var_dump($res);
}
catch(Exception $e)
{
	$cli->error($e->getMessage());
}