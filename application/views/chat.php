<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="option">
    <div url="<?PHP echo $link_profil ?>" class="float-left pad-t-10 hover font-b menu-profil cur-p" data-tip="Info untuk : <?PHP echo ucwords($_friend['name']) ?>">
        <strong><?PHP echo $_friend['name'] ?></strong>
    </div>
    <div class="button hover show-alert" data-category="del-all-msg" alert-type="error" alert-title="Menghapus Semua Pesan!" alert-msg="Apakah Anda yakin ingin menghapus semua pesan tersebut?" url="<?PHP echo $delete ?>" data-tip="Delete Message">
        <img src="<?PHP echo $i_delete ?>" alt="asdf;ljhko">
    </div>
</div>
<div class="inner-chat">
    <div class="chat-wrap">
        <?PHP 
        foreach($chat as $chat):
            $isSelf         = ($chat->_from == $_friend['id'])  ? ' _friend' : ' _self';
            $right_position = ($chat->_to == $_self['id'])  ? ' right' : '';
            $isDelete       = ($chat->delete_by != 0) ? TRUE : FALSE;
            $isPhoto        = ($chat->photo != NULL) ? TRUE : FALSE;
            //jika pesan telah dihapus semua oleh salah satu atau dua user
            if(($chat->delete_all_by != $_self['id']) && ($chat->delete_by != $_self['id'])):
            ?>
                <div class="contact<?PHP echo $right_position ?>"  data-id="<?PHP echo $chat->id ?>">
                    <div class="d-100">
                        <div class="photo">
                            <img src="<?PHP echo $photo_null ?>" alt="alsdojkhf">
                        </div>
                        <div class="msg<?PHP echo $isSelf ?>">
                            <?PHP if($chat->status_reply != 0): // if reply field value is not 0 ?>
                                <div class="reply-msg" data-reply="<?PHP echo $chat->status_reply ?>">
                                    <div class="img">
                                        <img src="<?PHP echo photo_chat($chat->reply_img) ?>" alt="">
                                    </div>
                                    <pre><?PHP echo $chat->reply_msg ?></pre>
                                </div>
                            <?PHP endif; ?>
                            <?PHP if($isPhoto && !$isDelete): //jika ada photo yang di insert dan status_delete != terhapus ?>
                                <div class="_img">
                                    <img class="view-photo" src="<?PHP echo photo_chat($chat->photo) ?>" alt="Yow">
                                </div>
                            <?PHP endif; ?>
                            <div class="button-msg<?PHP echo $isSelf ?>"></div>
                            <pre <?PHP echo $isDelete ? " style='font-style:italic'" : "" ?>><?PHP echo ($isDelete) ? "Pesan telah dihapus." : $chat->text ?></pre>
                        </div>
                    </div>
                </div>
            
        <?PHP 
            endif;
        endforeach; 
        ?>
    </div>
</div>
<?PHP echo $form ?>
    <div class="reply">
        <div class="close">X</div>
        <div class="inner-reply">
            <div class="img">
                <img src="" alt="">
            </div>
            <div class="text">
                <pre></pre>
            </div>
        </div>
    </div>
    <div class="form-send-inner">
        <input id="_from"  name="_from"  value="<?PHP echo $_self['id'] ?>"   hidden="">
        <input id="_to"    name="_to"    value="<?PHP echo $_friend['id'] ?>" hidden="">
        <input id="_reply" name="_reply" value=""                             hidden="">
        <input id="_photo" name="_photo" accept="image/x-png,image/gif,image/jpeg" type="file" style="display:none" hidden="">
        <textarea class="send-input" name="text-message" placeholder="Type your message..."></textarea>
        <div class="button button-send _send hover" data-tip="Send">
            <img src="<?PHP echo $i_send ?>" alt="dsafsadfasd">
        </div>
        <div class="button button-photo _send hover" data-tip="Add Photo">
            <img src="<?PHP echo $i_photo ?>" alt="asdfadsfsad">
        </div>
    </div>
</form>