<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader, ['debug' => true]);

if (isset($_GET['post_index'])) {
    doMainWork($_GET['post_index'], $twig);
} else {
    echo $twig->render('index.html.twig');
}

function doMainWork($post_code, $twig) {
    $arrayOfFoundServicesId = [];
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
                array_push($arrayOfFoundServicesId, $i);
                break;
            }
        }
    }

    echo $twig->render('result.html.twig', [
        'arrayOfFoundServicesId' => $arrayOfFoundServicesId,
        'arrayOfFoundServiceGroups' => $arrayWithServiceGroups,
        'arrayWithServicePostals' => $arrayWithServicePostals,
        'indexOfPostal' => $indexOfPostal,

    ]);

    /*echo '<div id="searchResult" class="col-8 col-md-6 col-lg-6 offset-2 offset-md-2 offset-lg-4 text-center">';
    
    if(isset($arrayOfFoundServicesId)){
        echo '<div><select id="selector" class="form-control">';
        for($i = 0; $i < count($arrayOfFoundServicesId); $i++) {
            echo '<option value="' . $arrayOfFoundServicesId[$i] . '">' . $arrayWithServiceGroups[$arrayOfFoundServicesId[$i]]->franchise_name . '</option>';
        }
        echo '</select></div>';
        for ($i = 0; $i < count($arrayOfFoundServicesId); $i++) {
            echo '<div class="search-container" id="' . $arrayOfFoundServicesId[$i] . '">';
            echo '<div><h2 id="serviceFranchaseName">' . $arrayWithServiceGroups[$arrayOfFoundServicesId[$i]]->franchise_name . '</h2></div>';
            echo '<div id="servicePhone">' . $arrayWithServiceGroups[$arrayOfFoundServicesId[$i]]->phone . '</div>';
            echo '<div id="serviceEmail">' . $arrayWithServiceGroups[$arrayOfFoundServicesId[$i]]->email . '</div>';
            echo '<div id="serviceWebsite">' . $arrayWithServiceGroups[$arrayOfFoundServicesId[$i]]->website . '</div>';
            echo '<div id="serviceInfo">' . $arrayWithServicePostals[$indexOfPostal]->postal_code . ',' .
        $arrayWithServicePostals[$indexOfPostal]->city . ',' . $arrayWithServicePostals[$indexOfPostal]->region.
        ','. $arrayWithServicePostals[$indexOfPostal]->state . '</div>';
            echo '<div id="serviceImage" class="w-100"><img id="img_service" class="w-100" src="http://pony.codevery.work:8450'
     . $arrayWithServiceGroups[$arrayOfFoundServicesId[$i]]->images . '" /></div></div>';
        }
        echo '</div>';
        echo '<script>let arrayOfContainers = $(".search-container"); $(arrayOfContainers[0]).show();</script>';
    }  */
}
?>
