'use strict';
//version: working video on both side- complete flow
var iceCandidatesRemote=[];
var walletAddressUsername=null;
var currentWalletContactUserId=null;
var currentWalletContactSenderId=null;
var currentWalletContactType=null;
var notifToken=null;
var tokenSentToServer=false;
var signature=null;
var newSignIn=false;
var config = {
    apiKey: "AIzaSyC3Pfrpm3LZcXnSMZpRQcglSPPnM05WKl4",
    authDomain: "sense-ff35d.firebaseapp.com",
    databaseURL: "https://sense-ff35d.firebaseio.com",
    projectId: "sense-ff35d",
    storageBucket: "sense-ff35d.appspot.com",
    messagingSenderId: "1067473790964"
};
var optionsGeo = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};
var servers=null;
var pcConstraint = null;

//Video
var currentVideoViewer=null;
var currentVideoViewee=null;
var currentAudioViewer=null;
var currentAudioViewee=null;

var currentVideoStreamViewer=null;
var currentVideoStreamViewee=null;
var currentAudioStreamViewer=null;
var currentAudioStreamViewee=null;
///---Messaging Vars --//
var connectionPC;
var sendChannel;
var receiveChannel;
var dataConstraint;
var dataChannelSend = $("#dataChannelSend");
var dataChannelReceive =$("#dataChannelReceive");
var sendButton = $("#sendButton");


//--- Messaging ---//
function createConnection(ev,_servers,_pcConstraint,_stream,useDataChannel,currentWalletContactType_in) {
    servers = _servers;
    pcConstraint = _pcConstraint;
    // For SCTP, reliable and ordered delivery is true by default.
    // Add connectionPC to global scope to make it visible
    // from the browser console.

    if (useDataChannel) {
        dataConstraint = null;
        connectionPC =
            new RTCPeerConnection(servers, pcConstraint);
        trace('Created local peer connection object connectionPC');

        connectionPC.onicecandidate = iceCallback;


        sendChannel = connectionPC.createDataChannel('sendDataChannel',
            dataConstraint);

        sendChannel.onopen = onSendChannelStateChange;
        sendChannel.onclose = onSendChannelStateChange;
        trace('Created send data channel');


    } else {
        connectionPC =
            new RTCPeerConnection(servers);
        trace('Created local peer connection object connectionPC');
        connectionPC.onaddstream  = currentWalletContactType_in=='video'?gotRemoteStream:gotRemoteStreamAudio;
        connectionPC.onicecandidate = iceCallback;
        connectionPC.addStream(_stream);
    }
    connectionPC.createOffer().then(
        gotOfferDescription,
        onCreateSessionDescriptionError
    );
}

function gotRemoteStream(event) {
    currentVideoStreamViewee= event.stream;
    currentVideoViewee=document.querySelector('#videoViewee');
    currentVideoViewee.srcObject = currentVideoStreamViewee;
    currentVideoViewee.onloadedmetadata = function(e) {
        currentVideoViewee.play();
    };

    console.log('got remote stream');
}

function gotRemoteStreamAudio(event) {
    currentAudioStreamViewee= event.stream;
    currentAudioViewee=document.querySelector('#audioViewee');
    currentAudioViewee.srcObject = currentAudioStreamViewee;
    currentAudioViewee.onloadedmetadata = function(e) {
        currentAudioViewee.play();
    };

    console.log('got remote stream');
}

function contactWalletUserForMessage(ev){
    currentWalletContactUserId= $(ev.target).attr("data-userID");
    currentWalletContactType="message";
    var _servers = null;
    var _pcConstraint = null;
    dataChannelSend.placeholder = '';
    trace('Using SCTP based data channels');
    createConnection(ev,_servers,_pcConstraint,null,true,currentWalletContactType);
    $(".screen").page().transition("message", "slide-in-from-right");
}

