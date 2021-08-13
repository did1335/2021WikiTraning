<?php
//001-開放政府API-Json練習
header("Content-Type:text/html; charset=utf-8");
//API doc: https://datacenter.taichung.gov.tw/swagger/api-docs/
$url="https://datacenter.taichung.gov.tw/swagger/OpenData/44ff471a-8bda-429d-b5ba-29eace7b05ed";
$opts = array('http' => array('method' => 'GET', 'header' => 'Content-type: application/x-www-form-urlencoded'));
$context = stream_context_create($opts); 
$Json = file_get_contents($url, false, $context); 
$myarray = json_decode($Json, true);
var_dump($myarray[0]); // prints array
?>

<!--003-Wikidata模組設計-->
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>

<div id='app'>
	<h1 v-if='title' v-text='message'></h1>
	<p v-if='gt0'>查詢紀錄共<span v-text='len'></span>筆</p>
	<p v-else>查無資料</p>
	<table style='text-align:center;'>
		<tr>
			<th>index</th>	
			<th>name</th>
		</tr>
		<tr v-for="(item, index) in result">
		  <td>${ index }</td>
		  <td>${ item }</td>
		</tr>
	</table>
</div>

<script>
var result=[];
class SPARQLQueryDispatcher {
	constructor( endpoint ) {
		this.endpoint = endpoint;
	}

	query( sparqlQuery ) {
		const fullUrl = this.endpoint + '?query=' + encodeURIComponent( sparqlQuery );
		const headers = { 'Accept': 'application/sparql-results+json' };

		return fetch( fullUrl, { headers } ).then( body => body.json() );
	}
}

const endpointUrl = 'https://query.wikidata.org/sparql';
const sparqlQuery = `SELECT ?__ ?__Label WHERE {
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],zh". }
  ?__ wdt:P31 wd:Q845945.
  ?__ wdt:P17 wd:Q865.
}
LIMIT 100`;

const queryDispatcher = new SPARQLQueryDispatcher( endpointUrl );
queryDispatcher.query( sparqlQuery ).then(console.log);
queryDispatcher.query( sparqlQuery ).then((val)=>{
	val['results']['bindings'].forEach(element=>
		result.push(element['__Label']['value']))}); //promise

var app = new Vue({
  el: '#app',
  delimiters: ['${', '}'],
  data: {
    message: 'Hello Vue!',
	result: result,
	title: true
  },
   computed: {
    len: function() {
      return this.result.length;
    },
	gt0: function(){
	  if(this.len>0){
	    return true;
	  } else {
        return false;
	  }		  
	},	
   }
});
</script>

<?php
//002-維基百科爬蟲
function api2($i) { 
	$url = "https://zh.wikipedia.org/wiki/%E7%BB%B4%E5%9F%BA%E7%99%BE%E7%A7%91"; 
	$opts = array('http' => array('method' => 'GET', 'header' => 'Content-type: application/x-www-form-urlencoded'));
	$context = stream_context_create($opts); 
	$result = file_get_contents($url, false, $context); 
	$first_step = explode('<table class="infobox vcard" cellspacing="3" style="border-spacing:3px;width:22em;text-align:left;font-size:small;line-height:1.5em"><caption class="fn org">' , $result); 
	$second_step = explode("</table>" , $first_step[1]); 
	
	if($i==0)
		preg_match_all('/<p[^>]*[^>]*>(.*?)<\/p?>/si', $result, $match); //所有段落
	if($i==1)
		preg_match_all('#<img[^>]*>#i', $result, $match); //所有圖片
	/*preg_match_all('/<div[^>]*id="mw-content-text"[^>]*>(.*?)<\/div?>/si', $result, $match); //id為mw-content-text之div*/

	return $match;
} 

echo '<div>圖片輸出 =>';
print_r(api2(1)[0][7]);
echo '</div>';

print_r(api2(0)); //輸出內文

print_r(api2(1)); //輸出圖片
