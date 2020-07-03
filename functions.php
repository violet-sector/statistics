<?PHP
ini_set("allow_url_fopen", 1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("dblogon.php");

$legions = array("None","Ajaxian", "Boraxian", "Krilgorian", "Tibran","Rogue");
$shiptypes = array("None",
                "Planet",
                "Goliath Mark II",
                "Flight Of Independence",
                "Fanged Fighter",
                "Golden Eagle",
                "Microw Fighter",
                "Demon Light Attacker",
                "Black Knight",
                "The Stinger",
                "Starship Fighter",
                "Sonic Speed Fighter",
                "Eagle Of Tunardia",
                "Shadow",
                "Cloud Of Death",
                "Mirage Mk III",
                "Galactic Bomber Alpha",
                "Hercules Bomber",
                "Blue Bird Bomber",
                "Boraxian Bomber",
                "Dark Speed Bomber",
                "Single Seated Tibran Bomber",
                "Repair Ship",
                "Repair Ship",
                "Repair Ship",
                "Repair Ship",
                "Carrier",
                "Carrier",
                "Carrier",
                "Carrier",
                "Cruiser",
                "Cruiser",
                "Cruiser",
                "Cruiser"
                );

function searchForPilot($id, $array) {
   #echo $id;
   foreach ($array as $key => $val) {
       if ($val['tvs_username'] === $id) {
           return $key;
       }
   }
   return null;
}

function getTurn() {
    $json_time = file_get_contents("https://www.violetsector.com/json/timer.php");
    $time = json_decode($json_time, true);
    return $time['tick'];
}

function getPilots() {
    $json = file_get_contents("https://www.violetsector.com/test.php");
    $pilots = json_decode($json, true);
    return $pilots['rankings_pilots'];
}

function getLastDBUpdate() {
    global $connection;
    $sql = "select ts from tvs order by ts DESC LIMIT 1";
    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));

    $time =mysqli_fetch_assoc($result);
    return $time['ts'];
}

function getPilotHistoryFromDatabase($pilot) {
    global $connection;
    $pilot = $connection->real_escape_string($pilot);
    //fetch table rows from mysql db
    $sql = "select * from tvs where tvs_username = '$pilot'";
    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));

    //create an array
    $history = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $history[] = $row;
    }
    return $history;
}

function getPilotsFromDatabase($ts) {
    global $connection;
    //fetch table rows from mysql db
    $sql = "select * from tvs where ts = '$ts' order by ts ASC";
    $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));

    //create an array
    $pilots = array();
    while($row =mysqli_fetch_assoc($result))
    {
        $pilots[] = $row;
    }
    return $pilots;
}

