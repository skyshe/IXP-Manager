<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    Patch Panel Port
    <?php if($t->patchPanel): ?>
        - <?= $t->patchPanel->getName() ?>
    <?php endif;?>
<?php $this->append() ?>

<?php $this->section('page-header-preamble') ?>

<?php $this->append() ?>


<?php $this->section('content') ?>
    <?php if($t->patchPanel): ?>
        <div class="">
            <h2>
                Ports for <?= $t->patchPanel->getName() ?>
                <?php if( $t->patchPanel->getColoReference() != $t->patchPanel->getName() ): ?>
                    (Colo Reference: <?= $t->patchPanel->getColoReference() ?>)
                <?php endif; ?>
            </h2>
        </div>
    <?php endif;?>
    <?php if(session()->has('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= session()->get('success') ?>
        </div>
    <?php endif; ?>
    <?php if(session()->has('error')): ?>
        <div class="alert alert-danger" role="alert">
            <b>Error : </b><?= session()->get('error') ?>
        </div>
    <?php endif; ?>

    <table id='patch-panel-port-list' class="table ">
        <thead>
            <tr>
                <td>Id</td>
                <td>Name</td>
                <?php if(!$t->patchPanel): ?>
                    <td>Patch Panel</td>
                <?php endif;?>
                <td>Switch / Port</td>
                <td>Customer</td>
                <td>Colocation circuit ref</td>
                <td>Ticket Ref</td>
                <td>Assigned at</td>
                <td>State</td>
                <td>Action</td>
            </tr>
        <thead>
        <tbody>
            <?php foreach( $t->patchPanelPorts as $patchPanelPort ): ?>
                <tr>
                    <td>
                        <?= $patchPanelPort->getId() ?>
                    </td>
                    <td>
                        <?= $patchPanelPort->getName() ?>
                    </td>
                    <?php if(!$t->patchPanel): ?>
                        <td>
                            <a href="<?= url('patch-panel/view' ).'/'.$patchPanelPort->getPatchPanel()->getId()?>">
                                <?= $patchPanelPort->getPatchPanel()->getName() ?>
                            </a>
                        </td>
                    <?php endif;?>
                    <td>
                        <?= $patchPanelPort->getSwitchName() ?>
                    <?php if( $patchPanelPort->getSwitchPortName() ): ?>
                            &nbsp;::&nbsp;<?= $patchPanelPort->getSwitchPortName() ?>
                    <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= url('customer/overview/id/' ).'/'.$patchPanelPort->getCustomerId()?>">
                            <?= $patchPanelPort->getCustomerName() ?>
                        </a>
                    </td>
                    <td>
                        <?= $patchPanelPort->getColoCircuitRef() ?>
                    </td>
                    <td>
                        <?= $patchPanelPort->getTicketRef() ?>
                    </td>
                    <td>
                        <?= $patchPanelPort->getAssignedAtFormated() ?>
                    </td>
                    <td>
                        <span title="" class="label label-<?= $patchPanelPort->getStateCssClass() ?>">
                            <?= $patchPanelPort->resolveStates() ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu">
                                    <input type="hidden"  id="notes_<?=$patchPanelPort->getId() ?>" value="<?=$patchPanelPort->getNotes() ?>">
                                    <input type="hidden"  id="private_notes_<?=$patchPanelPort->getId() ?>" value="<?=$patchPanelPort->getPrivateNotes() ?>">
                                    <input type="hidden"  id="pi_state_<?=$patchPanelPort->getId() ?>" label="<?=$patchPanelPort->getPhysicalInterfaceStateLabel()?>" value="<?=$patchPanelPort->getPhysicalInterfaceState() ?>">
                                    <?php if($patchPanelPort->getState() == \Entities\PatchPanelPort::STATE_AVAILABLE): ?>
                                        <li><a href="<?= url('/patch-panel-port/edit' ).'/'.$patchPanelPort->getId().'/allocated'?>">Allocate</a></li>
                                    <?php endif; ?>
                                    <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,false,false,true)" href="<?= url('/patch-panel-port/changeStatus' ).'/'.$patchPanelPort->getId()?>">Add note</a></li>
                                    <?php if($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT): ?>
                                        <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,true,<?= $patchPanelPort->getHasSwitchPort() ?>,false)" href="<?= url('/patch-panel-port/changeStatus' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_CONNECTED?>">Set Connected</a></li>
                                    <?php endif; ?>
                                    <?php if(($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED)): ?>
                                        <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,false,false)" id="ceasedRequested<?=$patchPanelPort->getId()?>,false" href="<?= url('/patch-panel-port/changeStatus' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_AWAITING_CEASE?>">Cease requested</a></li>
                                    <?php endif; ?>
                                    <?php if(($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_CEASE)): ?>
                                        <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,false,false,false)" href="<?= url('/patch-panel-port/changeStatus' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_CEASED?>">Set ceased</a></li>
                                    <?php endif; ?>
                                    <?php if($patchPanelPort->getCustomer()): ?>
                                        <li><a href="<?= url('/patch-panel-port/email' ).'/'.$patchPanelPort->getId().'/'.\Entities\PatchPanelPort::EMAIL_CONNECT?>">Email - Connect</a></li>
                                        <li><a href="<?= url('/patch-panel-port/email' ).'/'.$patchPanelPort->getId().'/'.\Entities\PatchPanelPort::EMAIL_CEASE?>">Email - Cease</a></li>
                                        <li><a href="<?= url('/patch-panel-port/email' ).'/'.$patchPanelPort->getId().'/'.\Entities\PatchPanelPort::EMAIL_INFO?>">Email - Information</a></li>
                                    <?php endif; ?>
                                    <?php if(($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED)): ?>
                                        <li><a target="_blank" href="<?= url('/patch-panel-port/sendLoaPDF' ).'/'.$patchPanelPort->getId()?>">Download Loa PDF</a></li>
                                    <?php endif; ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a onclick="return uploadPopup(<?= $patchPanelPort->getId() ?>)" href="#" title="Attach file">Attach file...</i></a>
                                    </li>
                                    <li>
                                        <a href="<?= url('/patch-panel-port/view' ).'/'.$patchPanelPort->getId()?>" title="Preview">View</i></a>
                                    </li>
                                    <li>
                                        <a href="<?= url('/patch-panel-port/edit' ).'/'.$patchPanelPort->getId()?>" title="Edit">Edit</a>
                                    </li>
                                </ul>
                            </div>
                            <a class="btn btn btn-default <?php if($patchPanelPort->getHistoryCount() == 0): ?> disabled <?php endif; ?>" title="History" <?php if($patchPanelPort->getHistoryCount() != 0): ?> href="<?= url('/patch-panel-port/history' ).'/'.$patchPanelPort->getId()?> <?php endif; ?> ">
                                <i class="glyphicon glyphicon-folder-open"></i>
                            </a>
                        </div>
                    </td>
                </tr>

            <?php endforeach;?>
        <tbody>
    </table>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <script>
        $(document).ready(function(){

            window.loadscript = false;

            pagination = true;
            <?php if($t->patchPanel): ?>
                pagination = false;
            <?php endif; ?>

            $('#patch-panel-port-list').DataTable( {
                "paging":   pagination,
                "autoWidth": false,
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": false,
                        "searchable": false,
                    }
                ],
                "order": [[ 0, "asc" ]]
            } );

        });

        function setNotesTextArea(pppId,input){
            val_textarea = $('#'+input).text();
            default_val = '* <?= date("Y-m-d" ).' ['.$t->user->getUsername().']: '?>';

            if(val_textarea == ''){
                $('#'+input).text(default_val);
            }
            else{
                if($('#'+input).text() != default_val){
                    if(input == 'notes'){
                        if(!window.new_notes_set){
                            $('#'+input).text(default_val+'\n\n'+val_textarea);
                            window.new_notes_set = true;
                        }
                    }
                    else{
                        if(!window.new_private_notes_set){
                            $('#'+input).text(default_val+'\n\n'+val_textarea);
                            window.new_private_notes_set = true;
                        }
                    }

                }
            }
            pos = default_val.length + ($('#'+input).val().length - $('#'+input).text().length);
            $('#'+input).setCursorPosition(pos);

        }

        function checkTextArea(pppId,input){
            if($('#'+input).text() == $('#'+input).val()){
                $('#'+input).text($('#'+input+'_'+pppId).val());
                if(input == 'notes'){
                    window.new_notes_set = false;
                }
                else{
                    window.new_private_notes_set = false;
                }

            }
        }

        function popup(href,pppId,connected,hasSwitchPort,only_note){
            var url = $(href).attr("href");
            var new_notes_set = false;

            if(only_note){
                html = "<p>Add notes</p><br/>";
            }
            else{
                html = "<p>Consider adding details to the notes such as a internal ticket reference to the cease request / whom you have been dealing with / expected cease date / etc..</p><br/>";
            }

            html +=    "Public Notes : <textarea id='notes' onblur='checkTextArea("+pppId+",\"notes\")' onfocus='setNotesTextArea("+pppId+",\"notes\")' onclick='setNotesTextArea("+pppId+",\"notes\")' rows='8' class='bootbox-input bootbox-input-textarea form-control' name='note' >"+$('#notes_'+pppId).val()+"</textarea>" +
                "<br/>" +
                "Private Notes : <textarea id='private_notes' onblur='checkTextArea("+pppId+",\"private_notes\")' onfocus='setNotesTextArea("+pppId+",\"private_notes\")' onclick='setNotesTextArea("+pppId+",\"private_notes\")' rows='8' class='bootbox-input bootbox-input-textarea form-control' name='note' >"+$('#private_notes_'+pppId).val()+"</textarea>";
            if(connected){
                if(hasSwitchPort){
                    html += "<br/><br/><span>Update Physical Port State To:  </span><select id='PIStatus'>";

                    <?php foreach ($t->physicalInterfaceLimited as $index => $state): ?>
                        piIndex = <?= $index?>;
                        currentState = "";
                        if(piIndex == $('#pi_state_'+pppId).val()){
                            currentState = "(current state)";
                        }
                        html += "<option <?php if($index == \Entities\PhysicalInterface::STATUS_QUARANTINE):?> selected <?php endif;?> value='<?= $index ?>'><?= $state?> "+currentState+"</option>";
                    <?php endforeach ;?>
                    if(currentState == ''){
                        html += "<option value='"+$('#pi_state_'+pppId).val()+"'>"+$('#pi_state_'+pppId).attr('label')+" (current state)</option>";
                    }
                    html += "</select>";
                }

            }

            var dialog = bootbox.dialog({
                message: html,
                title: "Note",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;

                        }
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Confirm',
                        callback: function () {
                            notes = $('#notes').val();
                            private_notes = $('#private_notes').val();
                            if(hasSwitchPort){
                                pi_status = $('#PIStatus').val();
                            }
                            else{
                                pi_status = null;
                            }

                            $.ajax({
                                url: "<?= url('patch-panel-port/setNotes/')?>",
                                data: {pppId:pppId,notes: notes,private_notes:private_notes,pi_status:pi_status,only_note:only_note},
                                type: 'GET',
                                dataType: 'JSON',
                                success: function (data) {
                                    if(data.success){
                                        if(only_note){
                                            $('.bootbox.modal').modal('hide');
                                            location.reload();
                                        }
                                        else{
                                            document.location.href = url;
                                        }

                                        return true;
                                    }
                                    else{
                                        $('.bootbox.modal').modal('hide');
                                        return false;
                                    }
                                }
                            });


                        }
                    }
                }

            });

            dialog.init(function(){
                window.new_notes_set = false;
                window.new_private_notes_set = false;
            });
            return false;
        }



        function uploadPopup(pppId){
            html = "<form id='upload' method='post' action='<?= url('/patch-panel-port/uploadFile' )?>/"+pppId+"' enctype='multipart/form-data'> <div id='drop'>Drop Files Here &nbsp;<a class='btn btn-success'><i class='glyphicon glyphicon-upload'></i> Browse</a> <br/><span class='info'> (max size 50MB) </span><input type='file' name='upl' multiple /> </div> <ul><!-- The file uploads will be shown here --> </ul><input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'> </form>";

            var dialog = bootbox.dialog({
                message: html,
                title: "Files Upload (Files will be public by default)",
                onEscape: function() {
                    location.reload();
                },
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Close',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            location.reload();
                            return false;
                        }
                    },
                }

            });
            dialog.bind('shown.bs.modal', function(){

            });

            dialog.init(function(){

                    $.getScript( "js/draganddrop/jquery.fileupload.js", function( data, textStatus, jqxhr ) {});
                    $.getScript( "js/draganddrop/jquery.iframe-transport.js", function( data, textStatus, jqxhr ) {});
                    $.getScript( "js/draganddrop/jquery.knob.js", function( data, textStatus, jqxhr ) {});
                    $.getScript( "js/draganddrop/jquery.ui.widget.js", function( data, textStatus, jqxhr ) {});
                    $.getScript( "js/draganddrop/script.js", function( data, textStatus, jqxhr ) {});
                    window.loadscript = true;


            });

            return false;
        }

        function deleteFile(idFile,idPPP){
            $.ajax({
                url: "<?= url('patch-panel-port/deleteFile/')?>",
                data: {idFile: idFile, idPPP: idPPP},
                type: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    if(data.success){
                        $('#file_'+idFile).fadeOut( "medium", function() {
                            $('#file_'+idFile).remove();
                        });
                    }
                    else{
                        $('#message_'+idFile).removeClass('success').addClass('error').html('Delete error : '+data.message);
                        $('#delete_'+idFile).remove();
                    }
                }

            });
        }

        function changePrivateFile(idFile,idPPP){
            $.ajax({
                url: "<?= url('patch-panel-port/changePrivateFile/')?>",
                data: {idFile: idFile, idPPP: idPPP},
                type: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    if(data.success){
                        $('#privateMessage_'+idFile).html(' / <i class="success">'+data.message+'</i>');
                        if($('#private_'+idFile).hasClass('fa-lock')){
                            $('#private_'+idFile).removeClass('fa-lock');
                            $('#private_'+idFile).addClass('fa-unlock');
                        }
                        else{
                            $('#private_'+idFile).removeClass('fa-unlock');
                            $('#private_'+idFile).addClass('fa-lock');
                        }

                    }
                    else{
                        $('#privateMessage_'+idFile).html(' / <i class="error"> '+data.message+'</i>');
                        $('#private_'+idFile).remove();
                    }

                }

            });
        }

    </script>
<?php $this->append() ?>