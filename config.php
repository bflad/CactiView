<?
#########################################################################
#
#       CactiView v1.1 - Brian Flad
#       http://fladpad.com - bflad417@gmail.com
#       
#       Forked and updated from the excellent:
#       CactiView v0.2 - Laurie Denness
#       http://laurie.denness.net - laurie@denness.net
#
#       Displays a rotation selection of graphs from Cacti/Ganglia
#
#       Configuration is available in config.php
#
#########################################################################

## CactiView configuration

# Time (in milliseconds) before the graphs will rotate automatically.
$timeout = 15000;

# Path to cacti (on your webserver, including the trailing slash) e.g. http://cactihost/cacti/
$cactipath = "http://cactihost/cacti/";

# Path to ganglia (on your webserver, including the trailing slash) e.g. http://gangliahost/cacti/
$gangliapath = "http://gangliahost/ganglia/";

# Graph definitions
#
# Alter the lines below to take the graphs you wish to rotate. You can define as many graphs as you wish.
#
# Cacti
# Specific graph information 
# array("source" => "cacti", "cactiid" => 558, "title" => "Graph Title")
# 
# Ganglia
# Cluster information
# array("source" => "ganglia", "cluster" => "Web Servers", "graph_type" => "load_report", "title" => "Web Servers Title")
# Host information (use FQDN, if applicable)
# array("source" => "ganglia", "cluster" => "Web Servers", "graph_type" => "cpu_report", "host" => "www1.example.com", "title" => "Web Servers Title")

$graphs = array (
array("source" => "ganglia", "cluster" => "Web Servers", "graph_type" => "load_report", "title" => "Graph 1" ),
array("source" => "ganglia", "cluster" => "Web Servers", "graph_type" => "cpu_report", "host" => "www1.example.com" , "title" => "Graph 2" ),
array("source" => "cacti", "cactiid" => "558" , "title" => "Graph 3" ),
);

# Disable debugging
error_reporting(0);

?>
