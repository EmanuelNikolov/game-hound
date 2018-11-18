<?php

namespace App\FlashMessage;


abstract class UserMessage
{
    
    public const REGISTRATION_CONFIRMED = "Yay! Your account was created, a verification link was sent to your email (つ ♡ ͜ʖ ♡)つ";

    public const INVALID_TOKEN = "Invalid token ¯\_( ͡° ͜ʖ ͡°)_/¯";

    public const EMAIL_CONFIRM_USER_DIFF = "You must be logged into your account to confirm your email ( ͡ಠ ʖ̯ ͡ಠ)";

    public const REGISTRATION_SUCCESS = "Your email has been verified successfully! Enjoy Game Hound ( ͡°з ͡°)";

    public const RESET_PASSWORD_SUCCESS = "You have successfully changed your password. Welcome back! ( ͡°з ͡°)";

    public const RESET_PASSWORD_REQUEST_FAIL = "Sorry, we couldn't find a user account with those credentials ┐( ͡° ʖ̯ ͡°)┌";

    public const RESET_PASSWORD_REQUEST_SUCCESS = "Wooho! A password reset link was sent to your email! ヽ༼ຈل͜ຈ༽ﾉ";

    public const RESET_PASSWORD_FAIL = "You have to verify your email before you can reset your password! ( ͡ಠ ʖ̯ ͡ಠ)";

    public const EMAIL_CHANGE_REQUESTED = "A verification link was sent to your email (つ ♡ ͜ʖ ♡)つ";

    public const PASSWORD_CHANGED = "Your password was successfully updated! ヽ༼ຈل͜ຈ༽ﾉ";
}