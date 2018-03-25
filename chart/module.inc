<?php if (! defined('HARMONY_INCLUDE')) die("Harmony Library is not loaded !");
/**\defgroup base Base objects
 *
 * Database module for Harmony PHP Library
 *  
 * Author: FredTGZ
 *  
 * Description:
 * This module implements base objects.
 *  
 * Howto:  
 */

	/* Libchart - PHP chart library
	 * Copyright (C) 2005-2008 Jean-Marc Trémeaux (jm.tremeaux at gmail.com)
	 * 
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 * 
	 */

	global $loadmodule;
	
	if ($loadmodule !== false) {
		require_once 'classes/model/Point.php';
		require_once 'classes/model/DataSet.php';
		require_once 'classes/model/XYDataSet.php';
		require_once 'classes/model/XYSeriesDataSet.php';
		
		require_once 'classes/view/primitive/Padding.php';
		require_once 'classes/view/primitive/Rectangle.php';
		require_once 'classes/view/primitive/Primitive.php';
		require_once 'classes/view/text/Text.php';
		require_once 'classes/view/color/Color.php';
		require_once 'classes/view/color/ColorSet.php';
		require_once 'classes/view/color/Palette.php';
		require_once 'classes/view/axis/Bound.php';
		require_once 'classes/view/axis/Axis.php';
		require_once 'classes/view/plot/Plot.php';
		require_once 'classes/view/caption/Caption.php';
		require_once 'classes/view/chart/Chart.php';
		require_once 'classes/view/chart/BarChart.php';
		require_once 'classes/view/chart/VerticalBarChart.php';
		require_once 'classes/view/chart/HorizontalBarChart.php';
		require_once 'classes/view/chart/LineChart.php';
		require_once 'classes/view/chart/PieChart.php';
	}
	else require("module.nfo");
?>
