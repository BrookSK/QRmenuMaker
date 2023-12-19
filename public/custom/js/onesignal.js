"use strict";
$(document).ready(function() {
    
    var OneSignal = window.OneSignal || [];

   
    OneSignal.push(["init", {
        appId: ONESIGNAL_APP_ID,
        subdomainName: "",
        autoRegister: true,
        promptOptions: {
            actionMessage: "We'd like to show you notifications for the latest orders",
            acceptButtonText: "ALLOW",
            cancelButtonText: "NO THANKS"
        }
    }]);


    var OneSignal = OneSignal || [];
    OneSignal.push(function(){
        
        
        OneSignal.setExternalUserId(USER_ID);

        OneSignal.on('subscriptionChange', function(isSubscibed){
            

            /*OneSignal.sendTag("user_id","4444", function(tagsSent) {
                
            });*/
        });

        var isPushSupported = OneSignal.isPushNotificationsSupported();
        if(isPushSupported)
        {
            OneSignal.getUserId( function(userId) {
                
            });

            OneSignal.isPushNotificationsEnabled().then(function(isEnabled)
            {
                if(isEnabled)
                {
                    
                }else {
                    OneSignal.showHttpPrompt();
                    
                }
            });
        }else{
            
        }
    });
});
