<div class="settings-account menu-settings-account">
    <div class="inner-settings">
        <div class="pad-10">
            <button class="btn blue menu-settings">BACK</button>
        </div>
        <div class="contact">
            <div class="photo">
                <img class="view-photo" src="<?PHP echo $profil['photo'] != NULL ? $profil['photo'] : $photo_null ?>" alt="">
            </div>
            <div class="user">
                <div class="name">
                    <span><?PHP echo $profil['name'] ?></span>
                </div>
            </div>
        </div>
        <div class="pad-10">
            <div class="pad-t-30">
                <div class="f-input t-1 black">
                    <input type="text" value="<?PHP echo $profil['username'] ?>">
                    <span>Username</span>
                </div>
            </div>
            <div class="pad-t-30">
                <div class="f-input t-1 black">
                    <input type="text" value="<?PHP echo $profil['name'] ?>">
                    <span>Name</span>
                </div>
            </div>
            <div class="pad-t-30">
                <div class="f-input t-1 black">
                    <input type="password" value="">
                    <span>Old Password</span>
                </div>
            </div>
            <div class="pad-t-30">
                <div class="f-input t-1 black">
                    <input type="password" value="">
                    <span>New Password</span>
                </div>
            </div>
            <div class="pad-t-30">
                <div class="f-input t-1 black">
                    <input type="password" value="">
                    <span>New Re-Password</span>
                </div>
            </div>
            <div class="pad-t-30 text-center">
                <button class="btn green">SAVE</button>
            </div>
        </div>
    </div>
</div>