function createHistoryHtmlTable($pilot, $data, $json_data) {
    global $legions;
    global $shiptypes;
    $pilot_index = searchForPilot($pilot,$json_data);
    $current_info = $json_data[$pilot_index];
    #pop current json data on top as first entry
    #print_r($current_info);
    #array_unshift($data , $current_info);
    $tableheader= "<table id=\"pilots-table\">
        <thead>
        <tr>
            <tr>
                <th>Turn</th>
                <th>Rank</th>
                <th>Pilot</th>
                <th>Legion</th>
                <th>Level</th>
                <th>Hp</th>
                <th>HP Diff</th>
                <th>MaxHP</th>
                <th>Ship</th>
                <th>Score</th>
                <th>Score Diff</th>
                <th>Kills</th>
                <th>Kills Diff</th>
                <th>Deaths</th>
                <th>Deaths Diff</th>
                <th>Online</th>
                <th>Snapshot</th>
            </tr>
        </thead><tbody>";
    $prev_value = null;
    $tablehtml="";
    foreach ($data as $value) {
            $score_diff =0;
            $hp_diff  =0;
            $dead_diff =0;
            $kill_diff =0;
            if ($prev_value != null) { 
                $score_diff = $value['score']-$prev_value['score'];
                $hp_diff = $value['hp']-$prev_value['hp'];
                $dead_diff = $value['deaths']-$prev_value['deaths'];
                $kill_diff = $value['kills']-$prev_value['kills'];
            }
            $tablehtml="<tr>
                <td>".$value['turn']."</td>
                <td>".$value['rank']."</td>
                <td>".htmlentities($value['tvs_username'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".$legions[$value['legion']]."</td>
                <td>".$value['level']."</td>
                <td>".$value['hp']."</td>
                <td>".$hp_diff."</td>
                <td>".$value['maxhp']."</td>
                <td>".$shiptypes[$value['ship']]."</td>
                <td>".$value['score']."</td>
                <td>".$score_diff."</td>
                <td>".$value['kills']."</td>
                <td>".$kill_diff."</td>
                <td>".$value['deaths']."</td>
                <td>".$dead_diff."</td>
                <td>".$value['online']."</td>
                <td>".$value['ts']."</td>
            </tr>".$tablehtml;
            $prev_value = $value;
    }
    # at last print current info
    $value = $current_info;
    $turn = getTurn();
    $rank = $pilot_index+1;
    $ts = date("Y-m-d H:i:s"); 
    if ($prev_value != null) { 
        $score_diff = $value['score']-$prev_value['score'];
        $hp_diff = $value['hp']-$prev_value['hp'];
        $dead_diff = $value['deaths']-$prev_value['deaths'];
        $kill_diff = $value['kills']-$prev_value['kills'];
    }
    $tablehtml="<tr>
                <td>".$turn."</td>
                <td>".$rank."</td>
                <td>".htmlentities($value['tvs_username'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".$legions[$value['legion']]."</td>
                <td>".$value['level']."</td>
                <td>".$value['hp']."</td>
                <td>".$hp_diff."</td>
                <td>".$value['maxhp']."</td>
                <td>".$shiptypes[$value['ship']]."</td>
                <td>".$value['score']."</td>
                <td>".$score_diff."</td>
                <td>".$value['kills']."</td>
                <td>".$kill_diff."</td>
                <td>".$value['deaths']."</td>
                <td>".$dead_diff."</td>
                <td>".$value['online']."</td>
                <td>".$ts."</td>
            </tr>".$tablehtml;
    $tablefooter="</tbody></table>";
    $html= $tableheader.$tablehtml.$tablefooter;
    return $html;
}


function createRankingHtmlTable($data, $db_data) {
    global $legions;
    global $shiptypes;
    $rank=1;
    $tablehtml= "<table id=\"pilots-table\">
        <thead>
        <tr>
            <tr>
                <th>Rank</th>
                <th>Pilot</th>
                <th>Legion</th>
                <th>Level</th>
                <th>Hp</th>
                <th>HP Diff</th>
                <th>MaxHP</th>
                <th>Ship</th>
                <th>Score</th>
                <th>Score Diff</th>
                <th>Kills</th>
                <th>Kills Diff</th>
                <th>Deaths</th>
                <th>Deaths Diff</th>
                <th>Online</th>
            </tr>
        </thead><tbody>";
    foreach ($data as $value) {
            $db_pilot_data = searchForPilot($value['tvs_username'],$db_data);
            $score_diff =0;
            $hp_diff  =0;
            $dead_diff =0;
            $kill_diff =0;
            if ($db_pilot_data != null && array_key_exists ($db_pilot_data,$db_data)) { 
                $pilot = $db_data[$db_pilot_data];
                $score_diff = $value['score']-$pilot['score'];
                $hp_diff = $value['hp']-$pilot['hp'];
                $dead_diff = $value['deaths']-$pilot['deaths'];
                $kill_diff = $value['kills']-$pilot['kills'];
            }
            $tablehtml.="<tr>
                <td>".$rank."</td>
                <td><a href='detail.php?pilot=".urlencode($value['tvs_username'])."'>".htmlentities($value['tvs_username'], ENT_QUOTES, 'UTF-8')."</a></td>
                <td>".$legions[$value['legion']]."</td>
                <td>".$value['level']."</td>
                <td>".$value['hp']."</td>
                <td>".$hp_diff."</td>
                <td>".$value['maxhp']."</td>
                <td>".$shiptypes[$value['ship']]."</td>
                <td>".$value['score']."</td>
                <td>".$score_diff."</td>
                <td>".$value['kills']."</td>
                <td>".$kill_diff."</td>
                <td>".$value['deaths']."</td>
                <td>".$dead_diff."</td>
                <td>".$value['online']."</td>
            </tr>";
            $rank++;
    } 
    $tablehtml.="</tbody></table>";
    return $tablehtml;
}

?>
