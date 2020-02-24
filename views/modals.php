<?php
/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 2:28 PM
 */ ?>
<!--<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>-->

<div id="confirmation" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default modal-close" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="continue">Continue</button>
            </div>
        </div>
    </div>
</div>

<div id="result" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Report</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="result-modal-body"></div>
        </div>

    </div>
</div>
