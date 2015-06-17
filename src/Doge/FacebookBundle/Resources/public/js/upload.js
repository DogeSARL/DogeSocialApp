var uploadApp = function(){
    var albums = {};

    function _init(){
        FB.api('/me/albums?fields=id,name', function(response) {
            var data = response.data;
            data.forEach(function( album ){
                FB.api('/'+album.id+'/photos', function(photos){
                    if (photos && photos.data && photos.data.length){
                        var newAlbum = [];

                        for(var j=0; j < photos.data.length; j++){
                            var photo = photos.data[j];
                            var newPhoto = [];

                            newPhoto[ "name" ] = photo.name;
                            newPhoto[ "picture" ] = photo.picture;

                            // photo.picture contain the link to picture

                            bestSizedImage = photo.images[0];
                            for( var k = 1 ; k < photo.images.length ; k++ ){
                                if( photo.images[k].width >= 430 && bestSizedImage.width > photo.images[k].width ){
                                    bestSizedImage = photo.images[k];
                                }
                            }

                            newPhoto[ "thumbnail" ] = bestSizedImage;

                            newAlbum.push( newPhoto );
                        }

                        albums[ album.id ] = newAlbum;
                    }
                });
            });
        });
    }

    function getPhotoFromAlbum( albumId ){
        console.log(10205659338776314);
        console.log(albums);
        return albums[ albumId ];
    }

    return {init:_init, getPhotoFromAlbum: getPhotoFromAlbum};
}();