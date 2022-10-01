<?php include "FileLoader.php"; ?>

<html>
	<head>
		
		<link rel="stylesheet" href="style.css">
		
	</head>
	
	<body>

		<h1>BOOKS STORE</h1>
		
		<p>Search for author name:</p>
		
		<form id="searchform" method="post">
			<input id="searchbox" type="search" name="searchbox" value="<?php if(array_key_exists('searchbox', $_POST)) echo $_POST['searchbox'];?>" />
			<input id="searchbutton" type="button"
					class="button" value="Search" onClick="searchData()"/>			  		
		</form>
		<input id="clearresutls" type="button"
					class="button" value="Clear search" onClick="clearData()"/>
		
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

		//Clear data from table, div and searchbox
		function clearData(){
			
			//Clean resutl div
			cleanDiv('result');
			
			//Clean rows in table
			var rows = document.getElementById("rows");
			rows.innerHTML = "";
			
			//Clean searchbox
			document.getElementById("searchbox").value = "";			

		}
			
		//Search data Ajax call
		function searchData() {
			
			//Clean resutl div
			cleanDiv('result');
			
			//Get search value
			var searchtext = document.getElementById("searchbox").value;
			
			//Local var for Ajax call
			var searchbox = '{"author" : "'+searchtext+'"}';
			var table = "books.author";
			var jointable = "books.book";
			
			//Get rows and count them
			var rows = document.getElementById("rows");
			var totalRowCount = rows.rows.length;			

			//If rows number is not 0 then start adding new rows
			if(totalRowCount == 0){
				
				//Ajax call to AjaxCalls.php with searchbox, table and jointable parameters
				var xhttp = new XMLHttpRequest();
				xhttp.open("GET", "AjaxCalls.php?searchbox="+searchbox+"&table="+table+"&jointable="+jointable, true);
				xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhttp.onreadystatechange = function() {
					
					if (this.readyState == 4 && this.status == 200) {	
					
						
							// Response
							var response = this.responseText; 
							
							//Transform JSON response to Object				
							const obj = JSON.parse(response);							
					
							//Iterate response records
							for(var k in obj) {
								
								//Create table row and add CSS for in animation. This is a block animation.
								var newRow = document.createElement("tr");
								newRow.classList.toggle("bookslide-in");
								
								//Create new cell's
								var newCellId = document.createElement("td");
								var newCellAuthor = document.createElement("td");
								var newCellName = document.createElement("td");

								//Add data to cell's
								newCellId.innerHTML = obj[k]['id'];
								newCellAuthor.innerHTML = obj[k]['author'];
								newCellName.innerHTML = obj[k]['name'];
								
								//Add cell's to row
								newRow.append(newCellId);
								newRow.append(newCellAuthor);
								newRow.append(newCellName);

								//Add new row
								document.getElementById("rows").appendChild(newRow);
							} 
					
						//console.log(response);
						//console.log(obj);

					}
				};
				xhttp.send();
			}else{
				//Clean table if there are rows
				cleanTable(totalRowCount, rows);
				
			}				
	
		}
		
		//Clean table function
		function cleanTable(totalRowCount, rows){
			var i = 0;
			
			//wait before runing remove row function.
			setTimeout(() => {  removeRow(totalRowCount, rows, i);}, 100);
		}	

		//Remove row function and add animation
		function removeRow(totalRowCount, rows, i){					
			
			//Turn on and off CSS animations
			rows.rows[i].classList.toggle("bookslide-in");
			rows.rows[i].classList.toggle("bookslide-out");
			
			//row counter for recursive funciton
			i++;
			
			//console.log("i="+i);	
			//console.log("totalRowCount="+totalRowCount);	

			//If not finish keep removing rows else finish cleaning innerHTML and start new search
			if( i < totalRowCount){
				setTimeout(() => {  removeRow(totalRowCount, rows, i);}, 100);	
			}else{
				setTimeout(() => {  rows.innerHTML = ''; searchData(); }, 2000);	
				
			}
		}
		
		//Clean div
		function cleanDiv(divId){
			var div = document.getElementById(divId);
			div.innerHTML = "";
			
		}
		</script>

		<p>Load data into Data Base:</p>
			
		<form method="post">
			<input type="submit" name="loaddata"
					class="button" value="Data Load" onClick="cleanDiv('result')" />			  		
		</form>			
		
		<div id="result">		
			<?php
				if(array_key_exists('loaddata', $_POST)) {
					
					//Search and load files to DB
					FileLoader::dirloader();
									
				}
			//phpinfo();				
			?>
		</div>
		
		
	</body>
</html>
	
	

	
	
	
	
	
	