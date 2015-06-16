var uploadApp = function(){
    var albums;

    function _init(){
        FB.api('/me/albums?fields=id,name', function(response) {
            (response.data).each(function( album ){
                albums = {};
                FB.api('/'+album.id+'/photos', function(photos){
                    if (photos && photos.data && photos.data.length){
                        var newAlbum = {};

                        for(var j=0; j<photos.data.length; j++){
                            var photo = photos.data[j];
                            // photo.picture contain the link to picture
                            newAlbum.push({ "name": photo.name, "picture":photo.picture });
                        }

                        albums.push( newAlbum );
                    }
                });
            });

            console.log( albums );
        });
    }

    function getPhotoFromAlbum( albumName ){
        albums.each(function( album ){
            if( album['name'] == albumName ){
                return album['name'];
            }
        });
    }

    return {init:"_init", getPhotoFromAlbum: "getPhotoFromAlbum"};
}();