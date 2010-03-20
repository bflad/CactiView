<?
#########################################################################
#
#       CactiView v1.0 - Brian Flad
#       http://fladpad.com
#       
#       Forked from the excellent:
#       CactiView v0.1 - Laurie Denness
#       http://laurie.denness.net - laurie@denness.net
#
#       Displays a section of Cacti graphs based on your selection.
#       Graphs rotate automatically rotate with AJAX
#
#       Configuration is available in config.php
#
#########################################################################

## CactiView configuration

# Time (in milliseconds) before the graphs will rotate automatically.
$timeout = 15000;

# Path to cacti (on your webserver, including the trailing slash) e.g. http://cactihost/cacti/
$cactipath = "http://cactihost/cacti/";

# Graph definitions
#
# Alter the lines below to take the graphs you wish to rotate. 
# For example, if the Cacti URL http://host/cacti/graph.php?action=view&local_graph_id=558&rra_id=all
# then your "cactiid" is 558. Then enter a title of your choosing to be display on the graph. 
#
# You can define as many graphs as you wish. 

$graphs = array (
array("cactiid" => 12 , "title" => "Title 1" ),
array("cactiid" => 23 , "title" => "Title 2" ),
);

# Disable debugging
error_reporting(0);

?>
