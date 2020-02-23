<?php
/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/23/20
 * Time: 8:41 AM
 */ ?>

<div class="container" style="height: 200px;">
    <form id="email_form">
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" placeholder="Write Subject" required>
        </div>
        <div class="form-group">
            <label for="from">From</label>
            <input type="text" class="form-control" id="from" name="from" placeholder="Write Sender's name" required>
        </div>
        <div class="form-group">
            <label for="from_email">From Email</label>
            <input type="email" class="form-control" id="from_email" name="from_email" placeholder="Write Sender's email" required>
        </div>
        <div class="form-group" style="float:left;">
            <label for="recipients">Recipients</label>
            <input id="recipients" type="file" accept="text/csv" class="form-control-file" name="recipients" required>
            <small class="form-text text-muted">Upload your csv file here</small>
        </div>
        <div style="clear:both;"></div>
        <div class="form-group">
            <div id="editor"></div>
        </div>
        <div class="form-group" style="float:left;">
            <label for="attachment">Attachment</label>
            <input id="attachment" type="file" class="form-control-file" name="attachment" >
            <small class="form-text text-muted">Upload your attachment file here</small>
        </div>
        <div style="clear:both;"></div>
        <div class="form-group" style="float:left;">
            <div class="alert alert-danger" role="alert" style="display:none;"></div>
            <div class="alert alert-success" role="alert" style="display:none;"></div>
        </div>
        <div class="form-group" style="float:right;">
            <input class="btn btn-primary" type="submit" value="Send" name="email_send">
        </div>
        <div style="clear:both;"></div>
    </form>
</div>

<script>
    var toolbarOptions = [['bold', 'italic', 'underline'], ['link']];
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Write here...',
        modules: {
            toolbar: toolbarOptions
        }
    });

    $(function(){
        $(".alert").click(function(){
            $(this).slideUp();
        });

        $("#email_form").submit(function(e){
            e.preventDefault();
            $(".alert").hide();

            var fd = new FormData();
            $("#email_form input").each(function(index, el){
                if($(el).attr("type") !== "submit"){
                    if($(el).attr("type") === "file"){
                        if(el.files.length > 0){
                            fd.append($(el).attr("name"), el.files[0]);
                        }
                    } else {
                        if(typeof $(el).attr("name") !== "undefined" && $(el).val() !== ""){
                            fd.append($(el).attr("name"), $(el).val());
                        }
                    }
                }
            });
            $("#editor").val(JSON.stringify(quill.getContents()));
            fd.append("content", $("#editor").val());

            $.ajax({
                url: "./process_form.php",
                enctype: 'multipart/form-data',
                method: 'POST',
                processData: false,  // Important!
                contentType: false,
                cache: false,
                data: fd,
                success:function(){

                },
                error: function(res) {
                    var message = getErrorMessageForAlert(res);
                    $(".alert-danger").text(message).slideDown();
                }
            });
        });
    });
</script>