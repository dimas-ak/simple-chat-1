<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-chat">
	<div class="wrap">
	    <div class="d-100 text-center h-100">
            <div class="d-50 float-none dis-ib ver-mid bg-trans2-black">
                <div class="menu-tab text-left">
                    <div class="btn-tab aktif" for="form-login">
                        <span>LOGIN</span>
                    </div><!--
                    --><div class="btn-tab" for="form-daftar">
                        <span>DAFTAR</span>
                    </div>
                </div>

                <!-- -=[FORM LOGIN]=- -->
                <div class="pad-10 form-login form">
                    <?PHP echo $login ?>
                        <div class="pad-10 _for-title">
                            <strong>FORM LOGIN</strong>
                            <div class="" id="msg-login"></div>
                        </div>
                        <div class="pad-t-30">
                            <div id="login-name" class="msg-info"></div>
                            <div class="f-input t-1">
                                <input type="text" name="_name" msg="Username">
                                <span>Username</span>
                            </div>
                        </div>
                        <div class="pad-t-30">
                            <div id="login-pass" class="msg-info"></div>
                            <div class="f-input t-1">
                                <input type="password" name="_pass" msg="Password">
                                <span>Password</span>
                            </div>
                        </div>
                        <div class="pad-t-30 of-hid pad-b-30">
                            <button class="btn green float-left">SIGN IN</button>
                            <div class="btn red float-right" onclick="reset('.form-login')">
                                <span>RESET</span>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- -=[/FORM LOGIN]=- -->

                
                <!-- -=[FORM DAFTAR]=- -->
                <div class="pad-10 form-daftar dis-none form">
                    <?PHP echo $daftar ?>
                        <div class="pad-10 _for-title">
                            <strong>FORM LOGIN</strong>
                            <div class="" id="msg-daftar"></div>
                        </div>
                        <div class="pad-t-30">
                            <div id="daftar-name" class="msg-info"></div>
                            <div class="f-input t-1">
                                <input type="text" name="_name" msg="Nama">
                                <span>Nama</span>
                            </div>
                        </div>
                        <div class="pad-t-30">
                            <div id="daftar-uname" class="msg-info"></div>
                            <div class="f-input t-1">
                                <input type="text" name="_uname" msg="Username">
                                <span>Username</span>
                            </div>
                        </div>
                        <div class="pad-t-30">
                            <div id="daftar-pass" class="msg-info"></div>
                            <div class="f-input t-1">
                                <input type="password" name="_pass" msg="Password">
                                <span>Password</span>
                            </div>
                        </div>
                        <div class="pad-t-30">
                            <div id="daftar-repass" class="msg-info"></div>
                            <div class="f-input t-1">
                                <input type="password" name="_repass" msg="Re-Password">
                                <span>Re-Password</span>
                            </div>
                        </div>
                        <div class="pad-t-30 of-hid pad-b-30 text-center">
                            <button class="btn green">SIGN IN</button>
                        </div>
                    </form>
                </div>
                <!-- -=[/FORM DAFTAR]=- -->

            </div>
        </div>
    </div>
</div>