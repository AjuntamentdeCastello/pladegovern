<?php

/*
 * Copyright (c) 2016-2018 The City Council of Castelló. 
 * Licensed under the EUPL, Version 1.2 or as soon they will be approved by the European Commission - 
 * subsequent versions of the EUPL (the "Licence"); You may not use this work except in compliance with the 
 * Licence. You may obtain a copy of the Licence at:
 * http://joinup.ec.europa.eu/software/page/eupl Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an "AS IS" basis, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the Licence for the specific language governing permissions and
 * limitations under the Licence.
 */

require_once dirname(__FILE__) . '/../vendor/autoload.php';

//If you do not want to use composer:
//require_once dirname(__FILE__) . '/../src/AjuntamentDeCastello/Kanban/CastelloKanbanBoard.php';

$key = 'Here_put_your_Trello_Key';

$application_token = 'Here_your_Trello_App_Token';

$id_board = 'Here_the_Id_of_your_board';

$labelsnames = array("done"=>"FETS", "doing"=>"MARXA", "todo"=>"PENDENTS", "waiting"=>"PARTIR");

$castelloKanbanBoard = new \AjuntamentDeCastello\Kanban\CastelloKanbanBoard($key, $application_token, $id_board, $labelsnames);

$castelloKanbanBoard->getDataFromTrello();

$castelloKanbanBoard->calculateIndicators();

?>

<!DOCTYPE html>
<html lang="es">

	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

		<!-- Meta Description -->
		<meta name="author"	 content="City Council of Castello" />
		<meta name="description"  content="Dashboard with updated information on the state of evolution of government commitments." />
		<meta name="keywords"	 content="dashboard, castello city council" />

		<title>City Council of Castello - Government Plan</title>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- Font Awesome CSS -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

		<!-- D3.js -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js" integrity="sha256-dsOXGNHAo/syFnazt+KTBsCQeRmlcW1XKL0bCK4Baec=" crossorigin="anonymous"></script>

		<!-- Spin.js -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js" integrity="sha256-PieqE0QdEDMppwXrTzSZQr6tWFX3W5KkyRVyF1zN3eg=" crossorigin="anonymous"></script>

		<!-- Donut Multiples Graphic -->
		<script src="js/donut-multiples.js"></script>	 

		<!-- Custom styles for this template -->
		<link href="css/government-plan.css" rel="stylesheet">

		<!-- Custom styles for D3J graphic -->
		<link href="css/d3j.css" rel="stylesheet">

	</head>

<body id="page-top">

	<nav class="navbar navbar-default navbar-static-top" role="navigation">
		<div class="container">
			<div class="col-md-6">			

				<img class="img-responsive" src="./img/castello_logo.png" alt="City Council of Castello de la Plana" title="City Council of Castello de la Plana" style="display:block"/>

			</div>

		</div>
	</nav>

	<div id="wrapper">

		<div id="page-wrapper">

			<div class="row">
				<div class="col-lg-12">
					<h2 class="text-success">Government Plan - Commitments - Current Status</h2>
					<br />
				</div>
			</div>

			<div class="row">
				<div class="col-lg-3 col-md-3 col-xs-6">
					<div class="panel panel-blue">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fas fa-child fa-4x"></i>
								</div>
								<div class="col-xs-3 text-left">
									<div class="hugem"><?=$castelloKanbanBoard->getNumberCardsDone()?><div class="hugesubtitle">Done</div></div>
									<div>&nbsp;</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer">
								<span class="pull-left">Done</span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-xs-6">
					<div class="panel panel-green">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fas fa-gavel fa-3x"></i>
								</div>
								<div class="col-xs-3 text-left">
									<div class="hugem"><?=$castelloKanbanBoard->getNumberCardsDoing()?><div class="hugesubtitle">Doing</div></div>
									<div>&nbsp;</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer">
								<span class="pull-left">In progress right now.</span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-xs-6">
					<div class="panel panel-orange">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fas fa-hourglass-start fa-3x"></i>
								</div>
								<div class="col-xs-3 text-left">
									<div class="hugem"><?=$castelloKanbanBoard->getNumberCardsToDo()?><div class="hugesubtitle">Pending</div></div>
									<div>&nbsp;</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer">
								<span class="pull-left">Pending</span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-xs-6">
					<div class="panel panel-grey">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="far fa-map fa-3x"></i>
								</div>
								<div class="col-xs-3 text-left">
									<div class="hugem">&nbsp;<?=$castelloKanbanBoard->getNumberCardsWaiting()?><div class="hugesubtitle">Mandate</div></div>
									<div>&nbsp;</div>
								</div>
							</div>
						</div>
						<a href="#">
							<div class="panel-footer">
								<span class="pull-left">For 2019</span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>

			</div>

			<div class="row">
				<div class="col-lg-12 col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4><i class="fas fa-chart-pie"></i> Current status by categories</h4>						  
						</div>
						<div class="panel-body">
							<div id="d3j-donut-multiples"><div id='d3j-spinner'></div></div>
						</div>
					</div>
				</div>
			</div>

			 <div class="row">
				<div class="col-lg-12">
					<p><i class="fas fa-copyright fa-2x"></i> Copyright &copy; <a href="http://www.castello.es" target="_blank">City Council of Castell&oacute;</a>. Developed by staff of the City Council of Castell&oacute; using Free Software.</p>
					<p><i class="fab fa-github fa-2x"></i> <a href="https://github.com/AjuntamentdeCastello/pladegovern" target="_blank">Source code available on Github</a>.</p>
				</div>
			</div>


		</div>

	</div>

</body>

</html>
