<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Pie Chart
#
# $RCSfile: pie_chart_image.php,v $  $Revision: 1.2 $
# ------------------------------------

include"../jpgraph-1.8/src/jpgraph.php";
include"../jpgraph-1.8/src/jpgraph_pie.php";

$title 	= $_GET['graph_title'];
$theme	= $_GET['theme'];

$legend	= eval( "return Array(".stripslashes($_GET['legend']).");" );
$data 	= eval( "return Array(".stripslashes($_GET['data']).");" );

# Create the Pie Graph.
$graph = new PieGraph(380,200);
//$graph->SetShadow();

# Set A title for the plot
$graph->title->Set($title);
$graph->title->SetFont(FF_FONT1,FS_BOLD);

# Create
$p1 = new PiePlot($data);
$p1->SetLegends($legend);
$p1->SetTheme($theme); //Sets the colour scheme defined in jpgraph_pie.php

$graph->Add($p1);
$graph->Stroke();


# ------------------------------------
# $Log: pie_chart_image.php,v $
# Revision 1.2  2005/12/08 19:40:10  gth2
# updating reports containing calls to jp-graph - gth
#
# Revision 1.1.1.1  2005/11/30 23:01:26  gth2
# importing initial version - gth
#
# ------------------------------------

?>