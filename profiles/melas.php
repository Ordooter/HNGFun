<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("select * from secret_word LIMIT 1");
    $result = $result->fetch(PDO::FETCH_OBJ);
    $secret_word = $result->secret_word;

    $result2 = $conn->query("Select * from interns_data where username = 'melas'");
    $user = $result2->fetch(PDO::FETCH_OBJ);
} else {
    require './../db.php';
    require '../answers.php';
    $message = trim(strtolower($_POST['message']));

    //step 1: Figure out the intent of the message
    //intents: Greeting, Find the current time, Ask about the HNG Programme
    //Train the bot
    //Provide directions for HNG Stage completions
    //check the db

    $intent = 'unrecognized';
    $unrecognizedAnswers = [
        'IDK at all at all. My Oga na better empty head. But u fit train me. Kukuma type: <b>#train: Question | Answer.</b>',
        'I don\'t understand bruv. U fit teach me o. Just type: <b>#train: Question | Answer.</b>',
        "Ah no know that one o. Buh you can sha teach me. If you want to just kukuma type: <b>#train: Question | Answer.</b>",
        "I no understand sha. Ask another one"
    ];

    if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false) {
        $intent = 'greeting';
    }

    if (strpos($message, 'how are you') !== false 
            || strpos($message, 'how do you do') !== false
            || strpos($message, 'how u dey') !== false
            || strpos($message, 'how you') !== false
            || strpos($message, 'how u') !== false
            || strpos($message, 'whatsup') !== false
            || strpos($message, 'xup') !== false
            || strpos($message, 'sup') !== false) {
        $intent = 'greeting_response';
    }

    if ((strpos($message, 'ah dey') !== false 
        || strpos($message, 'i dey') !== false) 
        && $intent !== 'greeting_response') {
            $intent = 'casual';
    }

    if ((strpos($message, 'wetin be') !== false ||
        strpos($message, 'what is') !== false) 
        && (strpos($message, 'hng'))) {
        $intent = 'about_hng';
        $response = aboutHNG();
    }

    if ((strpos($message, 'how') !== false) 
        && 
        (strpos($message, 'pass') !== false || strpos($message, 'cross') !== false || 
            strpos($message, 'go about') !== false || strpos($message, 'finish') !== false)
        && 
        (strpos($message, 'stage {{') !== false || strpos($message, 'stage{{') !== false)
        ) {
            $intent = 'about_hng_stage';
            $startIndex = strpos($message, '{{');
            $endIndex = strpos($message, '}}');
            $stage = (int) trim(substr($message, $startIndex + 2, $endIndex - $startIndex - 2));
            $response =  aboutHNGStage($stage);
    }

    //check for a function call
    if (($startIndex = strpos($message, '((')) !== false && ($endIndex = strpos($message, '))')) !== false) {
        if ($startIndex < $endIndex) {
            $message = trim($_POST["message"]);
            $funcName = substr($message, $startIndex + 2, $endIndex - $startIndex - 2);
            $funcName = trim($funcName);
            
            if(!function_exists($funcName)){
                $intent = 'confusion';
                $response = 'You been try call "function" wey no dey exist. Try again';
            } else {
                $intent = 'function_call';
                $response = $funcName();
            }
        }
    }

    //check for bot training
    $trainingData = '';
    if (strpos($message, '#train:') !== false) {
        $intent = 'training';
        $parts = explode('#train:', $message);
        if (count($parts) > 1) {
            $trainingData = $parts[1];
        }
    } else if (strpos($message, '# train:') !== false) {
        $intent = 'training';
        $parts = explode('# train:', $message);
        if (count($parts) > 1) {
            $trainingData = $parts[1];
        }
    } else if (strpos($message, '#train :') !== false) {
        $intent = 'training';
        $parts = explode('#train :', $message);
        if (count($parts) > 1) {
            $trainingData = $parts[1];
        }
    }

    if ($intent === 'training' && $trainingData === '') {
        $response = 'Oga, your training data no go well o. Use this format >>> "#train: Question | Answer"';
    } else if ($trainingData !== '') {
        $intent = 'training';
        $parts = explode('|', $trainingData);
        if (count($parts) > 1) {
            $question = trim($parts[0]);
            $answer = trim($parts[1]);
            //save in db
            $sql = "insert into chatbot (question, answer) values (:question, :answer)";
            $query = $conn->prepare($sql);
            $query->bindParam(':question', $question);
            $query->bindParam(':answer', $answer);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            
            $response = 'Omo! My head don burst o. U sabi something well well. Thank u wella';
        } else {
            $response = 'Oga, your training data no go well o. Use this format >>> "#train: Question | Answer"';
        }
    }

    if ($intent === 'unrecognized') {
        $answer = '';
        $stmt = $conn->prepare("SELECT answer FROM chatbot WHERE question='$message' ORDER BY rand() LIMIT 1");
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            $intent = 'db_question';
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $answer = $row["answer"];
            }
        }
    }

    switch($intent) {
        case 'greeting':
            echo 'Hello. How u dey like this?';
            break;
        case 'greeting_response':
            echo 'Ah dey my personal person';
            break;
        case 'about_hng':
        case 'about_hng_stage':
        case 'function_call':
        case 'training':
            echo $response;
            break;
        case 'db_question':
            echo $answer;
            break;
        case 'casual':
            echo 'Alright. No qualms';
            break;
        case 'confusion':
            echo $response;
            break;
        case 'unrecognized':
        default:
            echo $unrecognizedAnswers[rand(0, count($unrecognizedAnswers) - 1)];
            break;
    }

    exit;
}
  
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Melas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        html, body {
            padding: 0;
            margin: 0;
            height: 100%;
        }

        body {
            background-color: #445544;
            font-family: 'Lato', sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        nav {
            padding: 2em 3em;
            display: flex;
        }

        .nav-header {
            color: white;
            flex: 3;
        }

        .content {
            display: flex;
        }
        
        .desc {
            flex: 2;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .avatar {
            width: 15em;
            border: 1px solid #333;
            border-radius: 50%;
        }

        .data {
            text-align: center;
            color: white;
            font-size: 1.5em;
        }

        .data p {
            font-size: .5em;
            margin-top: -2px;
        }

        .contact {
            display: flex;
        }
        .contact a {
            padding: .5em;
            height: 50px;
            width: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #333;
            margin: .5em;
        }

        .contact a > i {
            color: #fff;
        }

        .contact a:hover {
            background-color: #fff;
        }

        .contact a:hover i {
            color: #333;
        }

        .chat-window {
            flex: 1;
            background-color: #444;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            margin-top: 1.5em;
            /* display: none; */
        }
        
        .chats {
            flex: 1;
            padding: .5em;
            max-height: 65vh;
            overflow-y: scroll;
        }

        .chat {
            font-size: 80%;
            position: relative;
            padding: 8px;
            margin: .5em 0 2em;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
        }

        .received {
            color: #fff;
            background: #075698;
            background: -webkit-gradient(linear, 0 0, 0 100%, from(#2e88c4), to(#075698));
            background: -moz-linear-gradient(#2e88c4, #075698);
            background: -o-linear-gradient(#2e88c4, #075698);
            background: linear-gradient(#2e88c4, #075698);
            
        }

        .sent {
            color: #075698;
            background: #fff;
            /* background: -webkit-gradient(linear, 0 0, 0 100%, from(#2e88c4), to(#075698));
            background: -moz-linear-gradient(#2e88c4, #075698);
            background: -o-linear-gradient(#2e88c4, #075698);
            background: linear-gradient(#2e88c4, #075698); */
        }

        .sent:after {
            content: "";
            position: absolute;
            top: -20px;
            right: 50px;
            bottom: auto;
            left: auto;
            /* border-width: 20px 20px 0 0; */
            border-width: 20px 0 0 20px;
            border-style: solid;
            border-color: transparent #fff;
            display: block;
            width: 0;
        }

        .received:after {
            content: "";
            position: absolute;
            bottom: -20px;
            left: 50px;
            border-width: 20px 0 0 20px;
            border-style: solid;
            border-color: #075698 transparent;
            display: block;
            width: 0;
        }

        #chat-input {
            width: 100%;
            margin-top: .5em;
            padding: .5em 1em;
            font-size: 80%;
            color: #444;
        }


        .chat-trigger {
            position: absolute;
            bottom: 2em;
            right: 2em;
            background-color: white;
            border-radius: 50%;
            padding: .5em .7em;
            box-shadow: 2px 2px 1px #222;
            border-width: 0px;
            color: #222;
        }

        .chat-trigger:hover {
            background-color: #222;
            color: white;
        }

        @media screen and (max-width: 360px) {
            .content {
                flex-direction: column;
            }

            .avatar {
                width: 8em;
                border: 1px solid #333;
                border-radius: 50%;
            }

            .chat-trigger {
                position: fixed;
                bottom: 0em;
                right: 0em;
                margin-top: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <div class="nav-header">
                HNG INTERN
            </div>
        </nav>
    
        <section class="content">
            <div class="desc">
                <image class="avatar" src="http://res.cloudinary.com/ccmelas/image/upload/v1523619383/melas_avatar_1.jpg"/>
                <div class="data">
                    <h3><?php echo $user->name; ?></h3>
                    <p><em>Web Developer</em></p>
                </div>        
                <div class="contact">
                    <a href="https://www.facebook.com/chiemela.chinedum" target="_blank"><i class="fa fa-facebook"></i></a>
                    <a href="https://twitter.com/ccmelas" target="_blank"><i class="fa fa-twitter"></i></a>
                    <a href="https://github.com/ccmelas" target="_blank"><i class="fa fa-github"></i></a>        
                </div>
            </div>
            <div class="chat-window" id="chat-window">
                <div class="chats" id="chats">
                    <p class="chat received">Weldone o. Na me be Mekus. How far</p>
                </div>
                <input type="text" id="chat-input" placeholder="Type and hit enter to send a message"/>
            </div>
            <button class="chat-trigger" id="chat-trigger"><i class="fa fa-comments"></i></button>
        </section>
        
    </div>

    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $("#chat-window").toggle();
            var chatTrigger = $("#chat-trigger");
            chatTrigger.on('click', function() {
                $("#chat-window").toggle(1000);
            });

            $('#chat-input').on('keypress', function (e) {
                if(e.which === 13){
                    //Disable textbox to prevent multiple submit
                    $(this).attr("disabled", "disabled");
                    if(this.value !== '') {
                        //send message
                        $("#chats").append(`<p class="chat sent">${this.value}</p>`);
                        $('#chats').animate({scrollTop: $('#chats').prop("scrollHeight")}, 1000);
                        sendMessage(this.value);
                        this.value = '';
                        
                    }

                    //Enable the textbox again if needed.
                    $(this).removeAttr("disabled");
                }
            });

            function sendMessage(message) {
                $.ajax({
                    method: 'POST',
                    url: 'profiles/melas.php',
                    data: {message: message},
                    success: function(response) {
                        $("#chats").append(`<p class="chat received">${response}</p>`);
                        $('#chats').animate({scrollTop: $('#chats').prop("scrollHeight")}, 1000);
                    },

                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });
    </script>
</body>
</html>