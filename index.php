<?php 

ini_set('max_execution_time', 0);

include("vendor/autoload.php");

// Swiftmailer
$transporter = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
    ->setUsername("")
    ->setPassword("");
$mailer = Swift_Mailer::newInstance($transporter);


// Twig
$loader = new Twig_Loader_Filesystem(__DIR__."/templates/");
$twig = new Twig_Environment($loader);


// Failled Recipients
$failedRecipients = array();
$numSent = 0;

$contacts = unserialize(file_get_contents('list.php'));

// Looping Clients
foreach($contacts as $contact) { 

    $htmlEmail = $twig->render(
        'email.html',
        $contact
    );

    $textEmail = new \Html2Text\Html2Text($htmlEmail);
    
    // Message
    $message = Swift_Message::newInstance()
        ->setSubject('Some Subject')
        ->setFrom(array('support@dwd.com.au' => 'DriverWeb'))
        ->setTo(array($contact['email'] => $contact['firstname'].' '.$contact['lastname']))
        ->setBody($htmlEmail, 'text/html')
        ->addPart($textEmail->getText(), 'text/plain');

    $numSent += $mailer->send($message, $failedRecipients);

    ob_end_flush();
    print '"'.$contact['email']."\",<br>";
    ob_start();
    
}

var_dump($failedRecipients);

print "\nSent:\n".$numSent;