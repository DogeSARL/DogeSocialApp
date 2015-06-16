var uploadApp = function(){
    var albums = {};

    function _init(){
        FB.api('/me/albums?fields=id,name', function(response) {
            var data = response.data;
            data.forEach(function( album ){
                console.log("ok");
                FB.api('/'+album.id+'/photos', function(photos){
                    console.log(photos);
                    if (photos && photos.data && photos.data.length){
                        var newAlbum = [];

                        for(var j=0; j < photos.data.length; j++){
                            var photo = photos.data[j];
                            // photo.picture contain the link to picture
                            newAlbum.push({ "name": photo.name, "picture": photo.picture });
                        }

                        console.log(newAlbum);

                        albums[ album.id ] = newAlbum;
                    }
                });
            });

            console.log( albums );
        });
    }

    function getPhotoFromAlbum( albumId ){
        albums.forEach(function( album ){
            if( album['name'] == albumId ){
                return album['name'];
            }
        });
    }

    return {init:_init, getPhotoFromAlbum: getPhotoFromAlbum};
}();