<?php
require_once 'pdoconfig.php';

function openCon()
{
    $dbhost = DBHOST;
    $dbuser = DBUSER;
    $dbpass = DBPASS;
    $dbname = DBNAME;
    try
    {
        $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        return $e->getMessage();
    }
    return $pdo;
}
function closeCon($pdo)
{
    $pdo = null;
}

$page = isset($_GET['page']) && $_GET['page'] > 0? $_GET['page'] : 1;
$count = 0;
$limit = ($page - 1) * PGSIZE + 1;
$offset = PGSIZE;

?>
<html>
<head>
  <title>Pàgina d'exemple consulta de municipis <?php echo ALUMNE; ?> </title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  
  <style>
  html, body, .container {
    box-sizing: border-box; 
    height: 100%;
    height: -webkit-fill-available; // Chrome
    margin: 0;
  }
  	
  body {
    /*position: relative;
    height: auto;
    min-height: 100% !important;*/
  }
  
  #table-container {
    max-height: 400px;
    overflow: auto;
    display:inline-block;
  }
  </style>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-sm">
        <div class="media">
          <div class="media-body">
            <h1>Consulta de municipis</h1>
            <h2 class="text-muted"><?php echo ALUMNE; ?></h2>
          </div>
          <img class="align-self-center mr-3" width="100px" src="logo_insmarianao.jpg" alt="Institut Marianao">
        </div> 
      </div> 
    </div>  
  <div class="row">
     <div class="col-sm">
  <?php
$pdo = openCon();
if ($pdo instanceof PDO)
{
    echo "<div class='alert alert-success' role='alert'>Connexió correcte</div>";
}
else
{
    echo "<div class='alert alert-danger' role='alert'>Error a la connexió ".$pdo. "</div>";
}
?>
    </div>
  </div> 
  <div class="row">
    <div id="table-container" class="col-sm table-responsive">
      <table  class="table table-striped table-bordered overflow-auto">
        <thead class="thead-dark">
          <tr>
            <th scope="col">#</th>
            <th scope="col">Nom</th>
            <th scope="col">Comarca</th>
            <th scope="col">Privincia</th>
            <th scope="col">CP</th>
          </tr>
        </thead>
      <tbody>
 <?php
try
{
    if ($pdo instanceof PDO)
    {
        $query = $pdo->prepare("SELECT * FROM municipis WHERE municipi IS NOT NULL ORDER BY municipi, provincia ASC LIMIT :limit,:offset");
        $query->bindParam('limit', $limit, PDO::PARAM_INT);
        $query->bindParam('offset', $offset, PDO::PARAM_INT);
        $query->execute();

	$count = $query->rowCount();
	while ($municipi = $query->fetch())
        {
?>
          <tr>
            <th scope="row"><?php echo $municipi['id']; ?> </th>
            <td><?php echo $municipi['municipi']; ?></td>
            <td><?php echo $municipi['comarca']; ?></td>
            <td><?php echo $municipi['provincia']; ?></td>
            <td><?php echo $municipi['cp']; ?></td>
          </tr>
<?php
        }
        
        closeCon($pdo); 
    }
}
catch(PDOException $err)
{
    error_log("PDOException" . $err->getMessage());
}
catch(Exception $ex)
{
    error_log("Exception" . $ex->getMessage());
}

?>  
      </table>
    </div> 
  </div> 
  <div class="row">
    <div class="col-sm">
      <nav>
        <ul class="pagination">
          <li class="page-item <?php echo ($page - 2 > 0? '' : 'disabled'); ?>">
            <a class="page-link" href="?page=<?php echo $page - 2; ?>" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
              <span class="sr-only">Previous</span>
            </a>
          </li>
<?php if ($page - 1 > 0) { ?>              
          <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>"><?php echo $page - 1; ?></a></li>
<?php } ?>              
          <li class="page-item active"><a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a></li>
<?php if ($count == PGSIZE) { ?>  
          <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>"><?php echo $page + 1; ?></a></li>
<?php } 
      if ($count == PGSIZE) { ?>  
          <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 2; ?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
            <span class="sr-only">Next</span>
          </a></li>
<?php } ?>         
        </ul>
      </nav>
    </div> 
  </div> 
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