function contactWalletUserForAudio(ev){
    $(".screen").page().transition("audio", "slide-in-from-right");
    currentWalletContactType="audio";
    navigator.getUserMedia = navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia;

    if (navigator.getUserMedia) {
        navigator.getUserMedia({ audio: true,video:false},
            function(stream) {
                currentAudioStreamViewer=stream;
                currentAudioViewer = document.querySelector('#audioViewer');

                var audioTracks = currentAudioStreamViewer.getAudioTracks();
                //console.log('Got stream with constraints:', constraints);
                //console.log('Using audio device: ' + audioTracks[0].label);
                currentAudioStreamViewer.oninactive = function() {
                    console.log('Stream ended');
                };
                window.stream = stream; // make variable available to browser console
                currentAudioStreamViewer.srcObject = stream;

                currentWalletContactUserId= $(ev.target).attr("data-userID");
                var _servers = {'iceServers': [{'urls': 'stun:stun.services.mozilla.com'}, {'urls': 'stun:stun.l.google.com:19302'}, {'urls': 'turn:numb.viagenie.ca','credential': 'brownie1','username': 'savalas@makesense.com'}]};
                var _pcConstraint = {
                    audio: true,
                    video: false
                };
                createConnection(ev,_servers,_pcConstraint,stream,false,currentWalletContactType);
            },
            function(err) {
                console.log("The following error occurred: " + err.name);
                $('#errorBanner').html(err.name);
            }
        );
    } else {
        console.log("getUserMedia not supported");
    }

}

function contactWalletUserForVideo(ev){
    currentWalletContactUserId= $(ev.target).attr("data-userID");
    currentWalletContactType="video";
    $(".screen").page().transition("video", "slide-in-from-right");
    navigator.getUserMedia = navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia;

    if (navigator.getUserMedia) {
        navigator.getUserMedia({ audio: true, video: { width: 1280, height: 720 } },
            function(stream) {
                currentVideoStreamViewer=stream;
                currentVideoViewer= document.querySelector('#videoViewer');
                currentVideoViewer.srcObject = currentVideoStreamViewer;
                currentVideoViewer.onloadedmetadata = function(e) {
                    currentVideoViewer.play();
                };

            //Now Set up Peer Connection
                var _servers = {'iceServers': [{'urls': 'stun:stun.services.mozilla.com'}, {'urls': 'stun:stun.l.google.com:19302'}, {'urls': 'turn:numb.viagenie.ca','credential': 'brownie1','username': 'savalas@makesense.com'}]};
                var _pcConstraint = {
                    audio: true,
                    video: true
                };

                createConnection(ev,_servers,_pcConstraint,stream,false,currentWalletContactType);
            },
            function(err) {
                console.log("The following error occurred: " + err.name);
                $('#errorBanner').html(err.name +': '+err.message);
            }
        );
    } else {
        console.log("getUserMedia not supported");
    }
}


function iceCallback(event) {
    trace('caller ice callback');
    if (event.candidate) {
        //Send to remote
        var walletSenderId=firebase.auth().currentUser.uid;
        var userWalletContactsRef= firebase.database().ref('wallet_users/' + currentWalletContactUserId +'/contacts/'+ walletSenderId);
        var msg = userWalletContactsRef.push({senderId:walletSenderId,ice: JSON.stringify(event.candidate)});
        msg.remove();
        trace('Local ICE candidate: \n' + event.candidate.candidate);
    }else{
        trace('All ice sent');
    }
}

function gotOfferDescription(desc) {
    var walletSenderId=firebase.auth().currentUser.uid;
    connectionPC.setLocalDescription(desc);

    //Send to the database
    var userWalletContactsRef= firebase.database().ref('wallet_users/' + currentWalletContactUserId +'/contacts/'+ walletSenderId);
    var msg = userWalletContactsRef.push({senderId:walletSenderId,contactType:currentWalletContactType,walletAddressUsername:walletAddressUsername, desc: JSON.stringify(desc)});
    msg.remove();
    trace('Offer from connectionPC \n' + desc.sdp);

}


function onCreateSessionDescriptionError(error) {
    trace('Failed to create session description: ' + error.toString());
}

