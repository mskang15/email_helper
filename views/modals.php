<?php
/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 2:28 PM
 */ ?>
<!--<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>-->

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
    $(function(){
//        $('.modal').modal({
//            keyboard: false
//        });

        $('#myModal').on('shown.bs.modal', function () {
            $('#myInput').trigger('focus')
        })
    });
</script>