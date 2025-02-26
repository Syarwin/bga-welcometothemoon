<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../Material/Scenario4.php";

class Scoresheet4 extends Scoresheet
{
  protected int $scenario = 4;
  protected array $datas = DATAS4;
}
