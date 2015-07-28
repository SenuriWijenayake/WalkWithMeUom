<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

     function getInvitations ($mobileNumber, $username)
    {
        
        $invitationQuery = $this->db->query("SELECT userwalks.id as `walkId`, userwalks.inviterName as `Inviter`, DATE_FORMAT(userwalks.dateOfWalk, '%a %d %b %Y') as `Date` , DATE_FORMAT(userwalks.dateOfWalk, '%r') as `Time`
                                             FROM userwalks
                                             INNER JOIN walkparticipants on userwalks.id = walkparticipants.walkId
                                             WHERE walkparticipants.participantNum = ' ". $mobileNumber . " '
                                             ORDER BY userwalks.dateOfWalk");

        

        return $invitationQuery->result();
    
    }

        function getParticipants ($walkIdentity)
    {
        
             //$walkIdentity = "a16eb592-060b-470e-9da8-2a51a093dc97";
             $participantsQuery = $this->db->query("SELECT user.mobileNumber as 'ParticipantNumber', user.username as 'ParticipantName'
                                                    FROM user
                                                    INNER JOIN walkparticipants on user.id = walkparticipants.participantId
                                                    WHERE (walkparticipants.participantStatus <> 'Denied') AND (walkparticipants.walkId = '" .$walkIdentity. "')"); 
        
        
            return $participantsQuery->result();
        
    }

        function getCreatedWalks ($mobileNumber, $username)
    {   

        $today = date("Y-m-d"); 
        $createdWalksQuery = $this->db->query("SELECT userwalks.id as `walkId`, userwalks.inviterName as `Inviter`, DATE_FORMAT(userwalks.dateOfWalk, '%a %d %b %Y') as `Date` , DATE_FORMAT(userwalks.dateOfWalk, '%r') as `Time`
                                              FROM userwalks
                                              WHERE userwalks.dateOfWalk >=  '" . $today. "' " . " AND userwalks.inviterId = ' ". $mobileNumber . " ' ".
                                              "ORDER BY userwalks.dateOfWalk
                                              LIMIT 0,1 ");


        return $createdWalksQuery->result();
    }

    function getFirstInvitation ($mobileNumber, $username)
    {
        $today = date("Y-m-d"); 
        $invitationQuery = $this->db->query("SELECT userwalks.id as `walkId`, userwalks.inviterName as `Inviter`, DATE_FORMAT(userwalks.dateOfWalk, '%a %d %b %Y') as `Date` , DATE_FORMAT(userwalks.dateOfWalk, '%r') as `Time`
                                             FROM userwalks
                                             INNER JOIN walkparticipants on userwalks.id = walkparticipants.walkId
                                             WHERE  userwalks.dateOfWalk >=  '" . $today. "' " . " AND walkparticipants.participantNum = ' ". $mobileNumber . " '".
                                             "ORDER BY userwalks.dateOfWalk
                                             LIMIT 0,1");


        return $invitationQuery->result();
    
    }

    function getHistoryOfCreatedWalks ($mobileNumber){
        $historyQuery = $this->db->query("SELECT  Month,SUM(Count) as 'countWalks'
                                          FROM(
                                          (SELECT DATE_FORMAT(userwalks.dateOfWalk, '%M') as `Month`, COUNT(*) as `Count`
                                          FROM userwalks
                                          WHERE userwalks.inviterId = 713456781 AND userwalks.dateOfWalk < now() AND (MONTH(now()) - MONTH(userwalks.dateOfWalk) IN (0,1,2)) GROUP BY DATE_FORMAT(userwalks.dateOfWalk, '%M'))
                                          
                                          UNION ALL

                                          (SELECT DATE_FORMAT(userwalks.dateOfWalk, '%M') as `Month`, COUNT(*) as `Count`
                                          FROM userwalks
                                          INNER JOIN walkparticipants
                                          WHERE userwalks.id = walkparticipants.walkId AND walkparticipants.participantNum = 713456781 AND 
                                                userwalks.dateOfWalk < now() AND (MONTH(now()) - MONTH(userwalks.dateOfWalk) IN (0,1,2))
                                          GROUP BY DATE_FORMAT(userwalks.dateOfWalk, '%M'))
                                          ) t
                                          GROUP BY Month");
        return $historyQuery->result();
    }


}
