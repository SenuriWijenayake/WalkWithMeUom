<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MenuController extends CI_Controller {

	public function index()
	{
	
	}

	public function loadMenu()
	{
		$data = json_decode(file_get_contents("php://input"),TRUE);
		$mobileNumber = 713456781;//(int) $data['UserId'];
		$username = "Mandy Moore";//$data['Username'];
		$resultSet = [];
		$resultWalkInvitations = [];
		$resultNextWalk = [];
		$prevWalk;
		
		//Extracting the walking invitations
		
		$result = $this->Menu->getInvitations($mobileNumber, $username);
		if (count($result) > 0)
        {
            foreach ($result as $row)
            {
                
               $walkId = $row->walkId;
               $row->Participants = $this->Menu->getParticipants($walkId);
               array_push($resultWalkInvitations, $row);
               
            }
        }

		//Extracting next walk details
        
		$createdWalks = $this->Menu->getCreatedWalks($mobileNumber, $username);
		if (count($createdWalks) > 0)
        {
            foreach ($createdWalks as $row)
            {
                
               $walkId = $row->walkId;
               $prevWalk = $row->Date;
               $row->Participants = $this->Menu->getParticipants($walkId);
               array_push($resultNextWalk, $row);
               
            }
        }

		$invitedWalks = $this->Menu->getFirstInvitation($mobileNumber, $username);
		if (count($invitedWalks) > 0)
        {
            foreach ($invitedWalks as $row)
            {
                
               $walkId = $row->walkId;
               $date = $row->Date;
               if($date<$prevWalk){
               		$row->Participants = $this->Menu->getParticipants($walkId);
               		array_pop($resultNextWalk);
               		array_push($resultNextWalk, $row);
               }
              
            }
        }
	
        //Extracting the walking history
        $history = $this->Menu->getHistoryOfCreatedWalks($mobileNumber);
        
        //merging the result array
        $resultSet = array_merge(array("nextWalk" => $resultNextWalk), array("invitations" => $resultWalkInvitations), array("walkHistory" => $history));
        print_r(json_encode($resultSet));

	}
	
}
