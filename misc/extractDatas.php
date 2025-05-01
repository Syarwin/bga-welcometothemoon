<?php

function clienttranslate($str)
{
  return $str;
}
class APP_DbObject {}

include_once '../modules/php/constants.inc.php';
$swdNamespaceAutoload = function ($class) {
  $classParts = explode('\\', $class);
  if ($classParts[2] == 'WelcomeToTheMoon') {
    array_shift($classParts);
    array_shift($classParts);
    array_shift($classParts);
    $file = dirname(__FILE__) . '/../modules/php/' . implode(DIRECTORY_SEPARATOR, $classParts) . '.php';
    if (file_exists($file)) {
      require_once $file;
    } else {
      var_dump('Cannot find file : ' . $file);
    }
  }
};
spl_autoload_register($swdNamespaceAutoload, true, true);

$maps = [];
foreach ([1, 2, 3, 4, 5] as $mapId) {
  $json = json_decode(file_get_contents("./editor/scenario-$mapId.json"));

  // Convert the array to a PHP code string with short array syntax
  $exportedArray = var_export($json, true);
  $exportedArray = preg_replace("#\\(object\\)#", "", $exportedArray);
  $exportedArray = preg_replace(['/array( )?\(/', '/\)/'], ['[', ']'], $exportedArray);

  // Create the PHP file content
  $phpCode = "<?php\nconst DATAS$mapId = " . $exportedArray . ";\n";

  $fp = fopen("../modules/php/Material/Scenario$mapId.php", 'w');
  fwrite($fp, $phpCode);
  fclose($fp);

  require_once "../modules/php/Models/Scoresheets/Scoresheet$mapId.php";
  $className = 'Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet' . $mapId;
  $map = new $className(null);
  $maps[$mapId] = $map->getUiData();
}

$fp = fopen('../modules/js/data.js', 'w');
fwrite($fp, 'const SCENARIOS_DATA = ' . json_encode($maps) . ';');
fclose($fp);
