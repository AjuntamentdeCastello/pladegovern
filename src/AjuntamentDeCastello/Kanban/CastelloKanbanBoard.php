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

/**
 * Class to hold the Kanban board information,and the indicators calculated from that information.
 * Achievement of the government commitments are managed with the Kanban board.
 *
 */

namespace AjuntamentDeCastello\Kanban;

class CastelloKanbanBoard 
{
	private $_key;
	private $_application_token;
	private $_id_board;
	private $_labelsnames;
	private $_individual_board_url;
	private $_json_object;
	private $_boardname;
	private $_numcardsdone;
	private $_numcardsdoing;
	private $_numcardstodo;
	private $_numcardswaiting;
	private $_labelscardsdone;
	private $_labelscardsdoing;
	private $_labelscardstodo;
	private $_labelscardswaiting;
	private $_update_date;
	private $_csv_data;

	/**
	 * Constructor
	 *
	 * @param string $key Key from https://trello.com/app-key/
	 * @param string $application_token Token from https://trello.com/1/authorize?expiration=never&scope=read,write,account&response_type=token&name=Server%20Token&key=<HERE_THE_KEY>
	 * @param string $id_board You can export your trello board as json to view the id of the board
	 * @param $labelsname Associative array. It assumes that the board lists follow the done/doing/todo/waiting 
	 * structure (in any order). 
	 * To know which list corresponds to the "done" list, a substring of the title of that list is indicated 
	 * ("done" => "here_the_substring_of_list_title"), and so on.
	 * For example: $labelsnames = array("done"=>"DONE", "doing"=>"CURRENT", "todo"=>"BACKLOG", "waiting"=>"ICEBOX");
	 */

	public function __construct($key, $application_token, $id_board, $labelsnames) 
	{
		$this->_key = $key;
		$this->_application_token = $application_token;
		$this->_id_board = $id_board;						
		$this->_individual_board_url = "https://api.trello.com/1/boards/$this->_id_board?fields=all&actions=all&action_fields=all&actions_limit=1000&cards=all&card_fields=all&card_attachments=false&lists=all&list_fields=all&members=all&member_fields=all&checklists=all&checklist_fields=all&organization=false&key=$this->_key&token=$this->_application_token";
		$this->_json_object = null;
		$this->_boardname = null;
		$this->_numcardsdone = null;
		$this->_numcardsdoing = null;
		$this->_numcardstodo = null;
		$this->_numcardswaiting = null;
		$this->_labelsnames = $labelsnames;
		$this->_labelscardsdone = null;
		$this->_labelscardsdoing = null;
		$this->_labelscardstodo = null;
		$this->_labelscardswaiting = null;
		$this->_update_date = null;
	}

	/**
	 * Return the number of cards in the "done" list
	 * @return integer
	 */
	public function getNumberCardsDone()
	{
		return $this->_numcardsdone;
	}

	/**
	 * Return the number of cards in the "doing" list
	 * @return integer
	 */
	public function getNumberCardsDoing()
	{
		return $this->_numcardsdoing;
	}

	/**
	 * Return the number of cards in the "to do" list
	 * @return integer
	 */
	public function getNumberCardsToDo()
	{
		return $this->_numcardstodo;
	}

	/**
	 * Return the number of cards in the "waiting" list
	 * @return integer
	 */
	public function getNumberCardsWaiting()
	{
		return $this->_numcardswaiting;
	}

	/**
	 * Return the data for the donut multiples graphic in csv format
	 * @return string
	 */
	public function getCSVData()
	{
		return $this->_csv_data;
	}

	/**
	 * Fetch the board information from trello
	 */
	public function getDataFromTrello() 
	{
		# Changed to curl: file_get_contents produces error 426 Upgrade Required
		# Old version:
		# $ctx = NULL;
		# $response = file_get_contents($this->_individual_board_url, false, $ctx);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_individual_board_json);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		curl_close($ch);

		$this->_json_object = json_decode($response);
		$this->_update_date = date("d-m-Y H:i");
	}