function handleMessageConnect(walletUserIdFrom,dataObj) {    //same as initial readMessage
    currentWalletContactSenderId=walletUserIdFrom;
    var contactType = dataObj.contactType;

    var walletAddressUsername=dataObj.walletAddressUsername;
    if(dataObj.ice!=undefined){
        console.log('ICEEEEEE');
        var ice = JSON.parse(dataObj.ice);

        if(!connectionPC || !connectionPC.remoteDescription.type){
            //push candidate onto queue...
            console.log('QUEEEEEEEE');
            iceCandidatesRemote.push(ice);
        }else {
            connectionPC.addIceCandidate(
                new RTCIceCandidate(ice)
            ).then(
                onAddIceCandidateSuccess,
                onAddIceCandidateError
            );
            trace('Remote ICE candidate from ' + walletUserIdFrom + ': \n ' + ice);
        }
    }else {
        var desc = JSON.parse(dataObj.desc);
        if (desc.type == "offer") {
            //$(".screen").page().shake();
            var txt;
            var r = confirm("User " + walletAddressUsername + " would like to contact you via " + contactType);
            if (r == true) {
                txt = "Connecting";
                $(".screen").page().transition(contactType, "slide-in-from-right"); //will send thetype
                handleMessageConnectOffer(walletUserIdFrom, desc, contactType);
                //alert(txt);
            } else {
                txt = "Declining Contact";  //send back an answer with 0
                alert(txt);
            }
        } else if (desc.type == "answer") {
            handleMessageConnectAnswer(desc);
        }
    }
}


function handleMessageConnectOffer(walletUserIdFrom,desc,contactType) {    //same as initial readMessage
    switch(contactType){
        case 'message':
            handleMessageConnectOfferMessage(walletUserIdFrom,desc);
            break;
        case 'audio':
            handleMessageConnectOfferAudio(walletUserIdFrom,desc);
            break;
        case 'video':
            handleMessageConnectOfferVideo(walletUserIdFrom,desc);
            break;
        default:
            handleMessageConnectOfferMessage(walletUserIdFrom,desc);
            break;
    }
}

function handleMessageConnectOfferMessage(walletUserIdFrom,desc) {    //same as initial readMessage
    // Add connectionPC to global scope to make it visible
    // from the browser console.
    var dataConstraint=null;
    trace('Created remote peer connection object connectionPC');
    var _servers =null;
    connectionPC =
        new RTCPeerConnection(_servers);

   // sendChannel = connectionPC.createDataChannel('sendDataChannel',
     //   dataConstraint);
    trace('Created send data channel for callee');
    connectionPC.ondatachannel = receiveChannelCallback;
    connectionPC.setRemoteDescription(desc);

    iceCandidatesRemote.forEach(function(anIceCandidateRemote) {
        connectionPC.addIceCandidate(
            new RTCIceCandidate(anIceCandidateRemote)
        ).then(
            onAddIceCandidateSuccess,
            onAddIceCandidateError
        )
    });

    connectionPC.onicecandidate = iceCallback;
    connectionPC.createAnswer().then(
        gotAnswer,
        onCreateSessionDescriptionError
    );
}

function handleMessageConnectOfferAudio(walletUserIdFrom,desc) {
    currentWalletContactUserId= walletUserIdFrom;
    currentWalletContactType="audio";
    $(".screen").page().transition("audio", "slide-in-from-right");
    navigator.getUserMedia = navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia;

    if (navigator.getUserMedia) {
        navigator.getUserMedia({ audio: true, video:false },
            function(stream) {
                currentAudioStreamViewer=stream;
                currentAudioViewer= document.querySelector('#audioViewer');
                currentAudioViewer.srcObject = currentVideoStreamViewer;
                currentAudioViewer.onloadedmetadata = function(e) {
                    currentAudioViewer.play();
                };

                trace('Created remote peer connection object connectionPC');

                var _servers = {'iceServers': [{'urls': 'stun:stun.services.mozilla.com'}, {'urls': 'stun:stun.l.google.com:19302'}, {'urls': 'turn:numb.viagenie.ca','credential': 'brownie1','username': 'savalas@makesense.com'}]};
                connectionPC =
                    new RTCPeerConnection(_servers);
                connectionPC.onaddstream = gotRemoteStreamAudio;
                connectionPC.onicecandidate = iceCallback;
                connectionPC.addStream(currentAudioStreamViewer);
                connectionPC.setRemoteDescription(new RTCSessionDescription(desc));
                connectionPC.createAnswer().then(
                    gotAnswer,
                    onCreateSessionDescriptionError
                );

                iceCandidatesRemote.forEach(function(anIceCandidateRemote) {
                    connectionPC.addIceCandidate(
                        new RTCIceCandidate(anIceCandidateRemote)
                    ).then(
                        onAddIceCandidateSuccess,
                        onAddIceCandidateError
                    )
                });
            },
            function(err) {
                console.log("The following error occurred: " + err.name);
            }
        );
    } else {
        console.log("getUserMedia not supported");
    }
}

