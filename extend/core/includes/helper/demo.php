include('user_agent.php');
 
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$ua = new CI_User_agent($user_agent);
 
echo $ua->platform() . '<br>';
echo $ua->browser() . '<br>';
echo $ua->version() . '<br>';
echo $ua->robot() . '<br>';
echo $ua->mobile() . '<br>';