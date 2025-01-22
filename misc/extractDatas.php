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

// function getCardInstance($id, $data = null)
// {
//   $t = explode('_', $id);
//   // First part before _ specify the type and the numbering
//   $prefixes = [
//     'A' => 'Animals',
//     'S' => 'Sponsors',
//     'P' => 'Projects',
//     'F' => 'FinalScoring',
//   ];
//   $prefix = $prefixes[$t[0][0]];

//   $file = "../modules/php/Cards/$prefix/$id.php";
//   if (file_exists($file)) {
//     require_once $file;
//     $className = "\ARK\Cards\\$prefix\\$id";
//     return new $className($data);
//   } else {
//     return null;
//   }
// }

// include_once '../modules/php/Cards/list.inc.php';

// $cards = [];
// foreach ($cardIds as $cardId) {
//   $card = getCardInstance($cardId);
//   if (!is_null($card)) {
//     $cards[$cardId] = $card->getStaticData();
//   }
// }

$maps = [];
foreach ([1] as $mapId) {
  require_once "../modules/php/Models/Scoresheets/Scoresheet$mapId.php";
  $className = 'Bga\Games\WelcomeToTheMoon\Models\Scoresheets\Scoresheet' . $mapId;
  $map = new $className(null);
  $maps[$mapId] = $map->getUiData();
}

$fp = fopen('../modules/js/data.js', 'w');
// fwrite($fp, 'const CARDS_DATA = ' . json_encode($cards) . ';');
fwrite($fp, 'const SCENARIOS_DATA = ' . json_encode($maps) . ';');
fclose($fp);
