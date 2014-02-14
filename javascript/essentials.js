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

function getHash(loc,data,d,callback) {

	var hashed;

	if (d!=false)
		$.post(loc+"/phpscripts/hashing.php",
			{hash: data,
				name: d}).done( function(data){
				callback(data);
			});
	else
		$.post(loc+"/phpscripts/hashing.php",
			{hash: data}).done( function(data){
				callback(data);
			});

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

function checkUniqueUsername( username, loc, callback ) {

	$.post( loc+"/phpscripts/checkUsernameUnique.php",{

			username: username

		}).done( function(data){

			console.log( data );

			if ( data == "true" )
				callback( true );
			else if ( data == "err" )
				alert( "Err...");
			else
				callback( false );

		});

}

function userFilledIn(loc, callback) {

	callback(false);

	checkUsername(loc, function(data1){
		if (data1)
			checkPass2(loc, function(data2){
				if (data2)
					checkEmail(loc, function(data3){
						if (data3)
							checkTime(loc, function(data4){
								if (data4)
									callback(true);
							});
					});
			});
	});

}

function checkUsername(loc, callback){

	var username = document.getElementById("user_reg_user").value;
	var acc;

	checkUniqueUsername( username, loc, function(data){

		acc = data && (username.length > 3 );
		acc = acc && (username.length < 21);

		document.getElementById("reg_user_res").innerHTML = chooseIcon( acc, loc );

		callback(acc);

	});

}

function checkPass1(loc, callback){

	var password = document.getElementById("user_reg_pass1").value;

	var acc = ( password.length > 4 );

	document.getElementById("reg_pass1_res").innerHTML = chooseIcon( acc, loc );

	callback( acc );

}

function checkPass2(loc, callback){

	var pass1 = document.getElementById("user_reg_pass1").value;
	var pass2 = document.getElementById("user_reg_pass2").value;

	checkPass1(loc, function(data){

		var acc = ( data && (pass1==pass2) );

		document.getElementById("reg_pass2_res").innerHTML = chooseIcon( acc, loc );

		console.log( "Password comp: " + acc );

		callback( acc );

	});


}

function checkEmail(loc, callback){

	var check = new RegExp("([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})");
	var email = document.getElementById("user_reg_email").value;

	var acc = check.test(email);

	document.getElementById("reg_email_res").innerHTML = chooseIcon( acc, loc );

	console.log( "Email comp: " + acc );

	callback(acc);

}

function checkTime(loc, callback){

	var time = document.getElementById("user_reg_time").value;

	var acc = ( time != "N" );

	document.getElementById("reg_time_res").innerHTML = chooseIcon( acc, loc );

	console.log( "Time comp: " + acc );

	callback(acc);

}

function checkCaptcha(loc, callback){

	var cap = document.getElementById("user_reg_cap").value;
	var fruit = new RegExp("^fruit$", "i");
	var acc = fruit.test(cap);

	console.log( "Captcha: " + acc );

	callback(acc);

}

function submitNewUserData(loc){

	checkCaptcha(loc, function(data){

		if (data==true) {
			userFilledIn(loc, function(data){

				console.log( "Callback: " + data );

				if ( data==true) {

					console.log("Adding User...");

					getHash(loc,document.getElementById("user_reg_pass1").value, false, function(data){

						console.log( "Hashed: " + data );

						$.post(loc+"/phpscripts/addUser.php", {
							username: document.getElementById("user_reg_user").value,
							password: data,
							email: document.getElementById("user_reg_email").value,
							timezone: document.getElementById("user_reg_time").value
						}).done(function(data){

							console.log(data);

							if (data=="success") {
								location.replace("registered.php");
							} else if (data=="No file found") {
								alert("No properties file found!");
							} else if (data=="No values found") {
								alert("Nothing was posted!");
							} else if (data=="sql error") {
								alert("SQL error!");
							}

						});

					});

				}

			});

		} else {
			alert("Your CAPTCHA failed. Are you sure you're not a bot?");
		}

	});

}

function submitUserEditData(loc){

	$.post(loc+"/phpscripts/userEdits.php",
		{
			id: document.getElementById("user_edit_id").value,
			bio: document.getElementById("user_edit_bio").value,
			sig: document.getElementById("user_edit_sig").value,
			avat: document.getElementById("user_edit_avat")
		}
	).done(function(data){
		console.log(data);
	});

}

function submitAdminData(loc){

	userFilledIn(loc,function(data){

		console.log( "Callback: " + data );

		if (data==true) {

			console.log("Adding admin...");

			getHash(loc,document.getElementById("user_reg_pass1").value, false, function(data){

				console.log( "Hashed: " + data );

				$.post(loc+"/phpscripts/addUser.php", {
						username: document.getElementById("user_reg_user").value,
						password: data,
						email: document.getElementById("user_reg_email").value,
						timezone: document.getElementById("user_reg_time").value,
						admin: true,
						first_install: true
					}).done(function(data){

						console.log(data);

						if (data=="success") {
							location.replace("installation_successful.php");
						} else if (data=="No file found") {
							alert("No properties file found!");
						} else if (data=="No values found") {
							alert("Nothing was posted!");
						} else if (data=="sql error") {
							alert("SQL error!");
						}

					});

			});

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

	overText.innerHTML += configFile + "<span id='overlay_text' >Once this has been created, please click the button below.<br /><span id='overlay_failure'></span></span> "
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
	var reg = new RegExp("https?:\/\/([a-zA-Z0-9\.\/_]+[.]{1}[a-z]{2,5})|localhost(\/\w*)*");

	var acc = ( reg.test(install) );

	document.getElementById("reg_inst_loc_res").innerHTML = chooseIcon( acc, loc );

	console.log( "Install Loc: " + acc);

	return acc;

}

function checkConstant(loc) {

	var constant = document.getElementById("user_reg_num_const").value;

	var acc = ( constant.length != 0 && !isNaN(constant) );

	document.getElementById("reg_num_const_res").innerHTML = chooseIcon( acc, loc );

	console.log( "Num Const: " + acc);

	return acc;

}

function filledIn(loc) {

	console.log( "checking fillings" );

	return ( checkDbHost(loc) && checkDbUser(loc) && checkDbPass(loc) &&
			checkDbUser(loc) && checkDbPort(loc) && checkDbPref(loc) &&
			checkDomain(loc) && checkInstLoc(loc) && checkConstant(loc) &&
			checkDbName(loc) );

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