function handleMessageConnectOfferVideo(walletUserIdFrom,desc) {
    currentWalletContactUserId= walletUserIdFrom;
    currentWalletContactType="video";
    $(".screen").page().transition("video", "slide-in-from-right");
    navigator.getUserMedia = navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia;

    if (navigator.getUserMedia) {
        navigator.getUserMedia({ audio: true, video: { width: 1280, height: 720 } },
            function(stream) {
                currentVideoStreamViewer=stream;
                currentVideoViewer= document.querySelector('#videoViewer');
                currentVideoViewer.srcObject = currentVideoStreamViewer;
                currentVideoViewer.onloadedmetadata = function(e) {
                    currentVideoViewer.play();
                };

                trace('Created remote peer connection object connectionPC');

                var _servers = {'iceServers': [{'urls': 'stun:stun.services.mozilla.com'}, {'urls': 'stun:stun.l.google.com:19302'}, {'urls': 'turn:numb.viagenie.ca','credential': 'brownie1','username': 'savalas@makesense.com'}]};
                connectionPC =
                    new RTCPeerConnection(_servers);
                connectionPC.onaddstream = gotRemoteStream;
                connectionPC.onicecandidate = iceCallback;
                connectionPC.addStream(currentVideoStreamViewer);
                connectionPC.setRemoteDescription(new RTCSessionDescription(desc));
                connectionPC.createAnswer().then(
                    gotAnswer,
                    onCreateSessionDescriptionError
                );

                iceCandidatesRemote.forEach(function(anIceCandidateRemote) {
                    connectionPC.addIceCandidate(
                        new RTCIceCandidate(anIceCandidateRemote)
                    ).then(
                        onAddIceCandidateSuccess,
                        onAddIceCandidateError
                    )
                });
        },
            function(err) {
                console.log("The following error occurred: " + err.name);
            }
        );
    } else {
        console.log("getUserMedia not supported");
    }
}

function handleMessageConnectAnswer(desc){
     connectionPC.setRemoteDescription(new RTCSessionDescription(desc));
    trace('Answer from remote connectionPC \n' + desc.sdp);

}

function gotAnswer(desc) {
    connectionPC.setLocalDescription(desc);
    trace('Answer for connectionPC \n' + desc.sdp);
    var walletUserId=firebase.auth().currentUser.uid;
    var userWalletContactsRef= firebase.database().ref('wallet_users/' + currentWalletContactSenderId +'/contacts/'+ walletUserId);
    var msg = userWalletContactsRef.push({senderId:walletUserId,walletAddressUsername:walletAddressUsername, desc:JSON.stringify(desc) });
    msg.remove();
}

function onAddIceCandidateSuccess() {
    trace('AddIceCandidate success.');
}

function onAddIceCandidateError(error) {
    trace('Failed to add Ice Candidate: ' + error.toString());
}

function receiveChannelCallback(event) {
    trace('Receive Channel Callback');
    receiveChannel = event.channel;
    receiveChannel.onmessage = onReceiveMessageCallback;
    receiveChannel.onopen = onReceiveChannelStateChange;
    receiveChannel.onclose = onReceiveChannelStateChange;
}

function onReceiveMessageCallback(event) {
    trace('Received Message');
    $("#dataChannelReceive").val(event.data);
}

//Sending Data
function sendData() {
    var data = $("#dataChannelSend").val();
    sendChannel.send(data);
    trace('Sent Data: ' + data);
}

function contactWalletUserDisconnectMessage(){
    $(".screen").page().transition("12", "slide-in-from-left");
    trace('Closing data channels');
    sendChannel.close();
    trace('Closed data channel with label: ' + sendChannel.label);
    receiveChannel.close();
    trace('Closed data channel with label: ' + receiveChannel.label);
    connectionPC.close();
    connectionPC = null;
    trace('Closed peer connections');
    sendButton.disabled = true;
    dataChannelSend.value = '';
    dataChannelReceive.value = '';
    dataChannelSend.disabled = true;
    disableSendButton();
}


