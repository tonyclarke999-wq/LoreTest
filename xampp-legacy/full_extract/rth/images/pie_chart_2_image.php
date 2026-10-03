<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Pie Chart 2
#
# $RCSfile: pie_chart_2_image.php,v $  $Revision: 1.1.1.1 $
# ------------------------------------

include"../jpgraph-1.8/src/jpgraph.php";
include"../jpgraph-1.8/src/jpgraph_pie.php";

$graph_title 	= $_GET['graph_title'];
$p1_title 		= $_GET['p1_title'];
$p2_title 		= $_GET['p2_title'];
$p1_theme 		= $_GET['p1_theme'];
$p2_theme 		= $_GET['p2_theme'];

$theme	= "test";


$legend		= eval( "return Array(".stripslashes($_GET['legend']).");" );
$p1_data 	= eval( "return Array(".stripslashes($_GET['p1_data']).");" );
$p2_data 	= eval( "return Array(".stripslashes($_GET['p2_data']).");" );

# Create the Pie Graph.
$graph = new PieGraph(700,250);

# Set A title for the plot
$graph->title->Set($graph_title);
$graph->title->SetFont(FF_FONT2,FS_BOLD);

# Create
$size=0.25;

$p1 = new PiePlot($p1_data);
$p1->SetTheme($p1_theme);
$p1->SetSize($size);
$p1->SetCenter(0.20,0.60);
$p1->SetLegends($legend);
$p1->title->Set($p1_title);

$p2 = new PiePlot($p2_data);
$p2->SetTheme($p2_theme); //Sets the colour scheme defined in jpgraph_pie.php
#$p2->SetTheme("pca"); //Sets the colour scheme defined in jpgraph_pie.php
$p2->SetSize($size);
$p2->SetCenter(0.60,0.60);
$p2->title->Set($p2_title);

$graph->legend->Pos(0.01,0.25,"right","center");
$graph->Add($p1);
$graph->Add($p2);
$graph->Stroke();

# ------------------------------------
# $Log: pie_chart_2_image.php,v $
# Revision 1.1.1.1  2005/11/30 23:01:26  gth2
# importing initial version - gth
#
# ------------------------------------

?>