<?php


include_once("db.php");
// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
// Check connection

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$existError =false;
  $reply = "";//process starts
if(isset($_POST["page"]) && !empty($_POST["page"]))
      { 
        if ($_POST['msg'] == 'commands') {
        $reply= 'These are my commands <p>1. what is my location, 2. tell me about your author, 3. open facebook, 6. open twitter, 7. open linkedin, 8. shutdown my pc, 9. get my pc name.</p>';
      } 
      else if($reply==""){
        require_once("../answer.php");
        $reply = assistant($_POST['msg']);
       
      }
     if($reply =="") {

         $post= mysqli_real_escape_string($conn, $_POST['msg']);
           $result = decider($post);
           if($result){
                $question=$result[0]; 
                $answer= $result[1];
                $sql = "SELECT * FROM chatbot";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        ;
                        while($row = mysqli_fetch_assoc($result)) {
              $strippedQ = strtolower(preg_replace('/\s+/', '', $question));
              $strippedA = strtolower(preg_replace('/\s+/', '', $answer));
              $strippedRowQ = strtolower(preg_replace('/\s+/', '', $row['question']));
              $strippedRowA = strtolower(preg_replace('/\s+/', '', $row['answer']));
                            if(($strippedRowQ == $strippedQ) && ($strippedRowA == $strippedA)){
                            $reply = "I know this already, but you can make me smarter by giving another response to this command";
                            $existError = true;
                            break;
                            
                            }
                            
                        }        
                } 

                if(!($existError)){
                    $sql = "INSERT INTO chatbot (question, answer) VALUES ('".$question."', '".$answer."')";
                    
                            if (mysqli_query($conn, $sql)) {
                                $reply = "Thanks to you, I am smarter now";
                            } else {
                                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                            }
                    
                    
                   }
                
       
           } else{
             $input = tester($post); 

      if($input){
        
      
        $time ="what is the time";
        // query db to look for question 
        $answer = "";
        $sql = "SELECT * FROM chatbot";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
          if (!$result) {
            die(mysqli_error($link));
        }
          $input = strtolower(trim($input));
          $sql = "SELECT * FROM chatbot WHERE trim(question) = '$input'";
          $result = mysqli_query($conn, $sql);
                     
          if(mysqli_num_rows($result) > 0){
            
            $answer = [];         
          while($row = mysqli_fetch_assoc($result)) {
            array_push($answer, $row['answer']);
                    
        } 
        $answer = $answer[array_rand($answer)];
         }       
            }
            
      if($answer != ""){
        $reply = $answer;
        
            
         } 
    }
    // end input
           
  } 
  // end test
 

  if($reply == ""){
        $reply ="I did'nt get that, please rephrase or try again later";
    }
  }
  echo $reply;
  exit();
  $sql = "SELECT * FROM secret_word";
 $result = mysqli_query($conn, $sql);
 $row = mysqli_fetch_assoc($result);
 $secret_word = $row['secret_word'];
 // $secret_word= "sample_secret_word";
}

// function

function decider($string){
  
  if (strpos($string, ":") !== false)
  {
    $field = explode (":", $string, 2);
    $key = $field[0];
    $key = strtolower(preg_replace('/\s+/', '', $key));
  if(($key == "train")){
     $password ="p@55";
     $trainer =$field[1];
     $result = explode ("#", $trainer);
  if($result[2] && $result[2] == $password){
    echo"<br>Training mode<br>";
    return $result;
  } else echo "opssss!!! Looks like you are trying to train me without permission";
  

    
     
  }
   }
   

}


  function tester($string){
   if (strpos($string, ":" ) !== false) 
   { 
    $field = explode (":", $string);
    $key = $field[0];
    $key = strtolower(preg_replace('/\s+/', '', $key));
    if(($key !== "train")){
      
     echo"<br>testing mode activated<br>";
     return $string;
  }
 }
 return $string;
  }

  






