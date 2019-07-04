if( 1 === Math.floor(Math.random() * 10)){
    $.when(
        $.getScript( "app/lib/detector.js" ),
        $.getScript( "app/lib/subscriptionHandler.js"),
        $.Deferred(function( deferred ){
            $( deferred.resolve );
        })
    ).done(function(){
        subscriptionHandler.init();
    });
}else{
    console.log("90% chance");
}
