<head>
<TITLE>TVS</TITLE>
<link href="https://unpkg.com/tabulator-tables@4.7.1/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.7.1/dist/js/tabulator.min.js"></script>

<?PHP
include 'functions.php';
$turn = getTurn();
$date = date("Y-m-d H:i:s"); 
$ts = getLastDBUpdate();
echo "<h2>Info for turn: $turn at ($date) Diffs compare to ($ts)</h2>";

$dbpilots = getPilotsFromDatabase($ts);

$pilots = getPilots();
echo createRankingHtmlTable($pilots,$dbpilots);
?>
<script type="text/javascript">
var table = new Tabulator("#pilots-table", {width:"500px",layout:"fitDataTable", columns:[
{title:"Rank", field:"number", formatter:"number"},
{title:"Pilot", field:"url_label", formatter:"link", formatterParams:{url:function(cell){return "detail.php?pilot=" + cell.getValue();}}},
 ],});
</script>