?>


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <!-- Page Content -->
    <div class="container">

      <!-- Portfolio Item Heading -->
      <h1 >Chidimma Juliet Ezekwe</h1>
      <small>Wed Developer</small>

      <!-- Portfolio Item Row -->
      <div class="row">

        <div class="col-md-6">
          <img class="img-fluid" src="http://res.cloudinary.com/julietezekwe/image/upload/v1523620041/juliet.jpg" alt="juliet">
        </div>

        <div class="col-md-6">
          <h3 class="my-3">Description</h3>
          <p>An Innovative web deveploper inter at HngInternship<sup>4</sup></p>
          <h3 class="my-3">Details</h3>
          <ul>
            <li>Creative</li>
            <li>Innovative</li>
            <li>Team player</li>
            <li>Result oriented and time conscious</li>
          </ul>
        </div>

      </div>
      <div class="row">
        <!-- chatbot -->
        <div class="col-md-6">
          
            <button type="button" class="btn btn-danger btn-lg pull-right" data-toggle="collapse" data-target="#chat">Chat now</button>
              
              <div id="chat" class="wrapper collapse">

                <div class="content">

                  <div class="sidebar">

                <br><br>

                    <div class="contacts">

                   <li class="person">
                          <span class="avatar">
                            <img src="http://res.cloudinary.com/julietezekwe/image/upload/v1523964193/human.png" alt="Sacha Griffin" />
                            <span class="status online"></span>
                          </span>
                          <span class="info">
                            <span class="status-msg">You Are Currently Logged In </span>
                          </span>
                        </li>
                           <li class="person">
                          <span class="avatar">
                            <img src="http://res.cloudinary.com/julietezekwe/image/upload/v1523964204/robot.jpg" alt="NitroChatBot" />
                            <span class="status online"></span>
                          </span>
                          <span class="info">
                            <span class="name">Julie's Assist</span>
                            <span class="status-msg">I am Julies's Assistant</span>
                          </span>
                        </li>

                      </ul><!-- /.contact-list -->

                    </div><!-- /.contacts -->

                  </div><!-- /.sidebar -->

                  <div class="chatbox">

                    <div class="person">
                      <span class="avatar">
                        <img src="http://res.cloudinary.com/julietezekwe/image/upload/v1523964193/human.png" alt="human Image" />
                        <span class="status online"></span>
                      </span>
                      <span class="info">
                       <span class="login-status">Online    | <?php
            echo "" . date("h:i:a");
            ?>, <?php
            $query = @unserialize (file_get_contents('http://ip-api.com/php/'));
            if ($query && $query['status'] == 'success') {
            echo '' . $query['country'] . ', ' . $query['city'] . '!';
            }
            ?></span>
                        
                      </span>
                    </div><!-- /.person -->
                <script>
            $(document).ready(function(){
            var hiddenDiv = $(".messages");
            var show = function() {
            hiddenDiv.fadeIn();
            play();

            };

            hiddenDiv.hide();
            setTimeout(show, 2000);


            });
                </script>
                    <div class="chatbox-messages" >
                      <div class="messages clear"><span class="avatar"><img src="http://res.cloudinary.com/julietezekwe/image/upload/v1523964204/robot.jpg"alt="Debby Jones" /></span><div class="sender"><div class="message-container"><div class="message"><p>
                      Hi My name is Cutie <i class="em em-sunglasses"></i> I can tell you about My Author <i class="em em-smiley"></i></p>
                              <p>You can tell me what to do i promise not to fail you, just type "commands' to see the list of what i can do.<br>You can train me too by simply using the key word "train", seperate the command and response with "#", and ofourse, the password</p>
                              </div><span class="delivered"><?php
            echo "" . date("h:i:a");
            ?></span></div><!-- /.message-container -</div><!-- /.sender --></div><!-- /.messages -->
                            </div>
                            <div class="push"></div>

                    </div><!-- /.chatbox-messages -->


                    <div class="message-form-container">

                      <script type="text/javascript">

                                  $(document).ready(function(){
               $('#msg').keypress(
                function(e){
                    if (e.keyCode == 13) {
                        e.preventDefault();
                        var msg = $(this).val();
                  $(this).val('');
                        if(msg!='')
                  $('<div class="messages clear"><div class="user"><div class="message-container"><div class="message"><p>'+msg+'</p></div><span class="delivered"><?php
            echo "" . date("h:i:a");
            ?></span></div></div><!-- /.user --></div>').insertBefore('.push');
                  $('.chatbox-messages').scrollTop($('.chatbox-messages')[0].scrollHeight);

                  formSubmit();

                    }

                function formSubmit(){
                var message = $("#msg").val();
                    var dataString = 'msg=' + msg + '&page=chat';
                    jQuery.ajax({
                        url: "juliet.php",
                        data: dataString,
                        type: "POST",
                         cache: false,
                             success: function(response) {
            setTimeout(function(){
                     $(' <div class="messages clear"><span class="avatar"><img src="http://res.cloudinary.com/julietezekwe/image/upload/v1523964204/robot.jpg"alt="Debby Jones" /></span><div class="sender"><div class="message-container"><div class="message"><p>'+response+'</p></div><span class="delivered"><?php
            echo "" . date("h:i:a");
            ?></span></div><!-- /.message-container -</div><!-- /.sender --></div><!-- /.messages --></div>').insertBefore('.push');
                  $('.chatbox-messages').scrollTop($('.chatbox-messages')[0].scrollHeight);
                  play();
                },  1000);

                  },
                        error: function (){}
                    });
                return true;
                }
                    });
            });
                  function play(){
                   var audio = document.getElementById("audio");
                   audio.play();
                             }                
            </script>
            <audio id="audio" src="https://res.cloudinary.com/julietezekwe/video/upload/v1523964158/beep.mp3" ></audio>

                      <form class="message-form" method="POST" action="" >
                        <textarea id="msg" name="msg" value=""  placeholder="Type a message here..."></textarea>
                          </form><!-- /.search-form -->


                    </div><!-- /.message-form-container -->

                  </div><!-- /.chatbox -->

                </div><!-- /.content -->

              </div><!-- /.wrapper -->


        </div>
      </div>
      <!-- /.row -->

    

    </div>
    <!-- /.container -->



    <!-- Bootstrap core JavaScript -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Custom scripts for this template -->
    <script src="../js/hng.min.js"></script>

  </body>

</html>
<style type="text/css">
  *, *:after, *:before {
  -moz-box-sizing:border-box;
  box-sizing:border-box;
  -webkit-font-smoothing:antialiased;
  font-smoothing:antialiased;
  text-rendering:optimizeLegibility;
}

html {
  font-size:75%;
}
body {
  font: 400 normal 14px/1.4 'Lato', sans-serif;
  color: #706c72;
  background: #0bc3f7;
}

.clear:before,
.clear:after {
   content: ' ';
   display: table;
}

.clear:after {
    clear: both;
}
.clear {
    *zoom: 1;
}
img {
  width: 100%;
  vertical-align: bottom;
}
a, a:visited {
  color: #2895F1;
  text-decoration: none;
}
a:hover, a:focus {
  text-decoration: none;
}
a:focus {
  outline: 1;
}

/*------------------------------------*\
    Structure
\*------------------------------------*/

.wrapper {
  width: 100%;
}

.content {
  width: 736px;
  height: 560px;
  margin: 40px auto;
  border-radius: 10px;
  box-shadow: 0 15px 30px 5px rgba(0,0,0,0.4);
}

.sidebar {
  float: left;
  width: 100%;
  max-width: 296px;
  height: 100%;
  background: #2b2130;
  border-radius: 10px 0 0 10px;
}

.chatbox {
  position: relative;
  float: left;
  width: 100%;
  max-width: 440px;
  height: 100%;
  background: #fdfdfd;
  border-radius: 0 10px 10px 0;
  box-shadow: inset 20px 0 30px -20px rgba(0, 0, 0, 0.6);
}

/*------------------------------------*\
    Sidebar
\*------------------------------------*/


/* Contact List */

.contact-list {
  margin: 0;
  padding: 0;
  list-style-type: none;
  height: 100%;
  max-height: 460px;
  overflow-y: hidden;
}


.contact-list .person {
  position: relative;
  padding: 12px 0;
  border-bottom: 1px solid rgba(112,108,114,0.3);
  cursor: pointer;
}


.contact-list .person.active:after {
  content: '';
  display: block;
    position: absolute;
      top: 0; left: 0; bottom: 0; right: 0;
  border-right: 4px solid #0bf9c7;
  box-shadow: inset -4px 0px 4px -4px #0bf9c7;
}

.person .avatar img {
  width: 56px;
  margin-left: 25px;
  border-radius: 50%;
}

.person .avatar {
  position: relative;
  display: inline-block;
}

.person .avatar .status {
  position: absolute;
  right: 6px;
  bottom: 0;
  width: 15px;
  height: 15px;
  border-radius: 50%;
  background: #9e99a1;
  border: 4px solid #222; 
}

.person .avatar .status.online {
  background: #0bf9c7;
}

.person .avatar .status.away {
  background: #f4a711;
}

.person .avatar .status.busy {
  background: #f42464;
}

.person .info {
  display: inline-block;
  width: 200px;
  padding: 0 0 0 10px; 
}

.person .name, .person .status-msg {
  display: inline-block;
}

.person .name {
  color: #fdfdfd;
  font-size: 17px;
  font-size: 1.7rem;
  font-weight: 700;
}

.person .status-msg {
  width: 180px;
  font-size: 14px;
  font-size: 1.4rem;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}



/*------------------------------------*\
    Chatbox
\*------------------------------------*/

.chatbox {
  color: #a0a0a0;
}

/* Chatbox header */

.chatbox .person {
  position: relative;
  margin: 12px 20px 0 0;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(112,108,114,0.2);
}

.chatbox .person .avatar .status {
  border-color: #fff;
}

.chatbox .person .info {
  width: 290px;
  padding-left: 20px;
}

.chatbox .person .name {
  color: #a0a0a0;
  font-size: 19px;
  font-size: 1.9rem;
}

.chatbox .person .login-status {
  display: block;
}

/* Chatbox messages */

.chatbox-messages {
  margin: 20px 20px 0 44px;
  height: 376px;
  overflow-y: overlay;
}

.chatbox-messages .avatar {
  float: left;
}

.chatbox-messages .avatar img {
  width: 56px;
    border-radius: 50%;
}

.chatbox-messages .message-container {
  position: relative;
  float: right;
  width: 320px;
  padding-left: 10px;
}

.chatbox-messages .message {
  display: inline-block;
  max-width: 260px;
  margin-bottom: 12px;
  border: 1px solid #dedede;
  border-radius: 25px;
}

.chatbox-messages .sender .message {
  background: #fff;
}

.chatbox-messages .user .message {
  background: #dedede;
}

.chatbox-messages .sender .message-container:first-child .message {
  border-radius: 0 50px 50px 50px;
}

.chatbox-messages .user .message-container:first-child .message {
  border-radius: 50px 0 50px 50px;
}

.chatbox-messages .message p {
  margin: 14px 24px;
  font-size: 11px;
  font-size: 1.1rem;
}

.chatbox-messages .delivered {
  position: absolute;
  top: 0;
  right: 0;
  font-size: 10px;
  font-size: 1.0rem;
}

/* Chatbox message form */

.message-form-container {
  width: 400px;
  height: 74px;
  position: absolute;
  left: 0;
  bottom: 0;
  margin: 0 20px;
  border-top: 1px solid rgba(112,108,114,0.2);
}

.message-form textarea {
  width: 290px;
  margin: 6px 0 0 24px;
  resize: none;
  border: 0;
  color: #a0a0a0;
  outline: 0;
}

.message-form textarea::-webkit-input-placeholder { color: #a0a0a0; }
.message-form textarea::-moz-placeholder { color: #a0a0a0;  }
.message-form textarea::-ms-placeholder { color: #a0a0a0; }
.message-form textarea:-moz-placeholder { color: #a0a0a0; }

.message-form textarea:focus::-webkit-input-placeholder { color: transparent; }
.message-form textarea:focus::-moz-placeholder { color: transparent;  }
.message-form textarea:focus::-ms-placeholder { color: transparent; }
.message-form textarea:focus:-moz-placeholder { color: transparent; }

/*------------------------------------*\
    Contacts List - Custom Scrollbar
\*------------------------------------*/


</style>

