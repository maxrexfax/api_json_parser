<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>PHP api json parser</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.37.1" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<style>
    .search-container {
            border: 1px solid black;
            padding: 10px;
            margin: 10px;
            display:none;
        }
</style>
</head>

<body>
<div class="row">
    <div class="col-6 col-md-8 col-lg-6 offset-2 offset-md-2 offset-lg-4">
        <form method="GET" action="autoservice.php">
            <div class="form-group">
                <label for="post_index">Postal code</label>
                <input type="text" class="form-control" value="" id="post_index" name="post_index" placeholder="Enter postal">
                <small id="postalHelp" class="form-text text-muted">Find info by post code</small>
            </div>
            <button type="submit" id="find_by_postal" class="btn btn-primary">Find</button>
        </form>
    </div>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['post_index'])) {
    doMainWork($_GET['post_index']);
}

function doMainWork($post_code) {
    $indexOfPostal;
    $arrayOfIndexesOfServices = [];
    $json = file_get_contents('http://pony.codevery.work:8450/');
    $objWithData = json_decode($json);
    $postIndexes = array_column($objWithData->region_mappings_string, 'postal_code');
    $arrayWithServiceGroups = $objWithData->auto_service;
    $arrayWithServicePostals = $objWithData->region_mappings_string;
    $indexOfPostal = array_search($post_code, $postIndexes);
    
    for ($i = 0; $i < count($arrayWithServiceGroups); $i++) {
        $tmpArrayOfCodes = explode(",", $arrayWithServiceGroups[$i]->region_codes);
        
        for ($j = 0; $j < count($tmpArrayOfCodes); $j++) {
            if($arrayWithServicePostals[$indexOfPostal]->region_code == $tmpArrayOfCodes[$j]) {
                array_push($arrayOfIndexesOfServices, $i);
                break;
            }
        }
    }
    echo '<div id="searchResult" class="col-8 col-md-6 col-lg-6 offset-2 offset-md-2 offset-lg-4 text-center">';
    
    if(isset($arrayOfIndexesOfServices)){
        echo '<div><select id="selector" class="form-control">';
        for($i = 0; $i < count($arrayOfIndexesOfServices); $i++) {
            echo '<option value="' . $arrayOfIndexesOfServices[$i] . '">' . $arrayWithServiceGroups[$arrayOfIndexesOfServices[$i]]->franchise_name . '</option>';
        }
        echo '</select></div>';
        for ($i = 0; $i < count($arrayOfIndexesOfServices); $i++) {
            echo '<div class="search-container" id="' . $arrayOfIndexesOfServices[$i] . '">';
            echo '<div><h2 id="serviceFranchaseName">' . $arrayWithServiceGroups[$arrayOfIndexesOfServices[$i]]->franchise_name . '</h2></div>';
            echo '<div id="servicePhone">' . $arrayWithServiceGroups[$arrayOfIndexesOfServices[$i]]->phone . '</div>';
            echo '<div id="serviceEmail">' . $arrayWithServiceGroups[$arrayOfIndexesOfServices[$i]]->email . '</div>';
            echo '<div id="serviceWebsite">' . $arrayWithServiceGroups[$arrayOfIndexesOfServices[$i]]->website . '</div>';
            echo '<div id="serviceInfo">' . $arrayWithServicePostals[$indexOfPostal]->postal_code . ',' .
        $arrayWithServicePostals[$indexOfPostal]->city . ',' . $arrayWithServicePostals[$indexOfPostal]->region.
        ','. $arrayWithServicePostals[$indexOfPostal]->state . '</div>';
            echo '<div id="serviceImage" class="w-100"><img id="img_service" class="w-100" src="http://pony.codevery.work:8450' . $arrayWithServiceGroups[$arrayOfIndexesOfServices[$i]]->images . '" /></div></div>';
        }
        echo '</div>';
        echo '<script>let arrayOfContainers = $(".search-container"); $(arrayOfContainers[0]).show();</script>';
    }     
}
?>    
</div>
<script>
    $(document).ready(function() {
        $("#selector").change(function() {
            $(".search-container").hide();
            $('#' + [$(this).val()]).show()
        });
    });
</script>
</body>

</html>
