<head>
<TITLE>TVS</TITLE>
<link href="https://unpkg.com/tabulator-tables@4.7.1/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.7.1/dist/js/tabulator.min.js"></script>

<?PHP
include 'functions.php';

if(isset($_REQUEST['pilot'])){
    $pilot = $connection->real_escape_string($_REQUEST['pilot']);
    $turn = getTurn();
    $date = date("Y-m-d H:i:s"); 
    $ts = getLastDBUpdate();
    echo "<h2>Pilot: $pilot history</h2>";

    $history = getPilotHistoryFromDatabase($pilot);
    $current_pilots = getPilots();
    echo createHistoryHtmlTable($pilot,$history,$current_pilots);
} else {
    echo "<h2>No information for this pilot</h2>";
}
?>
<script type="text/javascript">
var table = new Tabulator("#pilots-table", {width:"500px",layout:"fitDataTable", });
</script>

