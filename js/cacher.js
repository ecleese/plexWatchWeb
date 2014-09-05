if(sessionStorage === null) console.log("Storage not supported by the browser");

//cached object
var temp = sessionStorage.getItem('cacheObj');
var cacheObj = $.parseJSON(temp);

if (cacheObj === null) {
    var cacheObj = new Object();
}

function setCache(postId, postData) {
    
    var milliseconds = new Date().getTime();
    
    if (cacheObj.length > 0) {
        
        var objectExists = false;
        
        //check if we already have this data stored and is current
        for (var i = 0; i < cacheObj.length; i++) {
            if (cacheObj[i].postId === postId) {
                objectExists = true;
            }
        }
        // add the data to the object if it's not there already
        if (!objectExists) {
            cacheObj.push( { postId: postId, data: postData, timestamp: milliseconds } );
            sessionStorage.setItem('cacheObj', JSON.stringify(cacheObj));
        }
    } else {        
        cacheObj =  [ { postId: postId, data: postData, timestamp: milliseconds } ];
        sessionStorage.setItem('cacheObj', JSON.stringify(cacheObj));
    }

};

function getCache(postId) {
    
    var milliseconds = new Date().getTime();
    var validityMilliseconds = 60000*60*1; // 1 hour
    //var validityMilliseconds = 30000; // 30 seconds
    
    if (cacheObj.length > 0) {
        for (var i = 0; i < cacheObj.length; i++) {
            if (cacheObj[i].postId === postId) {
                if (milliseconds < (cacheObj[i].timestamp + validityMilliseconds)) {
                    return cacheObj[i].data;
                } else {
                    console.log('Object expired, destroying.');
                    cacheObj.splice(i, 1);
                }
            }
        }
    }
    return false;
};