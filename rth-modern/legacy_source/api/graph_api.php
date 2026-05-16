<?php
# ---------------------------------------------------------------------
# rth is a requirement, test, and bugtracking system
# Copyright (C) 2005 George Holbrook - rth@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
#----------------------------------------------------------------------
# ------------------------------------
# Graph API
#
# $RCSfile: graph_api.php,v $ $Revision: 1.1.1.1 $
# ------------------------------------

# ----------------------------------------------------------------------
# Creates pie chart image and stores it in images/chart_pie_image.png
# Inputs: Graph title, theme, array of values for legend, array of data, x and y coordinates
# Output: Graph is created and saved in images/piecharts2/pie_chart_image.png
# ----------------------------------------------------------------------
function graph_pie_chart($graph_title, $graph_theme, $legend_array, $data_array, $xcoord, $ycoord) {


	//unlink("Images/piecharts2/pie_chart_image.png");
	print "$graph_title";
	print "$graph_theme";
	print "$legend_array";
	print "$data_array";
	print "$xcoord";
	print "$ycoord";
	print"$legend_array[2]";

	$graph = new PieGraph($xcoord,$ycoord);

	$graph->title->Set($graph_title);

	$graph->title->SetFont(FF_FONT1,FS_BOLD);

	$pie = new PiePlot($data_array);

	$pie->SetLegends($legend_array);
	
	$pie->SetTheme($graph_theme); //Sets the colour scheme defined in jpgraph_pie.php

	$graph->Add($pie);
	
	$graph->Stroke("images/piecharts2/pie_chart_image.png");
	
}

# ------------------------------------
# $Log: graph_api.php,v $
# Revision 1.1.1.1  2005/11/30 23:01:11  gth2
# importing initial version - gth
#
# ------------------------------------

?>