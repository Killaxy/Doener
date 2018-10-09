<?php 
//import std Header
require_once 'head.php';
//import SQL Conection Handler
require_once 'connector.php';

session_start();

//abfangen von Benutzer eingaben und validierung
//Bitte keine direkt abfrage nur vergleichen(also kein Where) sonst sql injection und wenn es den nutzer mit pass gibt dann set validate true
$notValid=1;
$username = strtolower ($_POST['username']);
$passwort = hash("sha1",$_POST['passwort']);
//$passwort = $_POST['passwort'];
//echo "$username $passwort";
//Pass und user check => $notValid=false;

//Check PW and User combination
if(isset($username)){
    $notValid = 1;
	//Get ALL user and pw
    $users = query("SELECT * FROM kunden");
	//Checks every user. (-> performance?)
    while ($user = mysqli_fetch_array($users)) {
        if($user['nick']==$username&$user['pw']==$passwort&$user['val']=="J"){
            $kdnr = $user['kdnr'];
            $notValid = 0;
        }
    }
	//???
    mysqli_free_result($users);
	
}

//Setting Session Parameters. Redirct to kundenkonto
if($notValid == 0){
	//Create Session ID, save (+additional params) into Session-Variable
    $token = hash('sha256',bin2hex(openssl_random_pseudo_bytes (64)));
    $_SESSION['token'] = $token;
    $_SESSION['user'] = $kdnr;
    
	//If Session does not exist, create one
    $result = query("SELECT * FROM sessions WHERE name='".$kdnr."'");
    if(mysqli_num_rows($result)==0)query("INSERT INTO sessions (Sessionnr, name) Values ('".$token."', '".$kdnr."')");
	else query("UPDATE sessions SET Sessionnr = '".$token."' WHERE name='".$kdnr."'");

	//redirect
    header('Location: '.$base."/kundenkonto.php");
}
?>

<!--Login Formular-->
	<link href="css/signin.css" rel="stylesheet">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="Post" class="form-signin">
		<h1 class="h1 mb-3 font-weight-normal">Login</h1>
		<?php if($notValid == 1 && !empty($username)): echo "$username";?>
			<p>Passwort oder Benutzer falsch</p>
		<?php endif; ?>
		<label class="sr-only" for="username">Benutzername:</label>
		<input class="form-control" id="username" name="username" type="text" placeholder="Benutzername">
		<label class="sr-only" for="passwort">Passwort:</label>
		<input class="form-control" id="passwort" name="passwort" type="password" placeholder="Passwort">
		<button class="btn btn-lg btn-primary btn-block" type="submit">Enter</button>
	</form>

<?php 
//import std Footer
require_once 'footer.php';
?>