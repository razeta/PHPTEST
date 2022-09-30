<?php include "FileLoader.php"; ?>

<html>
	<head>
		
		<link rel="stylesheet" href="style.css">
		
	</head>
	
	<body>

		<p>Search for author name:</p>
		
		<form id="searchform" method="post">
			<input id="searchbox" type="search" name="searchbox" value="<?php if(array_key_exists('searchbox', $_POST)) echo $_POST['searchbox'];?>" />
			<input id="searchbutton" type="button"
					class="button" value="Search" onClick="searchData()"/>			  		
		</form>
		<input id="clearresutls" type="button"
					class="button" value="Clear" onClick="clearData()"/>
		
		<div id="datatable">
			<table id="bookstable">
				<tr>
					<th>ID</th>
					<th>Author</th>
					<th>Name</th>
				</tr>
				
				<tbody id="rows">

				</tbody>
			  
			</table>
		</div>
		
		<script>	

		function clearData(){
			cleanDiv('result');
			var rows = document.getElementById("rows");
			rows.innerHTML = "";
		}
			
		function searchData() {
			
			cleanDiv('result');
			
			var searchtext = document.getElementById("searchbox").value;
			
			var searchbox = '{"author" : "'+searchtext+'"}';
			var table = "books.author";
			var jointable = "books.book";
			
			var rows = document.getElementById("rows");
			var totalRowCount = rows.rows.length;			

			if(totalRowCount == 0){
				var xhttp = new XMLHttpRequest();
				xhttp.open("GET", "AjaxCalls.php?searchbox="+searchbox+"&table="+table+"&jointable="+jointable, true);
				xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhttp.onreadystatechange = function() {
					
					if (this.readyState == 4 && this.status == 200) {	
					
						
							// Response
							var response = this.responseText; 
							
							//var bookstable = document.getElementById("bookstable");					
							const obj = JSON.parse(response);							
					
							
							for(var k in obj) {
								//console.log(obj[k]['author']);
								
								var newRow = document.createElement("tr");
								newRow.classList.toggle("bookslide-in");
								//newRow.classList.add("bookslide-in");
								
								var newCellId = document.createElement("td");
								var newCellAuthor = document.createElement("td");
								var newCellName = document.createElement("td");

								newCellId.innerHTML = obj[k]['id'];
								newCellAuthor.innerHTML = obj[k]['author'];
								newCellName.innerHTML = obj[k]['name'];

								newRow.append(newCellId);
								newRow.append(newCellAuthor);
								newRow.append(newCellName);

								document.getElementById("rows").appendChild(newRow);
							} 
						
						
						//document.getElementById("searchbox").value = searchtext;
					
						//bookstable.append(response);
						//console.log(response);
						//console.log(obj);

					}
				};
				xhttp.send();
			}else{
				cleanTable(totalRowCount, rows);
				
			}				
	
		}
		
		function cleanTable(totalRowCount, rows){
			var i = 0;

			setTimeout(() => {  removeRow(totalRowCount, rows, i);}, 100);
		}	

		function removeRow(totalRowCount, rows, i){					
						
			rows.rows[i].classList.toggle("bookslide-in");
			rows.rows[i].classList.toggle("bookslide-out");
			i++;	
			console.log("i="+i);	
			console.log("totalRowCount="+totalRowCount);	

			if( i < totalRowCount){
				setTimeout(() => {  removeRow(totalRowCount, rows, i);}, 100);	
			}else{
				setTimeout(() => {  rows.innerHTML = ''; searchData(); }, 2000);	
				
			}
		}
		
		function cleanDiv(divId){
			var div = document.getElementById(divId);
			div.innerHTML = "";
			
		}
		</script>

		<p>Load Data:</p>
			
		<form method="post">
			<input type="submit" name="loaddata"
					class="button" value="Data Load" onClick="cleanDiv('result')" />			  		
		</form>			
		
		<div id="result">		
			<?php
				if(array_key_exists('loaddata', $_POST)) {
					
					FileLoader::dirloader();
									
				}
			phpinfo();				
			?>
		</div>
		
		
	</body>
</html>
	
	

	
	
	
	
	
	