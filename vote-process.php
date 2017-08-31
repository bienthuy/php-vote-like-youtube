<?php
/* Voting system
* Shared by BienThuy.Com
*  https://bienthuy.com/download/ma-nguon/tao-he-thong-vote-giong-youtube/
* Skype: ask.bienthuy@outlook.com
* Facebook: https://www.facebook.com/bienthuywebsite
* Twitter: https://www.twitter.com/bienthuywebsite
* Youtube : https://www.youtube.com/bienthuywebsite
* Pinterest: https://www.pinterest.com/bienthuywebsite
* Google plus: https://plus.google.com/+bienthuywebsite
* @ 2017. All Rights Reserved.
*/
####### ket noi toi database ##########
$db_username = 'xxxxx';
$db_password = 'xxxxx';
$db_name = 'xxxxx';
$db_host = 'localhost';
####### ket noi toi database ##########
if($_POST)
{ 
    ### kiem tra va ket noi toi database
    $sql_con = mysqli_connect($db_host, $db_username, $db_password,$db_name)or die('could not connect to database');
    //xem khach vote up hay down
    $user_vote_type = trim($_POST["vote"]);
    //tao mot id moi cho bai viet duoc vote
    $bt_id = filter_var(trim($_POST["unique_id"]),FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    //chuyen cai id nay thanh md5 cho no unique :D
    $bt_id = hash('md5', $bt_id);   
    //kiem tra xem cai request nay co phai gui qua ajax khong, neu gui truc tiep thi tu choi xu ly
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die();
    }
    switch ($user_vote_type)
    {        
       ##### neu ma khach thich bai viet #########
        case 'up':           
            //dung cookie de kiem tra xem khach da vote cho bai viet nay chua
            if (isset($_COOKIE["voted_".$bt_id]))
            {
            //khach da vote bai nay roi
			header('HTTP/1.1 500 Already Voted');
            //thoat ra, khong xu ly nua
			exit();
            }           
            //lay du lieu trong database ra de hien thi xem co bao nhieu luot vote up roi
            $result = mysqli_query($sql_con,"SELECT vote_up FROM bt_demvote WHERE content_id='$bt_id' LIMIT 1");
            $get_total_rows = mysqli_fetch_assoc($result);           
            if($get_total_rows)
            {
                //neu tim thay du lieu, thi sẽ update cai vote up đó thêm 1 lần like
                mysqli_query($sql_con,"UPDATE bt_demvote SET vote_up=vote_up+1 WHERE content_id='$bt_id'");
            }else{
            // neu khong tim thay thi insert vao du lieu
                mysqli_query($sql_con,"INSERT INTO bt_demvote (content_id, vote_up) value('$bt_id',1)");
            }   
			// set cookie trong vong 2 gio moi duoc vote 1 lan :D
            setcookie("voted_".$bt_id, 1, time()+7200);
            //hien thi tong lan vote up
			echo ($get_total_rows["vote_up"]+1);
            break; 
        ##### Neu user vote down #########
        case 'down':           
            //dung cookie de kiem tra xem khach da vote cho bai viet nay chua
            if (isset($_COOKIE["voted_".$bt_id]))
            {
			//khach da vote bai nay roi
                header('HTTP/1.1 500 Already Voted this Content!');
            //thoat ra, khong xu ly nua
			 exit();
            }
            //lay du lieu trong database ra de hien thi xem co bao nhieu luot vote up roi
            $result = mysqli_query($sql_con,"SELECT vote_down FROM bt_demvote WHERE content_id='$bt_id' LIMIT 1");
            $get_total_rows = mysqli_fetch_assoc($result);           
            if($get_total_rows)
            {
            //neu tim thay du lieu, thi sẽ update cai vote up đó thêm 1 lần like
                mysqli_query($sql_con,"UPDATE bt_demvote SET vote_down=vote_down+1 WHERE content_id='$bt_id'");
            }else{
            // neu khong tim thay thi insert vao du lieu
                mysqli_query($sql_con,"INSERT INTO bt_demvote (content_id, vote_down) value('$bt_id',1)");
            }        
			// set cookie trong vong 2 gio moi duoc vote 1 lan :D
            setcookie("voted_".$bt_id, 1, time()+7200); 
			//hien thi tong lan vote down
            echo ($get_total_rows["vote_down"]+1);
            break;        
        ##### Hien thi ra ngoai #########     
        case 'fetch':
            //lay du lieu so lan vote up va so lan vote down
            $result = mysqli_query($sql_con,"SELECT vote_up,vote_down FROM bt_demvote WHERE content_id='$bt_id' LIMIT 1");
            $row = mysqli_fetch_assoc($result);           
            // Dam bao rang so lieu nay phai la so va khong duoc null
            $vote_up    = ($row["vote_up"])?$row["vote_up"]:0;
            $vote_down  = ($row["vote_down"])?$row["vote_down"]:0;           
            //tao json cho no trong mang array de hien thi ra ngoai sau nay
            $send_response = array('vote_up'=>$vote_up, 'vote_down'=>$vote_down);
            echo json_encode($send_response); //display json encoded values
            break;
    }
}
?>