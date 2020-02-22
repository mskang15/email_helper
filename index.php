<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="css/main.css">
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script src="js/main.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
            <a class="navbar-brand" href="#">Zion Email Client</a>
        </nav>

        <main role="main" class="container" style="margin-top: 30px;">
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

            <?php include_once 'views/modals.php'; ?>
        </main>
    </body>
</html>
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