<?php

$app_name = config('app.name');

$req = "project_";
$pay = "payment_";
$off = "offer_";
$mil = "milestone_";
$auth = "auth_";

return [
    "welcome" => "welcome to {$app_name} :name",
    'hello' => 'Hello :name!',
    'thank_you' => 'Thank you for using our platform!',
    "id_verification_approved" => "Your ID Verification request has been approved",
    "id_verification_declined" => "Your ID Verification request has been declined",
    "contact_support" => "Contact Support",
    "message_received" => "You have received new message from :sender",
    "designer_rate" => "Your work in your completed project :project_title has been rated by :client",
    "client_rate" => ":designer rated you",
    "support_reply" => "New message regarding your ticket :ticket_title",

    // Requests
    "{$req}approved" => "Your Project request has been approved",
    "{$req}declined" => "Your Project request has been declined",
    "{$req}sent_for_approval" => "Your Project request has been sent for approval",

    // payment
    "{$pay}disposal" => "Your payment disposal has been completed",
    "{$pay}linked" => "Your payment method :method has been linked",
    "{$pay}received" => "You have received a payment from :user",
    "{$pay}withdraw" => "Your withdrawal request has been completed",

    // offer
    "{$off}accepted" => "Your offer for :project_title has been accepted",
    "{$off}declined" => "Your offer for :project_title has been declined",

    // milestone
    "{$mil}approved" => "Your milestone for :project_title has been approved by :client",
    "{$mil}declined" => "Your milestone for :project_title has been declined by :client",
    "{$mil}submitted" => "Your milestone has been submitted",
];