function contactWalletUserDisconnectAudio(){
    if(currentVideoViewer){
        var tracks = currentAudioStreamViewer.getTracks();
        tracks.forEach(function(track) {
            track.stop();
        });
        currentAudioViewer.srcObject=null;

        if(currentAudioStreamViewee) {
            var tracks2 = currentAudioStreamViewee.getTracks();
            tracks2.forEach(function (track) {
                track.stop();
            });
            currentAudioViewee.srcObject = null;
        }
    }
    $(".screen").page().transition("12", "slide-in-from-left");
}

function contactWalletUserDisconnectVideo(){
    if(currentVideoViewer){
        var tracks = currentVideoStreamViewer.getTracks();
        tracks.forEach(function(track) {
            track.stop();
        });
        currentVideoViewer.srcObject=null;

        if(currentVideoStreamViewee) {
            var tracks2 = currentVideoStreamViewee.getTracks();
            tracks2.forEach(function (track) {
                track.stop();
            });
            currentVideoViewee.srcObject = null;
        }
    }
    $(".screen").page().transition("12", "slide-in-from-left");
}

function onSendChannelStateChange() {
    var readyState = sendChannel.readyState;
    trace('Send channel state is: ' + readyState);
    if (readyState === 'open') {
        dataChannelSend.disabled = false;
        dataChannelSend.focus();
        sendButton.disabled = false;
        //closeButton.disabled = false;
    } else {
        dataChannelSend.disabled = true;
        sendButton.disabled = true;
        //closeButton.disabled = true;
    }
}

function onReceiveChannelStateChange() {
    var readyState = receiveChannel.readyState;
    trace('Receive channel state is: ' + readyState);
}

function trace(text) {
    if (text[text.length - 1] === '\n') {
        text = text.substring(0, text.length - 1);
    }
    if (window.performance) {
        var now = (window.performance.now() / 1000).toFixed(3);
        console.log(now + ': ' + text);
    } else {
        console.log(text);
    }
}


////Video/Audio Stream-///

// Handles error by logging a message to the console with the error message.
function handleLocalMediaStreamError(error) {
    console.log('navigator.getUserMedia error: ', error);
}


function standardTransition(ev){
    var page  = $(ev.target).attr("data-page-name");
    var trans = $(ev.target).attr("data-page-trans");
    if ($(".screen").page().fetch(page) === null)
        $(".screen").page().shake();
    else
        $(".screen").page().transition(page, trans);
}

function signWalletmarrySignIn(){
    newSignIn=true;
    walletAddressUsername=$("#walletAddressUsername_in").val();
    signature=$("#signature").val();
    if(walletAddressUsername.length>0 && signature.length>0) {
        firebase.auth().signInAnonymously().catch(function (error) {
            // Handle Errors here.
            var errorCode = error.code;
            var errorMessage = error.message;
            // ...
        });
    }else{
        var errorMsg=walletAddressUsername.length==0?"EOS Wallet Address or Username Required\n":"";
        errorMsg+=signature.length==0?"Address/Username must be signed":"";
        alert(errorMsg);
    }
}

function signWalletmarrySignOut(){
    var r = confirm("Are you sure that you would like to remove your wallet?");
    if (r == true) {
        var uid=firebase.auth().currentUser.uid;
        var userWalletRef= firebase.database().ref('wallet_users/' + uid);
        var updates = {};
        updates['tsRemoved'] = Math.round(new Date().getTime() / 1000);
        userWalletRef.update(updates);
        firebase.auth().signOut();
    } else {
    }
}

function contactWalletUser(ev){
    var contactType=$(ev.target).attr("data-contact-type");

    switch(contactType){
        case 'message':
            contactWalletUserForMessage(ev);
            break;
        case 'audio':
            contactWalletUserForAudio(ev);
            break;
        case 'video':
            contactWalletUserForVideo(ev);
            break;
        default:
            contactWalletUserForMessage(ev);
            break;
    }
}

