<?php


global $wpdb;
$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;
$current_user_name = $current_user->user_login;
if(0 == $current_user_id){
    wp-redirect("/login");
    exit();
}

wp_enqueue_script('userimages', get_template_directory_uri(), '/js/userimages', array(), '', true);
wp_enqueue_script('dropzone', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.js', array(), '', true);
wp_enqueue_style('dropzone', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css');

/*if($current_user_id==0)
wp_mail('team@ryankikta.com','user id null',var_export(array($_SERVER['HTTP_USER_AGENT'],wp_get_current_user()),true));*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <style type="text/css">
        a {
            color: #1f51cf;
        }
        a:hover {
            color: #1f51cf;
        }
        .form-signin .form-signin-heading,
        .form-signin .checkbox {
            margin-bottom: 10px;
        }
        .form-signin input[type="text"],
        .form-signin input[type="password"] {
            font-size: 16px;
            height: auto;
            margin-bottom: 15px;
            padding: 7px 9px;
        }
        body {
            padding: 0px;
            margin: 0px;

        }
        .container {
            padding: 0px;
            margin: 0px;

        }
        .cc {
            position: relative;
        }
	.dropzone {
	}
        .hidden {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: white;
            z-index: -1;
            color: black;
            text-align: center;
        }
        .thespan {
            position: relative;
            top: 40%;
            font-size: 18px;
        }
        .show:hover {
            opacity: 0.6;
            cursor: pointer;
        }
        .hoverr {
            background: none !important;
        }
        .orange {
            color: #1f51cf !important;
        }
        .pr-image {
            max-height: 200px !important;
            max-width: 200px !important;
        }
        .jumbo-pr {
            display: block;
            float: right;
            margin-bottom: 10px;
            margin-top: 20px;
            position: absolute;
            width: 210px;
        }
        .image-wrapper1 {
            height: 303px;
        }
        .image-holder {
            height: 370px !important;
        }

        #loading {
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 999;
            background: rgba(0,0,0,0.5);
            color: #fff;
        }
    </style>

    <script>
	var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
	Dropzone.autoDiscover = false;

        $(document).ready(function () {
	    var acceptedFileTypes = ".jpg, .png, .pdf, .psd, .tif, .dst";

	    $("p#filetypes").append(acceptedFileTypes);

            $(".cc").hover(function () {
                $(this).css({'background-color': ''});
            }, function () {
                $(this).css({'background-color': '#F0F0F0'});
            });

            $(".rename").click(function () {
                thisfileid = $(this).attr('id').substring(6);
                filename = $("#name" + thisfileid).html();
                $("#name").val(filename)
                $("#dialog-form").dialog("open");
            });

            $(".remove").click(function () {
                if (confirm('Are you sure you want to delete this image?')) {
                    thisfileid = $(this).attr('id').substring(6);
                    $.ajax({
                        url: "/processupload.php?action=delete&image_id=" + thisfileid,
                        type: 'GET',
                        async: false,
                        cache: false,
                        timeout: 30000,
                        error: function () {
                            alert("error")
                        },
                        success: function (msg) {
                            $("#holder" + thisfileid).hide();
                        }
                    });
                }
            });

            $("#dialog-form").dialog({
                autoOpen: false,
                height: 200,
                width: 400,
                modal: true,
                buttons: {
                    "Rename": function () {
                        $.ajax({
                            url: "/processupload.php?action=rename&image_id=" + thisfileid + "&newname=" + $("#name").val(),
                            type: 'GET',
                            async: false,
                            cache: false,
                            timeout: 30000,
                            error: function () {
                                alert("error")
                            },
                            success: function (msg) {
                                var obj = $.parseJSON(msg);
                                if (obj.result == 1) {
                                    $("#name" + thisfileid).html($("#name").val());
                                    $("#dialog-form").dialog("close");
                                } else {
                                    $("#formerror").html(obj.message);
                                }
                            }
                        });
                    },
                    Cancel: function () {
                        $(this).dialog("close");
                    }
                },
                close: function () {
                }
            });
/*
            $('.demo').ajaxupload({
                url: 'https://api.ryankikta.com/upload.php?mm=<?php echo urlencode(base64_encode($current_user_name)); ?>&ms=<?php echo urlencode(base64_encode($current_user_id)); ?>',
                finish: function () {
                    location.reload();
                },
                allowExt: ['jpg', 'jpeg', 'png', 'pdf', 'tif', 'psd', 'dst'],
                autoStart: true
            });
		
*/	  
	   $("div#imageupload").dropzone({
		url: ajax_url + '?action=uploadimages',
		thumbnailMethod: 'contain',
		acceptedFiles: acceptedFileTypes,
		dictDefaultMessage: 'Drop Files Or Click Here To Upload',
		success: function (resp){
			console.log(resp);
		}
	   });

            $(".dropzone").on("click", function (event) {
		console.log(event);
                if (!$("#agree").is(':checked')) {
                    event.stopImmediatePropagation();
                    alert("You must agree to the Upload Rules.");
                }
            });
        })



        function setJumbo(fileID) {
            var jumbo = 0;
            if ($(".jumbo" + fileID).is(':checked')) {
                jumbo = 1;
            } else jumbo = 0;
            var r = confirm(" By selecting jumbo, you agree to an additional $2.25 charge for oversized printing. Files MUST be sized for printing and no larger than 14\"x18\". We do not increase the size of your file to meet the max size. Some product sizes won't allow for oversized printing and may be reduced if needed.");
            if (r == false) {
                if (jumbo == 1)
                    $(".jumbo" + fileID).prop('checked', false);
                else $(".jumbo" + fileID).prop('checked', true);
                return false;
            }
            $.ajax({
                type: "POST",
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: "action=set_jumbo&fileID=" + fileID + "&jumbo=" + jumbo,
                dataType: "json",
                success: function (response) {
                    if (response.status == 'failure') {
                        if (jumbo == 1)
                            $(".jumbo" + fileID).prop('checked', false);
                        else $(".jumbo" + fileID).prop('checked', true);
                        alert("Sorry, request failed");
                    }
                }
            });
        }
        //set no underbase image
        function setUnderbase(fileID) {
            var nounderbase = 0;
            if ($(".underbase" + fileID).is(':checked')) {
                nounderbase = 1;
            } else nounderbase = 0;
            var r = confirm("By selecting no underbase we will NOT print a white underbase on your designs. This means a few things 1. No white will print. 2.  It will be a much less vibrant print. The final result will vary by design, product and product color. Attempting this on dark garments is NOT RECOMMENDED. We DO NOT GUARANTEE the results if you select No Underbase or offer reprints due to any reported color and/or coverage issues. This is option is for advanced users and not used by the majority of our clients. We are unable to tell you how your design will turn out if you choose this setting, It is up to you to do your own tests.");
            if (r == false) {
                if (nounderbase == 1)
                    $(".underbase" + fileID).prop('checked', false);
                else $(".underbase" + fileID).prop('checked', true);
                return false;
            }
            $.ajax({
                type: "POST",
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: "action=set_nounderbase&fileID=" + fileID + "&nounderbase=" + nounderbase,
                dataType: "json",
                success: function (response) {
                    if (response.status == 'success') {
                        if (nounderbase == 1) {
                            $(".always_underbase" + fileID).attr('checked', false);
                        }
                    }
                    if (response.status == 'failure') {
                        if (jumbo == 1)
                            $(".underbase" + fileID).prop('checked', false);
                        else $(".underbase" + fileID).prop('checked', true);
                        alert("Sorry, request failed");
                    }
                }
            });
        }
        //set always use underbase
        function setAlwaysUnderbase(fileID) {
            var nounderbase = 0;
            if ($(".always_underbase" + fileID).is(':checked')) {
                alwaysunderbase = 1;
            } else alwaysunderbase = 0;
            var r = confirm("By selecting always underbase we will ALWAYS print a white underbase on this design. This means a few things 1. The white underbase will print on all shirt colors INCLUDING white shirts 2. you will be charged an extra fee for using the white underbase on this design");
            if (r == false) {
                if (alwaysunderbase == 1)
                    $(".always_underbase" + fileID).prop('checked', false);
                else $(".always_underbase" + fileID).prop('checked', true);
                return false;
            }
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: "action=set_alwaysunderbase&fileID=" + fileID + "&alwaysunderbase=" + alwaysunderbase,
                dataType: "json",
                success: function (response) {
                    if (response.status == 'success') {
                        if (alwaysunderbase == 1) {
                            $(".underbase" + fileID).attr('checked', false);
                        }
                    }
                    if (response.status == 'failure') {
                        if (alwaysunderbase == 1)
                            $(".always_underbase" + fileID).attr('checked', false);
                        else $(".always_underbase" + fileID).attr('checked', true);
                        alert("Sorry, request failed");
                    }
                }
            });
        }
    </script>
</head>
<body>

<div class="container-fluid">
    <h2>Select files</h2>
    <div id="agreec" class="input_checkbox mb-20">
        <input type="checkbox" id="agree" name="agree"> 
        <label for="agree">I agree to the <a href="<?php echo site_url(); ?>/upload-rules" target="_blank">Upload Rules</a></label>
        <p id="filetypes">Accepted File Types: </p>
    </div>

    <div id="dropZone">
    	<div id="imageupload" class="dropzone m-auto dz-clickable"></div>
    </div>
    <div id="user-images"></div>
    <div>
       <form class="py-40 text-center">
            <div class="input_outline d-inline-block">
                <input type="text" id="filter" name="q" placeholder="SEARCH">
            </div>
            <input class="btn-primary" type="submit" value="Search">
        </form>
    </div>
</div>

</body>
</html>
