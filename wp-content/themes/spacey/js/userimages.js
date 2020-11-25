;( function () {
       
    var userImages = {        
        getImages: function(query = ''){
            console.log('user image js loaded');
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const pageNum = urlParams.get('pagenum');

            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: 'action=get_images&search=' + query + '&pagenum=' + pageNum,
                dataType: "json",
                success: function(resp){
                    console.log(resp);
                    $('#user-images').html(resp.html);
                }
            })
        },
        searchImages: function(){
            $('#filter').on('input propertychange paste', function(){
                setTimeout(function(){
                    let query = $('#filter').val();
                    userImages.getImages(query);
                }, 500);
            })  
        }, 
        setJumbo: function(){
            var isJumbo = 0;
            console.log('setjimbo loaded');
            $('.setJumbo').on('click', function(){
                var imageID = userImages.getImageID($(this));
                isJumbo = this.checked ? 1 : 0;
                console.log(isJumbo);
                //Ajax call to set if the image is printed at jumbo size
                $.ajax({
                    type: 'POST',
                    url: ajax_url, // Set in the header file
                    data: 'action=set_jumbo&fileID=' + imageID + '&isJumbo=' + isJumbo,
                    dataType: "json",
                    success: function(resp){
                        console.log(resp);
                    },
                    error: function(err){
                        console.log(err);
                    }
                })
            });
        },
        setUnderbase: function(){
            var underbase = 0;
            
            $('.setUnderbase').on('click', function(){
                let imageID = userImages.getImageID($(this));
                underbase = this.checked ? 1 : 0;
                console.log(underbase);
                //ajax call to set whether the image has an underbase
                $.ajax({
                    type: 'POST',
                    url: ajax_url, // Set in the header file
                    data: 'action=set_underbase&fileID=' + imageID + '&underbase=' + underbase,
                    dataType: "json",
                    success: function(resp){
                        console.log(resp);
                    },
                    error: function(err){
                        console.log(err);
                    }
                })
            });
        },
        renameImage: function(){
        },
        deleteImage: function(){
        },
        getImageID: function(ele){
            var imageID = ele.closest(".image_wrapper").attr('id');
            
            return imageID;
        },
        init: function(){
            console.log('userimages script loaded')
            this.getImages()
            this.searchImages()
            this.setJumbo()
            this.setUnderbase()
            this.renameImage()
            this.deleteImage()

            return this
        }
    }
  window.userImages = userImages
})();

$(window).on('load', function(){

    userImages.init()

})
