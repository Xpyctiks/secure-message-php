<?php
$configFile="config.php";
//Include config file - checks, generation if it is absent, and so on.
if (file_exists($configFile) && !empty($configFile)) {
  include_once $configFile;
  //if some variables is not set
  if (empty($key) || empty($mysqli_db) || empty($mysqli_host) || empty($mysqli_dbuser) || empty($mysqli_dbpass)) {
    echo("Some of config.php settings is not set. Please,check it!");
    die();
  }
}
else {
  echo("config.php is not found or it is empty!\n");
  $template="<?php\n//Encryption key.Will be used for message encryption before store in DB.\n\$key=\"\";\n//DB connect settings.\n\$mysqli_db=\"SecureMessage\";\n\$mysqli_host=\"localhost\";\n\$mysqli_dbuser=\"SecureMessage\";\n\$mysqli_dbpass=\"\";\n";
  if (file_put_contents($configFile,$template)) 
  {
    echo("New config.php created successfully!\nPlease, edit the config.php now and set DB credentials + encryption password, then upload content of schema.sql to the newely created DB.");
  }
  else
  {
    echo("Error creation of the new config.php! Check user permissions!");
  }
  die();
}

$mysqli_dsn = "mysql:host={$mysqli_host};dbname={$mysqli_db}";
$mysqli_options = [ PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', ];
$mysqli_dbh = new PDO($mysqli_dsn, $mysqli_dbuser, $mysqli_dbpass, $mysqli_options);

//daily function to clean all expired data in DB
if (isset($_GET['clean']))
{
  global $mysqli_dbh;
  //getting all records
  $mysqli="SELECT * FROM `messages`";
  $data=$mysqli_dbh->query($mysqli, PDO::FETCH_ASSOC);
  $count=$data->rowCount();
  //is there any records at all?
  $currtime=time();
  $counter=0;
  //parsing data
  foreach ($data as $key2 => $result)
  {
    $lifetime=$result['lifetime'];
    $created=$result['created'];
    $id=$result['id'];
    $link=$result['link'];
    //checking exparation and deleting if expired
    if (($created+$lifetime) < $currtime)
    {
      $mysqli="DELETE FROM `messages` WHERE id='".$id."'";
      $mysqli_dbh->query($mysqli, PDO::FETCH_ASSOC);
      $mysqli="INSERT INTO `msglogs` (`msgid`,`msglink`,`ip`) VALUES ('".$id."','".$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"]."/?link=".$link."','expired,unread');";
      $mysqli_dbh->query($mysqli, PDO::FETCH_ASSOC);
      $counter++;
    }
  }
  echo("Cleaned ".$counter." records.");
  die();
}

function generateRandomString($length = 32) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[random_int(0, $charactersLength - 1)];
  }
  return $randomString;
}
?>
<!doctype html>
<html lang="ua">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Безпечно дiлись важливими данними">
    <link rel="icon" type="image/png" href="favicon.png" />
    <title>Безпечнi повiдомлення</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script>
      function closeWindow() {
        window.open('','_self');
        window.close();
      }
    </script> 
  </head>
<body>
<?php
//Create link and secure data function
if (isset($_POST['create']))
{
  //check all variables are set and not empty
  if ((!isset($_POST['textMain'])) || (!isset($_POST['timeValid'])) || (empty($_POST['textMain'])) || (empty($_POST['timeValid'])))
  {
    echo("<script>alert('Помилка! Ви не ввели текст, або ще щось.');</script>");
    die();
  }
  //checking CSRF
  if ((!isset($_POST['csrfToken'])) || (empty($_POST['csrfToken'])) || (!isset($_COOKIE['CSRF'])) || (empty($_COOKIE['CSRF'])) || ($_POST['csrfToken'] != $_COOKIE['CSRF']))
  {
    echo("<script>alert('Помилка CSRF token. Оновiть сторiнку i спробуйте ще раз.');</script>");
    die();
  }
  $ciphering = "AES256";
  $iv_length = openssl_cipher_iv_length($ciphering);
  $options = 0;
  $encryption_iv = mb_substr($_POST['csrfToken'],0,6).mb_substr($_POST['csrfToken'],0,6)."1467";
  $encryption_key = $key;
  $link=generateRandomString();
  $encrypted_text = openssl_encrypt(trim(htmlspecialchars($_POST['textMain'])), $ciphering, $encryption_key, $options, $encryption_iv);
  $mysqli="INSERT INTO `messages` (`created`,`lifetime`,`token`,`link`,`message`) VALUES ('".time()."','".(trim(htmlspecialchars($_POST['timeValid']))*3600)."','".(trim(htmlspecialchars($_POST['csrfToken'])))."','".$link."','".$encrypted_text."')";
  $mysqli_dbh->query($mysqli, PDO::FETCH_ASSOC);
  ?>
  <div class="bg-light p-5 rounded">
    <div class="col-sm-8 mx-auto">
      <h1>Ось ваше тимчасове посилання:</h1>
        <p><a href="<?php echo("https://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?link=".$link);?>"><?php echo("https://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?link=".$link);?></a></p>
        <p>Скопiюйте його та передайте кому вважаєте за потрiбне. Це посилання можна буде вiдкрити тiльки один раз.</p>
        <p><a href="<?php echo("https://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);?>">Створити ще одне</a></p>
    </div>
  </div>
  <?php
  die();
}

