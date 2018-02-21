<div class="panel panel-default">	
    <div class="panel-heading">
        <h3 class="panel-title"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Send a message</h3>
    </div>
    <div class="panel-body">
        <form style="margin-top: 15px;" id="msg_send_form" method="post" class="form-horizontal">
            <div class="form-group">
                <label for="recipient" class="col-sm-2 control-label">To</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="recipient" name="recipient" placeholder="Username" required value="<?= $_GET["recipient"] ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="subject" class="col-sm-2 control-label">Subject</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
                </div>
            </div>
            <div class="form-group">
                <label for="message" class="col-sm-2 control-label">Message</label>
                <div class="col-sm-10">
                    <textarea class="form-control" rows="10" id="message" name="message" placeholder="message" required></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button id="send_button" type="submit" class="btn btn-default">Send message</button>
                </div>
            </div>
        </form>	
    </div>
</div>	
