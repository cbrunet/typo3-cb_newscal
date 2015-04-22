<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "cb_newscal"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Calendar for news',
	'description' => 'Display news as calendar of the month.',
	'author' => 'Charles Brunet',
	'author_email' => 'charles@cbrunet.net',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '1.2.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-7.99.99',
			'news' => '3.0.0-3.1.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'roq_newsevent' => '3.0.0-3.1.99'
		),
	),
);