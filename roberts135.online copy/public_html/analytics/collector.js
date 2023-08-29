window.onload = init;

function init() {
    window.cse135 = {};
    window.cse135.cse135_UserThemeSettings = generateSessionID();
    window.cse135.cse135_activity = "";
    window.cse135.cse135_scrollController = true;
    window.cse135.cse135_mouseController = true;
    window.cse135.cse135_lastActive = 0;
    window.cse135.cse135_activityTimeout = 0;
    window.onerror = (e, src, l, c, error) => {logError(e, src, l, c, error);};
    window.onmousemove = (e) => {logMouseMove(e);};
    window.onmousedown = (e) => {logClick(e);};
    window.onscroll = (e) => {logScroll(e);};
    window.onkeydown = (e) => {logKeyDown(e);};
    window.onkeyup = (e) => {logKeyUp(e);};
    document.onvisibilitychange = sendUnload;
    setTimeout(sendInitialData, 0);
    sendActivityData();
}

async function sendData(url, method, data) {
    if(data) {
        const response = await fetch(url, {
            method: method,
        headers: {
            "Content-Type": "cse135/dsv"
        },
        body: data});
        if(!response.ok) {
            setTimeout(() => {sendData(url, method, data)}, 10000);
        }
    }
}

function sendInitialData(data) {
    let testImg = document.createElement("img");
    testImg.src = "https://canvas.ucsd.edu/images/messages/avatar-50.png";
    testImg.width = 1;
    testImg.id = "testImg";
    document.lastChild.appendChild(testImg);
    let testCSS = document.createElement("p");
    testCSS.id = "testCSS";
    document.lastChild.appendChild(testCSS);
    sendData("/analyticsapi/static/" + window.cse135.cse135_UserThemeSettings, "PUT", collectStaticData());
    sendData("/analyticsapi/performance/" + window.cse135.cse135_UserThemeSettings, "PUT", collectPerformanceData());
    sendData("/analyticsapi/activity/" + window.cse135.cse135_UserThemeSettings, "PUT", "o~" + window.location.href + "~" + Date.now());
}

function sendActivityData() {
    sendData("/analyticsapi/activity/" + window.cse135.cse135_UserThemeSettings, "PATCH", window.cse135.cse135_activity.substring(1));
    console.log(window.cse135.cse135_activity);
    window.cse135.cse135_activity = "";
    setTimeout(sendActivityData, 10000);
}

function sendUnload() {
    if (document.visibilityState === "hidden") {
        let unload_data = "";
        if(window.cse135.cse135_activity == "") {
            unload_data = "l~" + window.location.href + "~" + Date.now();
        } else {
            unload_data = window.cse135.cse135_activity + "|l~" + window.location.href + "~" + Date.now();
            window.cse135.cse135_activity = "";
        }
        navigator.sendBeacon("/analyticsapi/activity/" + window.cse135.cse135_UserThemeSettings, unload_data);
    }
}

function logActivity() {
    clearTimeout(window.cse135.cse135_activityTimeout);
    if(window.cse135.cse135_lastActive) {
        window.cse135.cse135_activity += "|" + ("i~" + Date.now() + "~" + (Date.now() - window.cse135.cse135_lastActive));
    }
    window.cse135.cse135_lastActive = 0;
    window.cse135.cse135_activityTimeout = setTimeout(() => {window.cse135.cse135_lastActive = Date.now() - 2000}, 2000);
}

function logKeyDown(e) {
    logActivity();
    window.cse135.cse135_activity += "|" + ("d~" + e.keyCode);
}

function logKeyUp(e) {
    logActivity();
    window.cse135.cse135_activity += "|" + ("u~" + e.keyCode);
}

function logScroll(e) {
    if(window.cse135.cse135_scrollController) {
        window.cse135.cse135_scrollController = false;
        setTimeout(() => {
            logActivity();
            window.cse135.cse135_activity += "|" + ("s~" + window.scrollX + "~" + window.scrollY);
            window.cse135.cse135_scrollController = true;
        }, 500);
    }
}

function logClick(e) {
    logActivity();
    window.cse135.cse135_activity += "|" + ("c~" + e.screenX + "~" + e.screenY + "~" + e.button);
}

function logMouseMove(e) {
    if(window.cse135.cse135_mouseController) {
        window.cse135.cse135_mouseController = false;
        setTimeout(() => {
            logActivity();
            window.cse135.cse135_activity += "|" + ("m~" + e.screenX + "~" + e.screenY);
            window.cse135.cse135_mouseController = true;
        }, 100);
    }
}

function logError(e, src, l, c, error) {
    window.cse135.cse135_activity += "|" + ("e~" + src + "~" + l + "~" + c + "~" + error);
}

function collectStaticData() {
    return navigator.userAgent + "|"
        + navigator.language + "|"
        + (navigator.cookieEnabled ? 1 : 0) + "|1|"
        + ((document.getElementById("testImg").width == 1) ? 1 : 0) + "|"
        + ((window.getComputedStyle(document.getElementById("testCSS")).getPropertyValue("color") == "rgb(255, 0, 0)") ? 1 : 0) + "|"
        + window.innerWidth + "|"
        + window.innerHeight + "|"
        + window.outerWidth + "|"
        + window.outerHeight + "|"
        + screen.width + "|"
        + screen.height + "|"
        + (navigator.connection ?
            navigator.connection.effectiveType:
            "undefined");
}

function collectPerformanceData() {
    return JSON.stringify(window.performance.timing) + "|"
        + window.performance.timing.loadEventStart + "|"
        + window.performance.timing.loadEventEnd + "|"
        + (window.performance.timing.loadEventEnd - window.performance.timing.loadEventStart);
}

//expected collision after ~2^150 sessions
function generateSessionID() {
    let strID = "";
    for(let x = 0; x < 50; x++) {
        strID += "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_*".charAt(Math.floor(Math.random() * 64));
    }
    return strID;
}