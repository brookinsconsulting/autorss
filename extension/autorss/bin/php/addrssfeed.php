#!/usr/bin/env php
<?php

include_once( 'kernel/classes/ezscript.php' );
include_once( 'lib/ezutils/classes/ezcli.php' );

$cli =& eZCLI::instance();

$scriptSettings = array();
$scriptSettings['description'] = 'Add RSS feeds';
$scriptSettings['use-session'] = true;
$scriptSettings['use-modules'] = true;
$scriptSettings['use-extensions'] = true;

$script =& eZScript::instance( $scriptSettings );
$script->startup();

$config = '[path-offset:]';
$argumentConfig = '[content_class][attribute_mapping_identifier+]';
$optionHelp = false;
$arguments = false;
$useStandardOptions = true;

$options = $script->getOptions( $config, $argumentConfig, $optionHelp, $arguments, $useStandardOptions );
$script->initialize();

if ( count( $options['arguments'] ) < 2 )
{
    $script->shutdown( 1, 'wrong argument count' );
}

$pathOffset = isset( $options['path-offset'] ) && is_numeric( $options['path-offset'] ) ? $options['path-offset'] : 0;
$args = $options['arguments'];

$class = array_shift( $args );

include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
$params = array(
    'ClassFilterType' => 'include',
    'ClassFilterArray' => array( $class ),
    'Limitation' => array()
);

$nodes =& eZContentObjectTreeNode::subtree( $params, 1 );

include_once( 'extension/autorss/eventtypes/event/autorss/autorsstype.php' );
foreach ( $nodes as $node )
{
    AutoRSSType::createFeedIfNeeded( $node, $args, $pathOffset );
}

$script->shutdown( 0 );

?>