function contactWalletUserDisconnect(ev){
    var contactType=$(ev.target).attr("data-contact-type");

    switch(contactType){
        case 'message':
            contactWalletUserDisconnectMessage();
            break;
        case 'audio':
            contactWalletUserDisconnectAudio();
            break;
        case 'video':
            contactWalletUserDisconnectVideo();
            break;
        default:
            contactWalletUserDisconnectMessage();
            break;
    }
}

function disconnectWalletUserForMessage(){
    closeDataChannels_SCTP();
}

function screenPageMainInit(){
    $(".screen").page();

    $(".screen .page .navigate").click(function (ev) {
        var id= $(ev.target).attr("id");
        switch(id){
            case 'signWalletMarrySignIn':
                signWalletmarrySignIn(ev);
                break;
            case 'signWalletMarrySignOut':
                signWalletmarrySignOut(ev);
                break;
            case 'contactWalletUser':
                contactWalletUser(ev);
                break;
            default:
                if ($(ev.target).hasClass('contactWalletUser')){
                    contactWalletUser(ev);
                }else if ($(ev.target).hasClass('contactWalletUserDisconnect')) {
                    contactWalletUserDisconnect(ev);
                }
                else{
                    standardTransition(ev);
                }
                break;
        }
    });
}

//Pragma Mark Push Notifiction Token
function sendTokenToServer() {
    //Send token to server when there is a user and a token available
    if(firebase.auth().currentUser && notifToken){
        var uid=firebase.auth().currentUser.uid;
        var userWalletRef= firebase.database().ref('wallet_users/' + uid);
        var updates = {};
        updates['notifToken'] = notifToken;
        userWalletRef.update(updates);
    }
}

function setTokenSentToServer(isSent){
    tokenSentToServer=isSent;
}

function updateUIForPushEnabled(token){

}
function updateUIForPushPermissionRequired(){

}
function showToken(msg,err){
    console.log(msg);
}


//Pragma Mark geolocoation
function success(pos) {
    var crd = pos.coords;

    console.log('Your current position is:');
    console.log('Latitude :'+ crd.latitude);
    console.log('Longitude:'+ crd.longitude);
    console.log('More or less '+ crd.accuracy+' meters.');


    if(firebase.auth().currentUser){
        var uid=firebase.auth().currentUser.uid;
        var userWalletRef= firebase.database().ref('wallet_users/' + uid+'/geo');
        userWalletRef.set(
            {latitude: crd.latitude,
                longitude:crd.longitude,
                accuracy_m: crd.accuracy });
    }
}

function error(err) {
    console.log(err.code+':'+err.message);
}


function onTextChange() {
    var key = window.event.keyCode;

    // If the user has pressed enter
    if (key === 13) {
       sendData();
        return false;
    }
    else {
        return true;
    }
}

