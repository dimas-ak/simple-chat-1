<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?PHP 
    foreach($user as $u): 
        if((userdata('_admin') && $u->id != decrypt(userdata('_admin'))) || (userdata('_user') && $u->id != decrypt(userdata('_user')))):
    ?>
        <a name-user="<?PHP echo $u->name ?>" href="<?PHP echo $link . $u->id ?>" class="show-msg" for="<?PHP echo $u->id ?>" user="<?PHP echo $id ?>">
            <div class="contact _u left" >
                <div class="photo">
                    <img src="<?PHP echo $u->photo != null ?$u->photo : $photo_null ?>" alt="Yow">
                </div>
                <div class="user">
                    <div class="name">
                        <span><?PHP echo $u->name ?></span>
                    </div>
                    <div class="msg-new">
                        <span></span>
                    </div>
                </div>
            </div>
        </a>
<?PHP 
        endif; 
    endforeach; 
?>