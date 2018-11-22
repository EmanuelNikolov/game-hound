<?php

namespace App\FlashMessage;


abstract class UserMessage
{
    
    public const SIGNUP_SUCCESS = "Yay! Your account was created, a verification link was sent to your email <span class='text-nowrap'>(つ ♡ ͜ʖ ♡)つ</span>";

    public const INVALID_TOKEN = "Invalid token <span class='text-nowrap'>¯\_( ͡° ͜ʖ ͡°)_/¯</span>";

    public const EMAIL_CONFIRM_USER_DIFF = "You must be logged into your account to confirm your email <span class='text-nowrap'>( ͡ಠ ʖ̯ ͡ಠ)</span>";

    public const EMAIL_CONFIRM_SUCCESS = "Your email has been verified successfully! Enjoy Game Hound <span class='text-nowrap'>( ͡°з ͡°)</span>";

    public const RESET_PASSWORD_SUCCESS = "You have successfully changed your password. Welcome back! <span class='text-nowrap'>( ͡°з ͡°)</span>";

    public const RESET_PASSWORD_REQUEST_FAIL = "Sorry, we couldn't find a user account with those credentials <span class='text-nowrap'>┐( ͡° ʖ̯ ͡°)┌</span>";

    public const RESET_PASSWORD_REQUEST_SUCCESS = "Wooho! A password reset link was sent to your email! <span class='text-nowrap'>ヽ༼ຈل͜ຈ༽ﾉ</span>";

    public const RESET_PASSWORD_FAIL = "You have to verify your email before you can reset your password! <span class='text-nowrap'>( ͡ಠ ʖ̯ ͡ಠ)</span>";

    public const EMAIL_CHANGE_REQUESTED = "A verification link was sent to your email <span class='text-nowrap'>(つ ♡ ͜ʖ ♡)つ</span>";

    public const PASSWORD_CHANGED = "Your password was successfully updated! <span class='text-nowrap'>ヽ༼ຈل͜ຈ༽ﾉ</span>";
}