(function ($) {
    $(document).ready(function () {

        if (location.protocol !== 'https:') {
            // page is not secure
            var errorMsg='Not a secure location. Please go to <a href="https://sensesdk.com">https://sensesdk.com</a>';
            $('#errorBanner').html(errorMsg);
        }


        firebase.initializeApp(config);
        screenPageMainInit();
        navigator.geolocation.getCurrentPosition(success, error, optionsGeo);

        // Retrieve Firebase Messaging object.
        const messaging = firebase.messaging();

        // Add the public key generated from the console here.
        messaging.usePublicVapidKey("BDPcDuo9veLboya90p5yzWFdNxTy2vDmfOkHoVgUYEXNBvTNgzZw80FXvTLlKH5LWSlhzaHOfpyzBVYuAXAHsrQ");

        messaging.requestPermission().then(function() {
            console.log('Notification permission granted.');

            // Get Instance ID token. Initially this makes a network call, once retrieved
            // subsequent calls to getToken will return from cache.
            messaging.getToken().then(function(currentToken) {
                if (currentToken) {
                    notifToken=currentToken;
                    sendTokenToServer();
                    updateUIForPushEnabled(currentToken);
                } else {
                    // Show permission request.
                    console.log('No Instance ID token available. Request permission to generate one.');
                    // Show permission UI.
                    updateUIForPushPermissionRequired();
                    setTokenSentToServer(false);
                }
            }).catch(function(err) {
                console.log('An error occurred while retrieving token. ', err);
                showToken('Error retrieving Instance ID token. ', err);
                setTokenSentToServer(false);
            });

            // Callback fired if Instance ID token is updated.
            messaging.onTokenRefresh(function() {
                messaging.getToken().then(function(refreshedToken) {
                    console.log('Token refreshed.');
                    // Indicate that the new Instance ID token has not yet been sent to the
                    // app server.
                    // Send Instance ID token to app server.
                    notifToken=refreshedToken;
                    sendTokenToServer();
                    // ...
                }).catch(function(err) {
                    console.log('Unable to retrieve refreshed token ', err);
                    showToken('Unable to retrieve refreshed token ', err);
                });
            });

        }).catch(function(err) {
            console.log('Unable to get permission to notify.', err);
        });

        messaging.onMessage(function(payload) {
            alert(payload);
            console.log('Message received. ', payload);
           //Pop-up messagge to about caller
        });

        $("#sendButton").click(function () {
            sendData();
        });

        firebase.auth().onAuthStateChanged(function(user) {
            if (user) {
                // User is signed in.
                navigator.geolocation.getCurrentPosition(success, error, optionsGeo);
                var isAnonymous = user.isAnonymous;
                var uid = user.uid;
                var userWalletRef= firebase.database().ref('wallet_users/' + uid);
                var userWalletContactRef= firebase.database().ref('wallet_users/' + uid +'/contacts');
                if(newSignIn) {
                    userWalletRef.set({
                        device: navigator.userAgent, //be more descriptive
                        isAnonymous: isAnonymous,
                        walletAddressUsername: walletAddressUsername,
                        signature: signature,
                        tsCreated: Math.round(new Date().getTime() / 1000)
                    });

                }else{
                    firebase.database().ref('/wallet_users/' + uid).once('value').then(function(snapshot) {
                        var data=snapshot.val();
                        if(data) {
                            var thisWalletAddressUsername = data.walletAddressUsername;
                            walletAddressUsername = thisWalletAddressUsername;
                            $('#walletAddressUsername').html(thisWalletAddressUsername);
                        }

                        var updates = {};
                        updates['tsLastStateChange'] = Math.round(new Date().getTime() / 1000);
                        userWalletRef.update(updates);
                    })
                }
                sendTokenToServer();
                //listen for incoming messages
                userWalletContactRef.on("child_added", function(snapshot) {
                    console.log(snapshot.key);

                        var walletUserIdFrom=snapshot.key;
                        var snapShotVal = snapshot.val();
                        var keys = Object.keys(snapShotVal);

                    keys.forEach(function (aKey) {
                        var data=snapShotVal[aKey];
                        handleMessageConnect(walletUserIdFrom, data);
                    });
                });
               // $(".screen").page().transition("12", "none");
                $(".screen").page().transition("12", "slide-in-from-top");
            } else {
                // User is signed out.
                $(".screen").page().transition("11", "none");

                // ...
            }

            $("#walletAddressUsername").html(walletAddressUsername);
            // ...
        });

        $("#settings").click(function () {
            $(".screen").page().transition("settings", "slide-in-from-top");
        });

        $("#addSkill").click(function (btn) {
            var skill=$("#addSkillInput").val();
            if(skill.length>0) {
                $("#addSkillInput").val("");
                //var authData=Firebase.getAuth();
                console.log(firebase.auth().currentUser);
                skill=skill.toLowerCase();

                var uid = firebase.auth().currentUser.uid;
                var rating = 3;
                firebase.database().ref('skills/' + skill + '/' + uid).set({
                    walletAddressUsername: walletAddressUsername,
                    rating: rating,
                    tsCreated: Math.round(new Date().getTime() / 1000)
                });
            }else{
                alert("Please enter a valid skill.");
            }
        });

        $("#findContact").click(function (btn) {
            console.log(firebase.auth().currentUser)
            var uid=firebase.auth().currentUser.uid;
            var contactsFound=0;
            var contactInput=$("#findContactInput").val();

            if(contactInput.length>0) {
                $("#findContactInput").val("");

                if(contactInput.slice(0,2)=='0x'){ //Address
                    var walletUserRef = firebase.database().ref('wallet_users/');
                    walletUserRef.orderByChild("walletAddressUsername").equalTo(contactInput).once('value', function (snapshot) {
                        var divsOfUsersToCall = "";
                        snapshot.forEach(function (childSnapshot) {
                            console.log(childSnapshot);
                            //alert(JSON.stringify(childSnapshot));
                            var childKey = childSnapshot.key;
                            var childData = childSnapshot.val();

                            if (childKey != uid) {
                                var acceptedContacts = '<div style="col-md-6"><form class="bs-example bs-example-form" role="form">' +
                                    '<div class="btn-group input-group-md group">' +
                                    '<span class="btn btn-default btn-sm glyphicon glyphicon-pencil navigate contactWalletUser"  data-contact-type="message" data-page-name="message" data-page-trans="slide-in-from-right" data-userID="' + childKey + '"> Message</span>' +
                                    '<span class="btn btn-default btn-sm glyphicon glyphicon-earphone navigate contactWalletUser" data-contact-type="audio"  data-page-name="audio" data-page-trans="slide-in-from-right" data-userID="' + childKey + '"> Call</span>' +
                                    '<span class="btn btn-default btn-sm glyphicon glyphicon-facetime-video navigate contactWalletUser" data-contact-type="video" data-page-name="video" data-page-trans="slide-in-from-right" data-userID="' + childKey + '"> Video</span>' +
                                    '</div></form></div>';


                                divsOfUsersToCall +=
                                    '<div class="col-md-6 navigate-style">' +
                                    '<div  class="col-md-6 navigate contactWalletUser" data-page-name="21" data-page-trans="slide-in-from-right" data-userID="' + childKey + '">' +
                                    childData.walletAddressUsername +
                                    '</div>' +
                                    acceptedContacts +
                                    '</div>';
                            }
                            contactsFound++;
                        });
                        if(contactsFound>0) {
                            //$("#usersToCall" ).load(window.location.href + "#usersToCall" );
                            $('#usersToCall').html(divsOfUsersToCall);
                            screenPageMainInit();
                        }else{
                            $('#usersToCall').html("No Contacts Found.");
                        }
                    });
                }else {
                    var skillsRef = firebase.database().ref('skills/' + contactInput);
                    skillsRef.once('value', function (snapshot) {
                        var divsOfUsersToCall = "";
                        snapshot.forEach(function (childSnapshot) {
                            console.log(childSnapshot);
                            var childKey = childSnapshot.key;
                            var childData = childSnapshot.val();

                            if (childKey != uid) {
                                var acceptedContacts = '<div style="col-md-6"><form class="bs-example bs-example-form" role="form">' +
                                    '<div class="btn-group input-group-md group">' +
                                    '<span class="btn btn-default btn-sm glyphicon glyphicon-pencil navigate contactWalletUser col-md-2"  data-contact-type="message" data-page-name="message" data-page-trans="slide-in-from-right" data-userID="' + childKey + '"> Message</span>' +
                                    '<span class="btn btn-default btn-sm glyphicon glyphicon-earphone navigate contactWalletUser col-md-2" data-contact-type="audio"  data-page-name="audio" data-page-trans="slide-in-from-right" data-userID="' + childKey + '"> Call</span>' +
                                    '<span class="btn btn-default btn-sm glyphicon glyphicon-facetime-video navigate contactWalletUser col-md-2" data-contact-type="video" data-page-name="video" data-page-trans="slide-in-from-right" data-userID="' + childKey + '"> Video</span>' +
                                    '</div></form></div>';


                                divsOfUsersToCall +=
                                    '<div class="col-md-12 navigate-style">' +
                                    '<div  class="col-md-12 navigate contactWalletUser" data-page-name="21" data-page-trans="slide-in-from-right" data-userID="' + childKey + '">' +
                                    childData.walletAddressUsername +
                                    '</div>' +
                                    acceptedContacts +
                                    '</div>';
                            }
                            contactsFound++;
                        });
                        if(contactsFound>0) {
                            //$("#usersToCall" ).load(window.location.href + "#usersToCall" );
                            $('#usersToCall').html(divsOfUsersToCall);
                            screenPageMainInit();
                        }else{
                            $('#usersToCall').html("No Contacts Found.");
                        }
                    });
                }
            }else{
                alert("Please enter a valid skill.");
            }
        });

    });
})(jQuery);

