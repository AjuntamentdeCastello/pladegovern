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

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

$key = 'Here_put_your_Trello_Key';

$application_token = 'Here_your_Trello_App_Token';

$id_board = 'Here_the_Id_of_your_board';

$labelsnames = array("done"=>"FETS", "doing"=>"MARXA", "todo"=>"PENDENTS", "waiting"=>"PARTIR");

$castelloKanbanBoard = new \AjuntamentDeCastello\Kanban\CastelloKanbanBoard($key, $application_token, $id_board, $labelsnames);

$castelloKanbanBoard->getDataFromTrello();

$castelloKanbanBoard->calculateIndicators();

$columnHeadings = array("done"=>"Fets", "doing"=>"En Marxa", "todo"=>"Pendents", "waiting"=>"Mandat");

$castelloKanbanBoard->calculateCSVData($columnHeadings);

$a = $castelloKanbanBoard->getCSVData();

foreach ($a as $key => $value)
{
	fputcsv($output, $value);
}


?>
