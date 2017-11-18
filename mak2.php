<?php
// THIS PROJECT IS FOR WEB MINING J COMPONENT.
// THIS CODE IS USED TO CRAWL THE WEB PAGE AND EXTRACT INFORMATION.
// ITS A TROPICAL CRAWLER.
// IT FOLLOWS BREADTH FIRST ALGORITHM.

$start = "https://www.w3schools.com/";
//$start = "https://www.tutorialspoint.com/";
//$start = "https://www.csstutorial.net/";
//$start = "https://www.codeschool.com/";
//$start = "https://www.codecademy.com/";
//$start = "https://www.theodinproject.com/";
//$start = "http://www.guru99.com/";
//$start = "https://www.lynda.com/html/";
//$start = "https://learn.sparkfun.com/tutorials/";

//DATABSE INITIALISATION...
 $dbhost="localhost";
 $dbuser="root";
 $dbpass="";
 $dbname="web";
 $connection=mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
  if(mysqli_connect_errno()){
    die("Database connection failed:"
      );
  }
 //$query="INSERT INTO frontier(title,description,keywords,url) values('mak','mak','mak','mak.com');";
	
$already_crawled = array();
$crawling = array();


// THIS FUNCTION EXTRACTS THE DETAIL OF THE URL GIVEN TO IT....
function get_details($url,$hub,$inlinks) {

	global $connection;
	$options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: MAKBOT/1.0\n"));
	$context = stream_context_create($options);
	$doc = new DOMDocument();
	@$doc->loadHTML(@file_get_contents($url, false, $context));

	//EXTRACTING TITLE, DESCRIPTION AND KEYWORDS...
	$title = $doc->getElementsByTagName("title");
	$title = $title->item(0)->nodeValue;
	$description = "";
	$keywords = "";
	
	
	$metas = $doc->getElementsByTagName("meta");
	for ($i = 0; $i < $metas->length; $i++) {
		$meta = $metas->item($i);
		if (strtolower($meta->getAttribute("name")) == "description")
			$description = $meta->getAttribute("content");
		if (strtolower($meta->getAttribute("name")) == "keywords")
			$keywords = $meta->getAttribute("content");
	}
	
	//$title     = mysql_real_escape_string($title);
	//$description     = mysql_real_escape_string($description);
	//$keywords     = mysql_real_escape_string($keywords);
	//$url     = mysql_real_escape_string($url);

	//FOR DEBUGGING...
	echo "$title</br>";
	echo "$description</br>";
	echo "$keywords</br>";
	echo "$url</br>";

	//DATABASE ACCESSING AND INSERTION...
	$query="INSERT INTO frontier(title,description,keywords,url,hub,inlinks) values('{$title}','{$description}','{$keywords}','{$url}','{$hub}','{$inlinks}');";
	//$query="INSERT INTO frontier(title,description,keywords,url) values($title,$description,$keywords,$url);";
	$result=mysqli_query($connection,$query);
    if($result)
      $contact="success";
    else
      $contact="failure";
	echo "$contact.<p></p>";
 	
	//RETURNING THE TITLE, DESCRIPTION AND KEYWORDS...
	return '{ "Title": "'.str_replace("\n", "", $title).'", "Description": "'.str_replace("\n", "", $description).'", "Keywords": "'.str_replace("\n", "", $keywords).'", "URL": "'.$url.'"},';
}

// THIS FUNCTION EXTRACTS ALL THE LINKS FROM THE URL PROVIDED TO IT.
function follow_links($url) {
	global $connection;
	global $already_crawled;
	global $crawling;
	$hub = 0;
	$options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: MAKBOT/1.0\n"));
	$context = stream_context_create($options);
	$doc = new DOMDocument();
	@$doc->loadHTML(@file_get_contents($url, false, $context));
	$linklist = $doc->getElementsByTagName("a");
	foreach ($linklist as $link) {
		
		// SAVING THE LINK OR URL IN $L VARIABLE...
		$l =  $link->getAttribute("href");
		$hub = $hub + 1;
		
		// EDITING THE LINK FOR RUNING IT IN THE BROWSER....
		if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
		} else if (substr($l, 0, 2) == "//") {
			$l = parse_url($url)["scheme"].":".$l;
		} else if (substr($l, 0, 2) == "./") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
		} else if (substr($l, 0, 1) == "#") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
		} else if (substr($l, 0, 3) == "../") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
		} else if (substr($l, 0, 11) == "javascript:") {
			continue;
		} else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
			$l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
		}
		
		$inlinks = 1;
		// CHECKING FOR ALREADY CRAWLED. NOTE: A DATABASE WILL BE CREATED ON THE LOCALHOST TO STORE ALL THE URLS WHICH ARE ALREADY CRAWLED, OR IT WILL CHECK FROMTHE RANKING TABLE.
		if (!in_array($l, $already_crawled)) {
				$already_crawled[] = $l;
				$crawling[] = $l;
				echo get_details($l,$hub,$inlinks)."<p></p>";
		}
		else{
			  $query2 = "SELECT * FROM frontier WHERE url LIKE '$l';";
			//echo $query2;
				$run  = mysqli_query($connection,$query2);

				if (!$run)
				{
					echo "This project is doomed";
				}

				while($result = mysqli_fetch_assoc($run))
				{
					$inlinks = $result['inlinks'];
					$inlinks = $inlinks + 1;
					echo " chutiya manish $inlinks";
					$sql = "UPDATE frontier SET inlinks='$inlinks' WHERE url like '%l'";

					if (mysqli_query($connection, $sql)) {
						echo "Record updated successfully";
					} else {
						echo "Error updating record: " . mysqli_error($connection);
					}
				}

		}
	}
	array_shift($crawling);
	foreach ($crawling as $site) {
		follow_links($site);
	}
}
follow_links($start);
?>