//Open link function.Has Open button function to make the link unsensible to any messager preview 
if (isset($_GET['link']))
{
  //Doing request to DB with given link
  $mysqli="SELECT * FROM `messages` WHERE link='".(trim(htmlspecialchars($_GET['link'])))."'";
  $data=$mysqli_dbh->query($mysqli, PDO::FETCH_ASSOC);
  $count=$data->rowCount();
  //Does this link exists at all
  if ($count <= 0)
  {
    ?>
    <div class="bg-light p-5 rounded">
      <div class="col-sm-8 mx-auto">
        <h1>Упс! Проблема:</h1>
          <p>Таке посилання просто не icнує. 🤷</p>
          <p><a href="<?php echo("https://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);?>">Повернутися на головну сторiнку</a></p>
      </div>
    </div>
    <?php
    die();
  }
  //collecting all necessary data from DB about this link
  foreach ($data as $key2 => $result)
  {
    $encrypted_text=$result['message'];
    $token=$result['token'];
    $lifetime=$result['lifetime'];
    $created=$result['created'];
    $id=$result['id'];
    $link=$result['link'];
  }
  //Checking does the link is still valid
  if (($created+$lifetime) < time())
  {
    ?>
    <div class="bg-light p-5 rounded">
      <div class="col-sm-8 mx-auto">
        <h1>Упс! Проблема:</h1>
          <p>Строк життя посилання закiнчився. Воно бiльше не доступно, як i даннi, що були там.</p>
          <p><a href="<?php echo("https://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);?>">Повернутися на головну сторiнку</a></p>
      </div>
    </div>
    <?php
    $mysqli="DELETE FROM `messages` WHERE id='".$id."'";
    $mysqli_dbh->query($mysqli, PDO::FETCH_ASSOC);
    die();
  }
  //If "open" value not sent, just generating Show secure message button 
  if (!isset($_POST['open']))
  {
    ?>
    <div class="bg-light p-5 rounded">
      <div class="col-sm-8 mx-auto">
        <h4>Пiдтвердiть вiдкриття посилання.<br>Пiсля цього воно буде показано один раз и видалено:</h4>
          <p>
            <form method="POST" action="">
              <button class="btn-lg btn-primary" style="align: center; width: 300px;" type="submit" name="open">Вiдкрити посилання</button>
            </form>
          </p>
      </div>
    </div>
    <?php
    die();
  }
  //if "open" value is set, showing up encrypted message from this link
  if (isset($_POST['open']))
  {
    $ciphering = "AES256";
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    $encryption_iv = mb_substr($token,0,6).mb_substr($token,0,6)."1467";
    $encryption_key = $key;
    $decrypted_text = openssl_decrypt($encrypted_text, $ciphering, $encryption_key, $options, $encryption_iv);
    ?>
    <div class="bg-light p-5 rounded">
      <div class="col-sm-8 mx-auto">
        <h1>Ось текст що був зашифрован:</h1>
          <h3><pre><?php echo($decrypted_text);?></pre></h3>
          <a href="closeWindow();">Закрити вкладку</a>
      </div>
    </div>
    <?php
    $mysqli="DELETE FROM `messages` WHERE id='".$id."'";
    $mysqli_dbh->query($mysqli, PDO::FETCH_ASSOC);
    $mysqli="INSERT INTO `msglogs` (`msgid`,`msglink`,`ip`) VALUES ('".$id."','".$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"]."/?link=".$link."','".$_SERVER['REMOTE_ADDR']."');";
    $mysqli_dbh->query($mysqli, PDO::FETCH_ASSOC);
    die();
  }
}
//generating salt and cookie for every new page
$salt=rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
$token=$salt.":".MD5($salt.":".$key);
setcookie("CSRF", $token, time() + 600, "/");
?>
  <div id="global-block" style="width: 95vw; height: 95vh; padding-left: 3rem; float: center; postion: relative;">
    <div id="top-block" style="margin-top: 10px; text-align: center; postion: absolute; padding-right: 1rem;">
      <h1 class="fw-bold lh-1 mb-3">Безпечна вiдправка будь яких текстових данних:</h1>
      <p class="col-lg-101 fs-51" style="text-align: center;">введiть вашi важливi даннi i отримайте одноразове посилання на них. Подiлiться цим посиланням з будь ким, будь яким методом. Ваш спiвбесiдник зможе вiдкрити це посилання лише один раз. Даннi не передаються у вiдкритому виглядi, ви лише дiлитесь посиланням. Пiсля вiдкриття все даннi знищуються, тому це посилання не можна вiдкрити вдруге - це i є гарантiєю, що вашi секретнi даннi не були нiким перехопленi та вiдкритi.</p>
    </div>
    <div id="bottom-block" style="postion: absolute; padding-right: 1rem;">
      <form class="p-4 p-md-5 border rounded-3 bg-light" method="POST">
        <div id="main-text-field" class="form-floating mb-3">
          <textarea style="height: 300px;" class="form-control" id="textInput" name="textMain"></textarea>
          <label for="textInput">Введiть ваш текст сюди</label>
        </div>
        <div class="form-floating1 mb-1">
          <input type="number" style="width: 210px;"  class="form-control" id="hoursInput" min="0" max="24" value="1" name="timeValid">
          <label for="hoursInput">Час життя посилання(годин)</label>
        </div>
        <hr class="my-4">
        <button class="btn-lg btn-primary" style="width1: 95vw;" type="submit" name="create">Створити посилання</button>
        <input type="hidden" name="csrfToken" value="<?php echo($token);?>">
      </form>
    </div>    
  </div>
</body>
</html>
