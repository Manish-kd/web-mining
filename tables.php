<?php

$conn = mysqli_connect("localhost","root","","web");

    if (!$conn)
  {
    die('Could not connect: ' . mysqli_error());
	echo "not connected";
  }
//    echo "Great Job";
	
	
    $a = $_GET['query'];
//	$a = $a . ' ';
echo $a;

//  $query1 = "INSERT INTO names(firstname, lastname, register) values('$a','$b','$c');";
   
 // mysqli_query($conn,$query1);

  $query2 = "SELECT * FROM frontier  WHERE keywords LIKE '%$a%' and title like '%$a%' ;";
//echo $query2;
  $run  = mysqli_query($conn,$query2);

if (!$run)
{
	echo "This project is doomed";
}

  $i=0;
  $j=0;

  while($result = mysqli_fetch_assoc($run))
  {
    $title[] = $result['title'];
    $description[] = $result['description'];
    $keywords[] = $result['keywords'];
    $url[] = $result['url'];
	$hub[] = $result['hub'];
	$inlinks[] = $result['inlinks'];
    $i++;
  }


//for debugging and not for normal use
  print_r($title);
  echo "\n";
  print_r($description);
  echo "\n";
  print_r($keywords);
  echo "\n";
  print_r($i);
  print_r($url);
  echo "\n";
  print_r($hub);
  echo "\n";
  print_r($inlinks);
  echo "\n";
  
  mysqli_close($conn);

?>

<html>
<head>
  <title></title>
  <script type="text/javascript">
</script>

<style type="text/css">
  table, th, td {
    border: 1px solid black;
}

th,td{
  padding-left: 5px;
  padding-right: 5px;
}
</style>

  
</head>
<body>

<h1>THE RESULTS OF YOUR QUERY IS GIVEN BELOW: </h1>


<table style="margin-top: 20px; margin-left: 20px; ">
  <tr>
    <th>Title</th>
    <th>Description</th>
    <th>Keywords</th>
    <th>URL</th>
	<th>HUB-SCORE</th>
	<th>INLINKS-SCORE</th>
	</tr>
  <?php for($j=1; $j<=$i; $j++)
  {;
    ?>
  <tr>
    <td><?php echo $title[$j-1]; ?></td> 
    <td><?php echo $description[$j-1]; ?></td>
    <td><?php echo $keywords[$j-1]; ?></td>  
    <td><a href="<?php echo $url[$j-1]; ?>" target="_blank"><?php echo $url[$j-1]; ?></a></td>  
    <td><?php echo $hub[$j-1]; ?></td>
    <td><?php echo $inlinks[$j-1]; ?></td>

  </tr>
  <?php }; ?>
  
</table>

</body>
</html>

