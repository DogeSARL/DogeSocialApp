var uploadApp = function(){
    var albums = {};

    function _init(){
        FB.api('/me/albums?fields=id,name', function(response) {
            var data = response.data;
            console.log(reponse);
            console.log(reponse.data);
            data.forEach(function( album ){
                FB.api('/'+album.id+'/photos', function(photos){
                    if (photos && photos.data && photos.data.length){
                        var newAlbum = {};

                        for(var j=0; j<photos.data.length; j++){
                            var photo = photos.data[j];
                            // photo.picture contain the link to picture
                            newAlbum.push({ "name": photo.name, "picture":photo.picture });
                        }

                        newAlbum[ album.id ] = newAlbum;
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