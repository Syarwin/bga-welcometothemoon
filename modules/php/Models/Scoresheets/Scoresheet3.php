<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../constants.inc.php";
include_once dirname(__FILE__) . "/../../Material/Scenario3.php";


class Scoresheet3 extends Scoresheet
{
  protected int $scenario = 3;
  protected array $datas = DATAS3;
}
