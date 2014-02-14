/**
 * Copyright 2014 Matthew Ball (CyniCode/M477h3w1012)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

function chooseIcon( accepted, loc ) {

	return ( accepted ) ?
		'<img src=\''+loc+'/images/green_tick.png\' style=\'height:16px;\' />' :
		'<img src=\''+loc+'/images/red_cross.png\' style=\'height:16px;\' />';

}

function overlay() {
	var overlay = $( '#overlay' );
	var overlayContents = $( '#overlay_contents' );
	overlay.show( );
	overlayContents.show();
	overlayContents.css( 'top', '60%' );
	overlayContents.css( 'opacity', '0' );
	overlayContents.animate(
		{
			'top': '50%',
			'opacity': '1'
		}, 300 );
}

function remOverlay() {
	var overlay = $( '#overlay' );
	var overlayContents = $( '#overlay_contents' );
	overlay.hide( );
	overlayContents.animate(
		{
			'top': '60%',
			'opacity': '0'
		}, 300, function( ) { overlayContents.hide( ); } );
}

function fileGenSuccess( loc ) {

	console.log("Generation success");

	var overText = document.getElementById("overlay_contents");
	overText.innerHTML  = "<span id='overlay_text' >We're generating the database now...<br /></span>";
	overText.innerHTML += "<img src='"+loc+"/images/ajax-loader.gif' />";
	overlay();

	console.log("Posting values...");

	$.post(loc+"/phpscripts/generation.php")
		.done(function(data){

			console.log( "generation result: " + data );
			if ( data == "successful" ) {
				location.replace("user_info.php");
			} else if ( data == "no file found" ) {
				alert( "No config file found!" );
				remOverlay();
			} else if ( data == "no sql found" ) {
				alert( "No SQL file found!" );
				remOverlay();
			} else if ( data == "sql error" ) {
				alert( "SQL error!" );
				remOverlay();
			} else {
				alert( "Undefined error!" );
				remOverlay();
			}

		});
}

function submitConnData(loc) {

	testConn(loc, function(data){

		console.log("callback: "+data);

		if ( data && filledIn(loc) ) {

			console.log( "Success!" );

			$.post(loc+"/phpscripts/makePropsFile.php",
				{
					host: document.getElementById("user_reg_db_host").value,
					port: document.getElementById("user_reg_db_port").value,
					user: document.getElementById("user_reg_db_user").value,
					pass: document.getElementById("user_reg_db_pass").value,
					name: document.getElementById("user_reg_db_name").value,
					prefix: document.getElementById("user_reg_db_prefix").value,
					domain: document.getElementById("user_reg_domain").value,
					instLoc: document.getElementById("user_reg_inst_loc").value,
					constant: document.getElementById("user_reg_num_const").value
				}).done(function(data){

					console.log( "Properties data return: " + data );

					if ( data=="successful" ) {
						fileGenSuccess(loc);
					} else if ( data=="perm error" ) {
						waitForUserFileGen(loc);
					} else if ( data=="data error" ) {
						alert( "Some important data was not sent" );
					} else if ( data=="file exists error" ) {
						alert( "The properties file is already there" );
					}

				});


		} else {
			alert("Please fill in all the fields");
		}

	});

}

function waitForUserFileGen(loc){
	var overText = document.getElementById("overlay_contents");

	overText.innerHTML = "<span id='overlay_text'>Sorry, we've had some trouble making the configuration. Please copy and paste the data below into a file named 'props.php' located in the 'forum/config/' directory:</span>";

	var configFile = "<div class='overlay_config' >&lt?php <br />" +
						"define(\"DATAHOST\", \""+document.getElementById("user_reg_db_host").value+"\");<br />" +
						"define(\"DATAPORT\", \""+document.getElementById("user_reg_db_port").value+"\");<br />" +
						"define(\"DATAUSER\", \""+document.getElementById("user_reg_db_user").value+"\");<br />" +
						"define(\"DATAPASS\", \""+document.getElementById("user_reg_db_pass").value+"\");<br />" +
						"define(\"DATABASE\", \""+document.getElementById("user_reg_db_name").value+"\");<br />" +
						"define(\"DATAPFIX\", \""+document.getElementById("user_reg_db_prefix").value+"\");<br />" +
						"define(\"DATACONST\", \""+document.getElementById("user_reg_num_const").value+"\");<br />" +
						"define(\"INSTLOC\", \""+document.getElementById("user_reg_inst_loc").value+"\");<br />" +
						"define(\"DOMAIN\", \""+document.getElementById("user_reg_domain").value+"\");</div>";

	overText.innerHTML += configFile +
		"<span id='overlay_text' >" +
			"Once this has been created, please click the button below.<br />" +
			"<span id='overlay_failure'></span>" +
		"</span> ";
	overText.innerHTML += "<button onclick='checkPropsByUser(\""+loc+"\")' >Submit</button>";

	overlay();

}

function checkPropsByUser(loc) {

	$.post(loc+"/phpscripts/checkPropsFile.php").done(function(data){
		if (data=="success") {
			fileGenSuccess(loc);
		} else if (data=="file empty") {
			document.getElementById("overlay_failure").innerHTML = "The config file was empty!";
		} else if (data=="file not found") {
			document.getElementById("overlay_failure").innerHTML = "The file was not found!";
		}
	});

}

function checkDbHost(loc) {

	var host = document.getElementById("user_reg_db_host").value;

	var acc = ( host.length != 0 );

	document.getElementById("reg_db_host_res").innerHTML = chooseIcon( acc, loc );

	console.log( "Host: " + acc );

	return acc;

}

function checkDbPort(loc) {

	var port = document.getElementById("user_reg_db_port").value;

	var acc = ( port.length != 0 && !isNaN(port) );

	document.getElementById("reg_db_port_res").innerHTML = chooseIcon( acc, loc );

	console.log( "Port: " + acc );

	return acc;

}

function checkDbUser(loc) {

	var user = document.getElementById("user_reg_db_user").value;

	var acc = ( user.length != 0 );

	document.getElementById("reg_db_user_res").innerHTML = chooseIcon( acc, loc );

	console.log( "DB User: " + acc );

	return acc;

}

function checkDbPass(loc) {

	var pass = document.getElementById("user_reg_db_pass").value;

	document.getElementById("reg_db_pass_res").innerHTML = chooseIcon( true, loc );

	console.log( "DB Pass: " + true );

	return true;

}

function checkDbName(loc) {

	var name = document.getElementById("user_reg_db_name").value;
	var reg = new RegExp("[a-zA-Z0-9$_]+");

	var acc = ( reg.test(name) );

	document.getElementById("reg_db_name_res").innerHTML = chooseIcon( acc, loc );

	console.log( "DB Name: " + acc);

	return acc;

}

function checkDbPref(loc) {

	var prefix = document.getElementById("user_reg_db_prefix").value;
	var reg = new RegExp("[a-zA-Z0-9$_]*");

	var acc = ( reg.test(prefix) );

	document.getElementById("reg_db_prefix_res").innerHTML = chooseIcon( acc, loc );

	console.log( "DB prefix: " + acc);

	return acc;

}

function checkDomain(loc) {

	var domain = document.getElementById("user_reg_domain").value;
	var reg = new RegExp("https?:\/\/([a-zA-Z0-9\.\/_]+[.]{1}[a-z]{2,5}\/?)|localhost\/?");

	var acc = ( reg.test(domain) );

	document.getElementById("reg_domain_res").innerHTML = chooseIcon( acc, loc );

	console.log( "Domain: " + acc);

	return acc;

}

function checkInstLoc(loc) {

	var install = document.getElementById("user_reg_inst_loc").value;
	var reg = new RegExp("(\/\w*)*");

	var acc = ( reg.test(install) );

	document.getElementById("reg_inst_loc_res").innerHTML = chooseIcon( acc, loc );

	console.log( "Install Loc: " + acc);

	return acc;

}

function filledIn(loc) {

	console.log( "checking fillings" );

	return ( checkDbHost(loc) && checkDbUser(loc) && checkDbPass(loc) &&
			checkDbUser(loc) && checkDbPort(loc) && checkDbPref(loc) &&
			checkDomain(loc) && checkInstLoc(loc) && checkDbName(loc) );

}

function testConn(loc, callback) {

	$.post(loc+"/phpscripts/testConn.php",
			{host: document.getElementById("user_reg_db_host").value,
			port: document.getElementById("user_reg_db_port").value,
			user: document.getElementById("user_reg_db_user").value,
			pass: document.getElementById("user_reg_db_pass").value,
			name: document.getElementById("user_reg_db_name").value}).done(function(data){

		var acc = (data=="true");
		document.getElementById("conn_result").innerHTML = chooseIcon(acc, loc);

		console.log( "Test Connection: " + acc );

		callback( acc );

	});

}