	/**
	 * Calculate the total numbers of cards of each type of list and, for each label, the number of cards with 
	 * that label in each list.
	 */
	public function calculateIndicators() 
	{
		$boardname = $this->_json_object->name;

		foreach ($this->_json_object->lists as $list) 
		{
			if ( (strpos( $list->name, $this->_labelsnames["done"]) !== false) && (!($list->closed)) )
			{
				$idlistdone = $list->id;
			}

			if ( (strpos( $list->name, $this->_labelsnames["doing"]) !== false) && (!($list->closed)) )
			{
				$idlistdoing = $list->id;
			}

			if ( (strpos( $list->name, $this->_labelsnames["todo"]) !== false) && (!($list->closed)) )
			{
				$idlisttodo = $list->id;
			}

			if ( (strpos( $list->name, $this->_labelsnames["waiting"]) !== false) && (!($list->closed)) )
			{
				$idlistwaiting = $list->id;
			}
		}		

		$numcardsdone = 0;
		$numcardsdoing = 0;
		$numcardstodo = 0;
		$numcardswaiting = 0;

		foreach ($this->_json_object->cards as $card) 
		{
			if (!($card->closed))
			{
				switch ($card->idList) 
				{
					case $idlistdone:
						$numcardsdone++;
						$cardstate = 'done';
						break;
					case $idlistdoing:
						$numcardsdoing++;
						$cardstate = 'doing';
						break;
					case $idlisttodo:
						$numcardstodo++;
						$cardstate = 'todo';
						break;
					case $idlistwaiting:
						$numcardswaiting++;
						$cardstate = 'waiting';
						break;
				}

				foreach ($card->labels as $key => $label) 
				{
					$labelsnames[$label->id] = $label->name; 

					switch ($cardstate) 
					{
						case 'done':						
							$labelscardsdone[$label->id] = $labelscardsdone[$label->id]+1; 
							break;
						case 'doing':
							$labelscardsdoing[$label->id] = $labelscardsdoing[$label->id]+1; 
							break;
						case 'todo':
							$labelscardstodo[$label->id] = $labelscardstodo[$label->id]+1; 
							break;
						case 'waiting':
							$labelscardswaiting[$label->id] = $labelscardswaiting[$label->id]+1; 
							break;
					}
				}
			}
		}	   

		$this->_boardname = $boardname;
		$this->_numcardsdone = $numcardsdone;
		$this->_numcardsdoing = $numcardsdoing;
		$this->_numcardstodo = $numcardstodo;
		$this->_numcardswaiting = $numcardswaiting;
		$this->_labelsnames = $labelsnames;
		$this->_labelscardsdone = $labelscardsdone;
		$this->_labelscardsdoing = $labelscardsdoing;
		$this->_labelscardstodo = $labelscardstodo;
		$this->_labelscardswaiting = $labelscardswaiting;
	}

	/**
	 * Calculate the data for the donut multiples graphic in csv format: for each label, the number of cards 
	 * with that label in each list.
	 * @param $columnHeadings Associative array. Heading of the csv data. It assumes that the board lists 
	 * follow the done/doing/todo/waiting structure (in any order). 
	 * You must indicate the column heading string for the data of each list. 
	 * For example: $columnHeadings = array("done"=>"Done", "doing"=>"Current", "todo"=>"Backlog", "waiting"=>"Icebox")
	 */
	public function calculateCSVData($columnHeadings) 
	{
		$r = array ();
		$r[] = array('Label',$columnHeadings["done"],$columnHeadings["doing"],$columnHeadings["todo"],$columnHeadings["waiting"]);

		$labelsnames = $this->_labelsnames;

		ksort($labelsnames);

		$labelscardsdone = $this->_labelscardsdone;
		$labelscardsdoing = $this->_labelscardsdoing;
		$labelscardstodo = $this->_labelscardstodo;
		$labelscardswaiting = $this->_labelscardswaiting;

		foreach ($labelsnames as $key => $value)
		{

			if ( !(isset($labelscardsdone[$key])) ) { $labelscardsdone[$key] = 0; }
			if ( !(isset($labelscardsdoing[$key])) ) { $labelscardsdoing[$key] = 0; }
			if ( !(isset($labelscardstodo[$key])) ) { $labelscardstodo[$key] = 0; }
			if ( !(isset($labelscardswaiting[$key])) ) { $labelscardswaiting[$key] = 0; }

			if (trim($value)!='')
			{
				$r[] = array($value, $labelscardsdone[$key], $labelscardsdoing[$key], $labelscardstodo[$key], $labelscardswaiting[$key]);				
			}
      

		}
		$this->_csv_data = $r;
    }  
}

